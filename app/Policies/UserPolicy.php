<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Access;

class UserPolicy extends AreaPolicy
{
    protected string $area = 'users';

    /** Only a Super Admin may change another Super Admin's account. */
    public function update(User $user, $target): bool
    {
        if ($target->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        return $this->allows($user, 'update');
    }

    public function delete(User $user, $target): bool
    {
        // Nobody deletes their own account from the panel, and the last
        // Super Admin can never be removed (that would lock everyone out).
        if ($target->is($user)) {
            return false;
        }

        if ($target->isSuperAdmin()) {
            return $user->isSuperAdmin() && User::role(Access::SUPER_ADMIN)->count() > 1;
        }

        return $this->allows($user, 'delete');
    }

    /** Bulk delete could bypass the per-user rules above, so it is not offered. */
    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, $model): bool
    {
        return false;
    }
}
