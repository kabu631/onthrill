<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/** Read-only booking screen for staff who may see bookings but not change them. */
class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    public function getTitle(): string
    {
        return 'Booking '.$this->record->reference;
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
