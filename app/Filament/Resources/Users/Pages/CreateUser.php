<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Support\Access;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // No role chosen (or the creator may not assign roles): a plain customer.
        if ($this->record->roles()->doesntExist()) {
            $this->record->assignRole(Access::CUSTOMER);
        }
    }
}
