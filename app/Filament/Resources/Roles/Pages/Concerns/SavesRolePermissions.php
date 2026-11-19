<?php

namespace App\Filament\Resources\Roles\Pages\Concerns;

use App\Filament\Resources\Roles\RoleResource;
use App\Support\Access;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

/** Shared by the create and edit role pages. */
trait SavesRolePermissions
{
    /** Turn the per-area checkbox state into a flat list of permission names. */
    protected function permissionsFrom(array $data): array
    {
        $names = [];

        foreach (Access::AREAS as $area => [, $abilities]) {
            foreach (array_intersect($abilities, $data[RoleResource::field($area)] ?? []) as $ability) {
                $names[] = "{$area}.{$ability}";
            }
        }

        return $names;
    }

    /** "Booking Staff" becomes "booking-staff", refusing duplicates and built-in names. */
    protected function roleNameFrom(array $data, ?Role $ignore = null): string
    {
        $name = Str::slug($data['name'] ?? '');

        $taken = Role::where('name', $name)->where('guard_name', 'web')
            ->when($ignore, fn ($q) => $q->whereKeyNot($ignore->getKey()))
            ->exists();

        if ($name === '' || $taken || in_array($name, Access::PROTECTED_ROLES, true)) {
            throw ValidationException::withMessages(['data.name' => 'Please choose a different role name; this one is already in use.']);
        }

        return $name;
    }
}
