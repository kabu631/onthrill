<?php

namespace App\Filament\Resources\Inquiries;

use App\Filament\Resources\Inquiries\Pages\EditInquiry;
use App\Filament\Resources\Inquiries\Pages\ListInquiries;
use App\Filament\Resources\Inquiries\Pages\ViewInquiry;
use App\Models\Inquiry;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InquiryResource extends Resource
{
    protected static ?string $model = Inquiry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|\UnitEnum|null $navigationGroup = 'Bookings';

    protected static ?string $navigationLabel = 'Enquiries';

    protected static ?string $modelLabel = 'enquiry';

    protected static ?string $pluralModelLabel = 'enquiries';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $new = Inquiry::where('status', 'new')->count();

        return $new ? (string) $new : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Enquiry')->columnSpan(['lg' => 2])->columns(2)->schema([
                    TextEntry::make('name')->weight('bold'),
                    TextEntry::make('created_at')->label('Received')->dateTime('d M Y, h:i A'),
                    // The number stays left-aligned like the other details; the WhatsApp
                    // button sits beside the label (a suffix button would push the
                    // number to the middle of the column).
                    TextEntry::make('phone')
                        ->url(fn (Inquiry $record) => 'tel:'.phone_digits($record->phone))
                        ->hintAction(Action::make('whatsapp')->label('Reply on WhatsApp')->icon(Heroicon::OutlinedChatBubbleLeftRight)->color('success')
                            ->url(fn (Inquiry $record) => 'https://wa.me/'.phone_digits($record->phone), shouldOpenInNewTab: true)),
                    TextEntry::make('email')->placeholder('-')->url(fn (Inquiry $record) => $record->email ? 'mailto:'.$record->email : null),
                    TextEntry::make('topic')->label('About')->state(fn (Inquiry $record) => $record->topic())->placeholder('General enquiry')->columnSpanFull()
                        ->url(fn (Inquiry $record) => $record->package?->url() ?? $record->destination?->url(), shouldOpenInNewTab: true),
                    TextEntry::make('message')->columnSpanFull()->prose(),
                ]),
                Section::make('Follow-up')->columnSpan(['lg' => 1])->schema([
                    Select::make('status')->options(Inquiry::STATUSES)->required()->native(false),
                    Textarea::make('admin_notes')->label('Internal notes')->rows(6),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->with(['package', 'destination']))
            ->columns([
                TextColumn::make('name')->searchable(['name', 'phone', 'email'])->weight('medium')
                    ->description(fn (Inquiry $record) => $record->phone),
                TextColumn::make('topic')->label('About')->state(fn (Inquiry $record) => $record->topic())->placeholder('General')->wrap(),
                TextColumn::make('message')->limit(70)->wrap()->toggleable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => Inquiry::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'new' => 'warning', 'in_progress' => 'info', default => 'success'
                    }),
                TextColumn::make('created_at')->label('Received')->since()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(Inquiry::STATUSES),
            ])
            ->recordActions([
                EditAction::make()->label('Open'),
                ViewAction::make()->label('Open')->hidden(fn (Inquiry $record) => auth()->user()->can('update', $record)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
            'view' => ViewInquiry::route('/{record}'),
            'edit' => EditInquiry::route('/{record}/edit'),
        ];
    }
}
