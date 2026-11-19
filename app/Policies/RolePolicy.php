<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Access;

class RolePolicy extends AreaPolicy
{
    protected string $area = 'roles';

    /** The built-in Super Admin and Customer roles are fixed. */
    public function update(User $user, $role): bool
    {
        return ! in_array($role->name, Access::PROTECTED_ROLES, true) && $this->allows($user, 'update');
    }

    public function delete(User $user, $role): bool
    {
        return ! in_array($role->name, Access::PROTECTED_ROLES, true) && $this->allows($user, 'delete');
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, $model): bool
    {
        return false;
    }
}
