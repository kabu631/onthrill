<?php

namespace App\Filament\Resources\Bookings;

use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Bookings\Pages\ViewBooking;
use App\Filament\Support\LinksRecordsById;
use App\Models\Booking;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    use LinksRecordsById;

    protected static ?string $model = Booking::class;

    // Admin URLs use the numeric id; the website uses the booking reference.
    protected static ?string $recordRouteKeyName = 'id';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'reference';

    public static function canCreate(): bool
    {
        return false; // bookings come from the package pages on the website
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['reference', 'name', 'phone', 'email'];
    }

    public static function getNavigationBadge(): ?string
    {
        $new = Booking::where('status', 'new')->count();

        return $new ? (string) $new : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New booking requests';
    }

    public static function statusColor(?string $status): string
    {
        return match ($status) {
            'new' => 'warning',
            'confirmed' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'gray',
        };
    }

    public static function paymentColor(?string $status): string
    {
        return match ($status) {
            'paid' => 'success',
            'advance_paid' => 'info',
            'refunded' => 'gray',
            default => 'warning',
        };
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make([
                    Section::make('Tour')->columns(2)->schema([
                        TextEntry::make('package_name')->label('Package')->weight('bold')->columnSpanFull()
                            ->url(fn (Booking $record) => $record->package?->url(), shouldOpenInNewTab: true),
                        DatePicker::make('travel_date')->label('Travel date')->native(false)->displayFormat('d M Y'),
                        TextInput::make('travelling_from')->label('Travelling from')->maxLength(120),
                        TextInput::make('adults')->numeric()->minValue(1)->maxValue(60)->required(),
                        TextInput::make('children')->numeric()->minValue(0)->maxValue(60)->default(0),
                        Textarea::make('notes')->label('Traveller’s message')->disabled()->columnSpanFull()
                            ->visible(fn (?Booking $record) => filled($record?->notes)),
                    ]),

                    Section::make('Price')->columns(3)
                        ->description('Worked out on the website from the package price. Change it here if you agree a different price with the traveller.')
                        ->schema([
                            Select::make('currency')->options(['INR' => 'INR (Indian rupees)', 'NPR' => 'NPR (Nepali rupees)'])->required()->native(false)->live(),
                            TextInput::make('price_per_person')->label('Per person')->numeric()->minValue(0)
                                ->prefix(fn (Get $get) => $get('currency') === 'NPR' ? 'NPR' : '₹'),
                            TextInput::make('total')->numeric()->minValue(0)
                                ->prefix(fn (Get $get) => $get('currency') === 'NPR' ? 'NPR' : '₹'),
                            TextInput::make('advance')->label('Advance to pay')->numeric()->minValue(0)
                                ->prefix(fn (Get $get) => $get('currency') === 'NPR' ? 'NPR' : '₹'),
                        ]),

                    Section::make('Traveller')->columns(2)->schema([
                        TextInput::make('name')->required()->maxLength(120),
                        TextInput::make('phone')->required()->tel()->maxLength(20),
                        TextInput::make('email')->email()->required()->maxLength(160),
                        Select::make('nationality')->options(Booking::NATIONALITIES)->required()->native(false),
                    ]),
                ])->columnSpan(['lg' => 2]),

                Group::make([
                    Section::make('Status')->schema([
                        TextEntry::make('reference')->copyable()->weight('bold'),
                        TextEntry::make('created_at')->label('Received')->dateTime('d M Y, h:i A'),
                        Select::make('status')->options(Booking::STATUSES)->required()->native(false),
                        Textarea::make('admin_notes')->label('Internal notes')->rows(4)->helperText('Only visible to the team.'),
                    ]),
                    Section::make('Payment')->schema([
                        Select::make('payment_status')->options(Booking::PAYMENT_STATUSES)->required()->native(false),
                        Select::make('payment_method')->label('Paid by')->options(Booking::PAYMENT_METHODS)->native(false),
                        TextInput::make('payment_reference')->label('Transaction / UTR reference')->maxLength(191),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('reference')->label('Booking')->searchable()->weight('medium')
                    ->description(fn (Booking $record) => $record->created_at->format('d M Y, h:i A')),
                TextColumn::make('name')->label('Traveller')->searchable(['name', 'phone', 'email'])
                    ->description(fn (Booking $record) => $record->phone.' · '.(Booking::NATIONALITIES[$record->nationality] ?? $record->nationality)),
                TextColumn::make('package_name')->label('Package')->wrap()->searchable()
                    ->description(fn (Booking $record) => $record->travellers().' '.str('traveller')->plural($record->travellers())),
                TextColumn::make('travel_date')->label('Travel')->date('d M Y')->sortable()->placeholder('Not set'),
                TextColumn::make('total')->sortable()
                    ->formatStateUsing(fn ($state, Booking $record) => $record->money($state))
                    ->placeholder('On request'),
                TextColumn::make('payment_status')->label('Payment')->badge()
                    ->formatStateUsing(fn ($state) => Booking::PAYMENT_STATUSES[$state] ?? $state)
                    ->color(fn ($state) => static::paymentColor($state)),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => Booking::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => static::statusColor($state)),
            ])
            ->filters([
                SelectFilter::make('status')->options(Booking::STATUSES)->multiple(),
                SelectFilter::make('payment_status')->label('Payment')->options(Booking::PAYMENT_STATUSES),
                SelectFilter::make('nationality')->options(Booking::NATIONALITIES),
                SelectFilter::make('package_id')->label('Package')->relationship('package', 'name')->searchable()->preload(),
                Filter::make('upcoming')->label('Travelling in the next 30 days')
                    ->query(fn (Builder $query) => $query->whereBetween('travel_date', [today(), today()->addDays(30)])),
            ])
            ->recordActions([
                EditAction::make()->label('Open'),
                ViewAction::make()->label('Open')->hidden(fn (Booking $record) => auth()->user()->can('update', $record)),
                Action::make('whatsapp')->label('')->tooltip('WhatsApp traveller')->icon(Heroicon::OutlinedChatBubbleLeftRight)->color('success')
                    ->url(fn (Booking $record) => static::whatsappLink($record), shouldOpenInNewTab: true),
            ]);
    }

    public static function whatsappLink(Booking $record): string
    {
        return 'https://wa.me/'.phone_digits($record->phone).'?text='.rawurlencode(
            "Namaste {$record->name}, this is ".setting('site_name')." about your booking {$record->reference} ({$record->package_name})."
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookings::route('/'),
            'view' => ViewBooking::route('/{record}'),
            'edit' => EditBooking::route('/{record}/edit'),
        ];
    }
}
