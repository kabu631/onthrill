<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestBookings extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->can('viewAny', Booking::class) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest booking requests')
            ->query(Booking::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('reference')->label('Booking')->weight('medium')
                    ->description(fn (Booking $record) => $record->created_at->diffForHumans()),
                TextColumn::make('name')->label('Traveller')->description(fn (Booking $record) => $record->phone),
                TextColumn::make('package_name')->label('Package')->wrap()
                    ->description(fn (Booking $record) => $record->travellers().' '.str('traveller')->plural($record->travellers())),
                TextColumn::make('travel_date')->label('Travel')->date('d M Y')->placeholder('Not set'),
                TextColumn::make('total')->formatStateUsing(fn ($state, Booking $record) => $record->money($state))->placeholder('On request'),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => Booking::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => BookingResource::statusColor($state)),
            ])
            ->recordActions([
                Action::make('open')->url(fn (Booking $record) => BookingResource::getUrl(auth()->user()->can('update', $record) ? 'edit' : 'view', ['record' => $record])),
            ])
            ->emptyStateHeading('No bookings yet');
    }
}
