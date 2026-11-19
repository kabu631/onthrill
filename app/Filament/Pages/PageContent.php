<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\SavesSettings;
use App\Filament\Support\Fields;
use App\Support\SiteTexts;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * Every heading, intro, button and label on the website's built-in pages: the
 * homepage sections, and the wording listed in App\Support\SiteTexts.
 * (About Us, Travel Information etc. are free-form pages under Website > Pages.)
 */
class PageContent extends Page
{
    use SavesSettings;

    public const HOME_SECTIONS = [
        'hero' => 'Hero banner',
        'facts' => 'Facts strip',
        'destinations' => 'Destinations',
        'packages' => 'Tour packages',
        'why' => 'Why travel with us',
        'how' => 'How booking works',
        'testimonials' => 'Testimonials',
        'gallery' => 'Gallery',
        'faq' => 'Questions',
        'cta' => 'Call-to-action banner',
    ];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?string $navigationLabel = 'Page Content';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'Page Content';

    public static function canAccess(): bool
    {
        return auth()->user()?->allows('page_content.update') ?? false;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')->label('View website')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->color('gray')
                ->url('/', shouldOpenInNewTab: true),
        ];
    }

    public function form(Schema $schema): Schema
    {
        $note = fn (string $text) => Section::make()->compact()->schema([])->description($text);

        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('pages')->persistTabInQueryString()->tabs([
                    $this->homepageTab(),
                    Tab::make('Header & footer')->icon(Heroicon::OutlinedBars3)->schema([
                        $note('The logo, phone, WhatsApp, email and social links are in Settings > Site Settings. Pages listed in the menu and footer are chosen under Website > Pages.'),
                        ...$this->wordingSections('Header & footer'),
                    ]),
                    Tab::make('Destinations')->icon(Heroicon::OutlinedMapPin)->schema([
                        $note('Each destination’s own text, facts, photos and questions are edited under Tours > Destinations.'),
                        ...$this->wordingSections('Destinations'),
                    ]),
                    Tab::make('Tour packages')->icon(Heroicon::OutlinedMap)->schema([
                        $note('Each package’s own itinerary, prices and photos are edited under Tours > Packages.'),
                        ...$this->wordingSections('Tour packages'),
                        Section::make('Points under the price box')->collapsible()->collapsed()->schema([
                            TagsInput::make('package_trust_points')->hiddenLabel()->helperText('Up to 4. Press Enter after each one.'),
                        ]),
                    ]),
                    Tab::make('Enquiries & contact')->icon(Heroicon::OutlinedEnvelope)->schema([
                        $note('Phone, email, address, hours and the map on the contact page come from Settings > Site Settings > Contact & Social.'),
                        ...$this->wordingSections('Enquiries & contact'),
                    ]),
                    Tab::make('Other pages')->icon(Heroicon::OutlinedDocumentDuplicate)->schema(
                        $this->wordingSections('Other pages'),
                    ),
                ]),
            ]);
    }

    /**
     * One collapsible section per group of wording listed in App\Support\SiteTexts.
     * An emptied field goes back to the original text.
     */
    private function wordingSections(string $tab): array
    {
        return collect(SiteTexts::GROUPS[$tab])
            ->map(fn (array $fields, string $title) => Section::make($title)->collapsible()->collapsed()->columns(2)->schema(
                collect($fields)->map(function (array $field, string $key) {
                    [$label, $default] = $field;
                    $long = $field[2] ?? false;
                    preg_match_all('/:[a-z]+/', $default, $tokens);
                    $component = $long
                        ? Textarea::make($key)->rows(2)->maxLength(600)->columnSpanFull()
                        : TextInput::make($key)->maxLength(255);

                    return $component->label($label)->placeholder($default)
                        ->helperText($tokens[0] ? 'Keep '.implode(', ', array_unique($tokens[0])).': the website fills it in.' : null);
                })->values()->all()
            ))
            ->values()->all();
    }

    private function homepageTab(): Tab
    {
        return Tab::make('Homepage')->icon(Heroicon::OutlinedHome)->schema([
            Section::make('Sections shown')->description('Untick a section to hide it from the homepage.')->schema([
                CheckboxList::make('home_sections')->hiddenLabel()->options(self::HOME_SECTIONS)->columns(4)->bulkToggleable(),
            ]),

            Section::make('Hero banner')->collapsible()->columns(2)->schema([
                TextInput::make('hero_eyebrow')->label('Heading start')->maxLength(60)->columnSpanFull()
                    ->helperText('The heading reads “Heading start” followed by the title of the photo on screen, e.g. “Book Your” + “Chardham Yatra”.'),
                Textarea::make('hero_text')->label('Intro text')->rows(3)->maxLength(500)->columnSpanFull(),
                TextInput::make('hero_primary_label')->label('Main button text')->maxLength(40),
                TextInput::make('hero_primary_url')->label('Main button link')->maxLength(255)->placeholder('/packages'),
                TextInput::make('hero_secondary_label')->label('Second button text')->maxLength(40),
                TextInput::make('hero_secondary_url')->label('Second button link')->maxLength(255)->placeholder('/destinations'),

                Repeater::make('hero_slides')->label('Slideshow photos')->columnSpanFull()
                    ->helperText('Shown one after another behind the heading. Use wide landscape photos, at least 1920 px wide.')
                    ->itemLabel(fn (array $state) => $state['title'] ?? null)
                    ->collapsible()->collapsed()->reorderable()->maxItems(6)->columns(2)
                    ->schema([
                        Fields::settingImage('image', 'Photo')->required()->columnSpanFull(),
                        TextInput::make('title')->label('Heading line')->required()->maxLength(60)->placeholder('Chardham Yatra'),
                        TextInput::make('place')->label('Place shown in the corner')->maxLength(80)->placeholder('Kedarnath, Uttarakhand'),
                        TextInput::make('link')->label('Place link')->maxLength(255)->placeholder('/destinations/kedarnath'),
                        TextInput::make('credit')->label('Photo credit')->maxLength(255)
                            ->helperText('Needed for Creative Commons photos. Leave empty for your own.'),
                    ]),

                Section::make('Without slideshow photos')->compact()->collapsible()->collapsed()->columnSpanFull()
                    ->description('Used only when the slideshow above is empty.')
                    ->schema([
                        TagsInput::make('hero_words')->label('Rotating lines after the heading start')->helperText('Press Enter after each one.'),
                        Fields::settingImage('hero_background', 'Background photo'),
                    ]),
            ]),

            Section::make('Destinations')->collapsible()->collapsed()
                ->description('Choose the destinations shown here with the “Show on homepage” switch under Tours > Destinations.')
                ->schema([
                    TextInput::make('destinations_title')->label('Heading')->maxLength(120),
                    Textarea::make('destinations_text')->label('Intro text')->rows(2)->maxLength(300),
                ]),

            Section::make('Tour packages')->collapsible()->collapsed()
                ->description('Choose the packages shown here with the “Show on homepage” switch under Tours > Packages.')
                ->schema([
                    TextInput::make('home_packages_title')->label('Heading')->maxLength(120),
                ]),

            Section::make('Why travel with us')->collapsible()->collapsed()->schema([
                TextInput::make('why_title')->label('Heading')->maxLength(80),
                Textarea::make('why_text')->label('Intro text')->rows(2)->maxLength(400),
                Grid::make(2)->schema([
                    Fields::settingImage('why_image', 'Large photo')->helperText('Upright photo works best.'),
                    Fields::settingImage('why_image_small', 'Small photo (overlapping the corner)'),
                ]),
                Repeater::make('why_items')->label('Points')->grid(2)->maxItems(6)->collapsible()
                    ->itemLabel(fn (array $state) => $state['title'] ?? null)
                    ->schema([
                        TextInput::make('title')->required()->maxLength(80),
                        Textarea::make('text')->rows(2)->maxLength(250),
                    ]),
            ]),

            Section::make('How booking works')->collapsible()->collapsed()->schema([
                Grid::make(2)->schema([
                    TextInput::make('how_title')->label('Heading')->maxLength(80)->columnSpanFull(),
                    Textarea::make('how_text')->label('Intro text')->rows(2)->maxLength(300)->columnSpanFull(),
                    TextInput::make('how_button_label')->label('Button text')->maxLength(40),
                    TextInput::make('how_button_url')->label('Button link')->maxLength(255),
                ]),
                Repeater::make('how_steps')->label('Steps')->grid(2)->maxItems(4)
                    ->itemLabel(fn (array $state) => $state['title'] ?? null)
                    ->schema([
                        TextInput::make('title')->required()->maxLength(60),
                        Textarea::make('text')->rows(2)->maxLength(200),
                        Fields::settingImage('icon', 'Icon')->helperText('Optional. Without one the step number is shown.'),
                    ]),
            ]),

            Section::make('Testimonials')->collapsible()->collapsed()
                ->description('Reviews are managed under Website > Testimonials.')
                ->schema([
                    TextInput::make('testimonials_title')->label('Heading')->maxLength(80),
                ]),

            Section::make('Gallery')->collapsible()->collapsed()->schema([
                TextInput::make('home_gallery_title')->label('Heading')->maxLength(80),
            ]),

            Section::make('Questions')->collapsible()->collapsed()
                ->description('Also sent to Google as an FAQ, so keep the answers short and factual.')
                ->schema([
                    TextInput::make('faq_title')->label('Heading')->maxLength(80),
                    Textarea::make('faq_text')->label('Intro text')->rows(2)->maxLength(300),
                    Repeater::make('faqs')->label('Questions')->collapsible()->reorderable()
                        ->itemLabel(fn (array $state) => $state['question'] ?? null)
                        ->schema([
                            TextInput::make('question')->required()->maxLength(200),
                            Textarea::make('answer')->required()->rows(3)->maxLength(1000),
                        ]),
                ]),

            Section::make('Call-to-action banner')->collapsible()->collapsed()->schema([
                TextInput::make('cta_title')->label('Heading')->maxLength(120),
                Textarea::make('cta_text')->label('Text')->rows(2)->maxLength(300),
                Fields::settingImage('cta_image', 'Background photo')->helperText('Wide photo. It is darkened so the text stays readable.'),
            ]),

            // Labels of the search bar, the numbers and the buttons
            ...$this->wordingSections('Homepage'),
        ]);
    }
}
