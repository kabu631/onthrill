<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\Pages\Concerns\SavesRolePermissions;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class CreateRole extends CreateRecord
{
    use SavesRolePermissions;

    protected static string $resource = RoleResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $role = Role::create(['name' => $this->roleNameFrom($data), 'guard_name' => 'web']);
        $role->syncPermissions($this->permissionsFrom($data));

        return $role;
    }
}
