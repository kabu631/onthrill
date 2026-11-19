<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\Pages\Concerns\SavesRolePermissions;
use App\Filament\Resources\Roles\RoleResource;
use App\Support\Access;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRole extends EditRecord
{
    use SavesRolePermissions;

    protected static string $resource = RoleResource::class;

    public function getTitle(): string
    {
        return 'Edit role: '.Access::roleLabel($this->record->name);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->modalDescription('People who only have this role will lose their admin access.'),
        ];
    }

    /** Tick the boxes for the permissions the role already has. */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $granted = $this->record->permissions->pluck('name');

        foreach (Access::AREAS as $area => [, $abilities]) {
            $data[RoleResource::field($area)] = array_values(array_filter(
                $abilities,
                fn ($ability) => $granted->contains("{$area}.{$ability}"),
            ));
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update(['name' => $this->roleNameFrom($data, $record)]);
        $record->syncPermissions($this->permissionsFrom($data));

        return $record;
    }
}
