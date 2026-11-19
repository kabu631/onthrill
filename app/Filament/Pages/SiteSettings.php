<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\SavesSettings;
use App\Filament\Support\Fields;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Business settings: branding, contact details, prices and payments, email, SEO.
 * Texts shown on the website's pages are under Page Content.
 */
class SiteSettings extends Page
{
    use SavesSettings;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Site Settings';

    public static function canAccess(): bool
    {
        return auth()->user()?->allows('settings.update') ?? false;
    }

    protected function hiddenKeys(): array
    {
        return ['mail_password'];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testEmail')
                ->label('Send test email')
                ->icon(Heroicon::OutlinedEnvelope)
                ->color('gray')
                ->modalDescription('Uses the saved email settings. Save any changes first.')
                ->schema([
                    TextInput::make('to')->label('Send to')->email()->required()->default(fn () => auth()->user()->email),
                ])
                ->action(function (array $data) {
                    try {
                        Mail::raw(
                            'This is a test email from '.setting('site_name').'. If you can read this, the website can send booking and enquiry emails.',
                            fn ($message) => $message->to($data['to'])->subject('Test email from '.setting('site_name')),
                        );
                    } catch (Throwable $e) {
                        Notification::make()->title('Email could not be sent')->body($e->getMessage())->danger()->persistent()->send();

                        return;
                    }

                    config('mail.default') === 'log'
                        ? Notification::make()->title('No mail server set up yet')
                            ->body('The message was written to the log file instead of being sent. Fill in the Email tab and save.')->warning()->send()
                        : Notification::make()->title('Test email sent')->body("Check the inbox of {$data['to']}.")->success()->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('settings')->persistTabInQueryString()->tabs([
                    $this->generalTab(),
                    $this->contactTab(),
                    $this->paymentsTab(),
                    $this->emailTab(),
                    $this->seoTab(),
                ]),
            ]);
    }

    private function generalTab(): Tab
    {
        return Tab::make('General')->icon(Heroicon::OutlinedGlobeAlt)->schema([
            Grid::make(2)->schema([
                TextInput::make('site_name')->required()->maxLength(80),
                TextInput::make('tagline')->maxLength(80)->helperText('Shown under the name when no logo is uploaded.'),
            ]),
            Grid::make(2)->schema([
                Fields::settingImage('logo', 'Logo (header)')
                    ->helperText('Dark lettering, transparent background (PNG, SVG or WebP). Leave empty to show the site name.'),
                Fields::settingImage('logo_footer', 'Logo (footer)')
                    ->helperText('The footer is dark: use a version with light lettering and a transparent background.'),
                Fields::settingImage('page_banner', 'Page banner background')
                    ->helperText('Behind the title of inner pages that have no photo of their own. Wide image, about 1900 × 630 px.'),
                Fields::settingImage('footer_background', 'Footer background')
                    ->helperText('Shown darkened behind the footer. Leave empty for plain dark brown.'),
            ]),
            Textarea::make('footer_about')->label('Footer text')->rows(2)->maxLength(400),
        ]);
    }

    private function contactTab(): Tab
    {
        return Tab::make('Contact & Social')->icon(Heroicon::OutlinedPhone)->schema([
            Section::make()->compact()->schema([])
                ->description('Empty lines are left out of the website, so fill in only what customers should use.'),
            Grid::make(2)->schema([
                TextInput::make('phone')->tel()->maxLength(30)->helperText('With country code, e.g. +977 98XXXXXXXX or +91 98XXXXXXXX.'),
                TextInput::make('whatsapp')->label('WhatsApp number')->tel()->maxLength(30)
                    ->helperText('Leave empty if it is the same as the phone number.'),
                TextInput::make('email')->email()->required()->maxLength(160),
                TextInput::make('business_hours')->placeholder('Sunday to Friday, 9 am to 6 pm')->maxLength(120),
            ]),
            Textarea::make('address')->label('Office address')->rows(2)->maxLength(300),
            Grid::make(2)->schema([
                TextInput::make('map_location')->label('Place shown on the contact page map')->maxLength(200)->placeholder('Thamel, Kathmandu, Nepal')
                    ->helperText('Used when there is no office address and no embed link below.'),
                TextInput::make('map_embed_url')->label('Google Maps embed link (optional)')->url()->maxLength(1000)
                    ->helperText('Google Maps > Share > Embed a map > copy the src="…" link, to pin your exact office.'),
            ]),
            Section::make('Social links')->description('Paste the full address of your page. Leave empty to hide an icon.')->columns(2)->schema([
                TextInput::make('facebook')->url()->placeholder('https://www.facebook.com/yourpage')->prefixIcon(Heroicon::OutlinedLink),
                TextInput::make('instagram')->url()->placeholder('https://www.instagram.com/yourpage')->prefixIcon(Heroicon::OutlinedLink),
                TextInput::make('youtube')->url()->placeholder('https://www.youtube.com/@yourchannel')->prefixIcon(Heroicon::OutlinedLink),
                TextInput::make('twitter')->label('X / Twitter')->url()->placeholder('https://x.com/yourhandle')->prefixIcon(Heroicon::OutlinedLink),
            ]),
        ]);
    }

    private function paymentsTab(): Tab
    {
        return Tab::make('Prices & Payments')->icon(Heroicon::OutlinedBanknotes)->schema([
            Section::make('Prices')->columns(3)->schema([
                Select::make('default_currency')->label('Show prices in')->required()->native(false)
                    ->options(['INR' => 'Indian rupees (INR)', 'NPR' => 'Nepali rupees (NPR)'])
                    ->helperText('Until the visitor picks INR or NPR in the header.'),
                TextInput::make('npr_per_inr')->label('NPR for ₹1')->numeric()->minValue(0.5)->maxValue(5)->step(0.001)->required()
                    ->helperText('Used for packages without their own NPR price. The fixed rate is 1.6.'),
                TextInput::make('advance_percent')->label('Advance to confirm a booking')->numeric()->minValue(0)->maxValue(100)->suffix('%')->required(),
            ]),

            Section::make('Travellers paying from India (INR)')
                ->description('Shown on the booking page and in the booking email when the price is in Indian rupees.')
                ->columns(2)
                ->schema([
                    TextInput::make('upi_id')->label('UPI ID')->placeholder('yourname@bank')->maxLength(100)
                        ->regex('/^[A-Za-z0-9.\-_]{2,256}@[A-Za-z][A-Za-z0-9]{1,63}$/')
                        ->validationMessages(['regex' => 'Enter a UPI ID like name@bank.'])
                        ->helperText('On phones, travellers get a button that opens their UPI app with the amount filled in.'),
                    TextInput::make('upi_number')->label('UPI number')->tel()->maxLength(20)
                        ->helperText('Mobile number linked to the UPI ID.'),
                    TextInput::make('upi_name')->label('Name shown on UPI')->maxLength(100)
                        ->helperText('So travellers know they are paying the right account.'),
                    Fields::settingImage('upi_qr', 'UPI QR code'),
                    Textarea::make('bank_details_india')->label('Indian bank account')->rows(3)->maxLength(1000)->columnSpanFull()
                        ->placeholder("Account name\nAccount number\nIFSC\nBank and branch"),
                ]),

            Section::make('Travellers paying from Nepal (NPR)')
                ->description('Shown when the price is in Nepali rupees.')
                ->columns(2)
                ->schema([
                    TextInput::make('esewa_id')->label('eSewa ID')->maxLength(60)->helperText('Usually the registered mobile number.'),
                    TextInput::make('khalti_id')->label('Khalti ID')->maxLength(60),
                    Fields::settingImage('nepal_qr', 'Fonepay / eSewa / Khalti QR code')->columnSpanFull(),
                    Textarea::make('bank_details_nepal')->label('Nepali bank account')->rows(3)->maxLength(1000)->columnSpanFull()
                        ->placeholder("Account name\nAccount number\nBank and branch"),
                ]),

            Textarea::make('payment_note')->label('Note under the payment details')->rows(2)->maxLength(400),
        ]);
    }

    private function emailTab(): Tab
    {
        return Tab::make('Email')->icon(Heroicon::OutlinedEnvelope)->schema([
            Section::make('Who receives notifications')->schema([
                TextInput::make('booking_notify_email')->label('Send new booking and enquiry emails to')->email()->required()->maxLength(160),
                Toggle::make('inquiry_autoreply')->label('Send travellers an automatic “we received your enquiry” email'),
            ]),
            Section::make('Mail server (SMTP)')
                ->description('Needed for the website to actually send emails. Use the SMTP details from your hosting provider, Google Workspace or Gmail (with an app password), Zoho or Brevo. Then use “Send test email” at the top.')
                ->columns(2)
                ->schema([
                    TextInput::make('mail_host')->label('SMTP server')->placeholder('smtp.gmail.com')->maxLength(150),
                    TextInput::make('mail_port')->label('Port')->numeric()->placeholder('587'),
                    Select::make('mail_encryption')->label('Security')->options(['tls' => 'TLS / STARTTLS (port 587)', 'ssl' => 'SSL (port 465)', '' => 'None'])->native(false),
                    TextInput::make('mail_from_address')->label('Send from address')->email()->placeholder('info@onthrill.com')->maxLength(160),
                    TextInput::make('mail_username')->label('Username')->maxLength(160)->autocomplete('off'),
                    TextInput::make('mail_password')->label('Password')->password()->revealable()->maxLength(200)->autocomplete('new-password')
                        ->placeholder(filled(setting('mail_password')) ? '•••••••• (saved, leave empty to keep)' : null)
                        ->helperText('Stored encrypted.'),
                ]),
        ]);
    }

    private function seoTab(): Tab
    {
        return Tab::make('SEO')->icon(Heroicon::OutlinedMagnifyingGlass)->schema([
            Section::make('Homepage in search results')->schema([
                TextInput::make('meta_title')->label('Title')->maxLength(120)
                    ->helperText('About 60 characters show in Google.'),
                Textarea::make('meta_description')->label('Description')->rows(3)->maxLength(300)
                    ->helperText('About 155 characters show in Google.'),
                Fields::settingImage('share_image', 'Share image')
                    ->helperText('Shown when a page without its own photo is shared on WhatsApp or Facebook. 1200 × 630 px.'),
            ]),
            Section::make('Business details for search engines')
                ->description('Used in the structured data that Google, Bing and AI assistants read. Leave a line empty if you are not sure.')
                ->columns(2)
                ->schema([
                    TextInput::make('organization_name')->label('Registered business name')->maxLength(150),
                    TextInput::make('geo_placename')->label('Office town or city')->placeholder('Kathmandu')->maxLength(100),
                    TextInput::make('geo_region')->label('Region code')->placeholder('NP-P3')->maxLength(10)
                        ->helperText('ISO code of the province or state, e.g. NP-P3 for Bagmati, IN-UT for Uttarakhand.'),
                    Grid::make(2)->schema([
                        TextInput::make('geo_latitude')->label('Office latitude')->numeric()->minValue(-90)->maxValue(90),
                        TextInput::make('geo_longitude')->label('Office longitude')->numeric()->minValue(-180)->maxValue(180),
                    ]),
                ]),
            Section::make('Search Console and analytics')->columns(2)->schema([
                TextInput::make('google_site_verification')->label('Google verification code')->maxLength(120)
                    ->helperText('Search Console > Add property > HTML tag: the content="…" value only.'),
                TextInput::make('bing_site_verification')->label('Bing verification code')->maxLength(120),
                TextInput::make('google_analytics_id')->label('Google Analytics ID')->placeholder('G-XXXXXXXXXX')->maxLength(30)
                    ->regex('/^G-[A-Z0-9]{4,}$/')->validationMessages(['regex' => 'The ID starts with G-.']),
            ]),
            Section::make()->compact()->schema([])
                ->description('The sitemap is at /sitemap.xml, robots.txt and llms.txt are written for you. Submit the sitemap in Google Search Console and Bing Webmaster Tools once the site is live.'),
        ]);
    }
}
