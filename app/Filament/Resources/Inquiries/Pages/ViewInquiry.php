<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Filament\Resources\Inquiries\InquiryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

/** Read-only enquiry screen for staff who may view enquiries but not change them. */
class ViewInquiry extends ViewRecord
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
