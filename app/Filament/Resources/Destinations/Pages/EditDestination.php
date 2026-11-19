<?php

namespace App\Filament\Resources\Destinations\Pages;

use App\Filament\Resources\Destinations\DestinationResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditDestination extends EditRecord
{
    protected static string $resource = DestinationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view')->label('View on website')->icon(Heroicon::OutlinedArrowTopRightOnSquare)->color('gray')
                ->url(fn () => $this->getRecord()->url())->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
