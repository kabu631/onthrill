<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    public function getTitle(): string
    {
        return 'Booking '.$this->record->reference;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('whatsapp')
                ->label('WhatsApp traveller')
                ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                ->color('success')
                ->url(fn (Booking $record) => BookingResource::whatsappLink($record), shouldOpenInNewTab: true),
            Action::make('page')
                ->label('Traveller’s page')
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->url(fn (Booking $record) => route('bookings.show', $record), shouldOpenInNewTab: true),
            ActionGroup::make([
                Action::make('confirm')
                    ->label('Confirm booking')
                    ->icon(Heroicon::OutlinedCheck)
                    ->visible(fn (Booking $record) => $record->status === 'new')
                    ->action(fn () => $this->setStatus(['status' => 'confirmed'])),
                Action::make('advancePaid')
                    ->label('Advance received')
                    ->icon(Heroicon::OutlinedBanknotes)
                    ->visible(fn (Booking $record) => $record->payment_status === 'pending')
                    ->requiresConfirmation()
                    ->modalDescription('Marks the advance as received and confirms the booking.')
                    ->action(fn (Booking $record) => $this->setStatus([
                        'payment_status' => 'advance_paid',
                        'status' => $record->status === 'new' ? 'confirmed' : $record->status,
                    ])),
                Action::make('paid')
                    ->label('Paid in full')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->visible(fn (Booking $record) => $record->payment_status !== 'paid')
                    ->requiresConfirmation()
                    ->action(fn (Booking $record) => $this->setStatus([
                        'payment_status' => 'paid',
                        'status' => $record->status === 'new' ? 'confirmed' : $record->status,
                    ])),
            ])->label('Update')->button()->icon(Heroicon::OutlinedChevronDown)->iconPosition('after'),
        ];
    }

    private function setStatus(array $changes): void
    {
        $this->record->update($changes);
        $this->refreshFormData(array_keys($changes));
    }
}
