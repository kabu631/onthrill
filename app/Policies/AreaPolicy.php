<?php

namespace App\Policies;

use App\Models\User;

/**
 * Maps the standard policy abilities onto "<area>.<ability>" permissions.
 * Each model policy only declares its area (see App\Support\Access::AREAS).
 */
abstract class AreaPolicy
{
    protected string $area;

    protected function allows(User $user, string $ability): bool
    {
        return $user->allows("{$this->area}.{$ability}");
    }

    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'view');
    }

    public function view(User $user, $model): bool
    {
        return $this->allows($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'create');
    }

    public function update(User $user, $model): bool
    {
        return $this->allows($user, 'update');
    }

    /** Bulk edits (e.g. "Make visible") that have no single record. */
    public function updateAny(User $user): bool
    {
        return $this->allows($user, 'update');
    }

    public function delete(User $user, $model): bool
    {
        return $this->allows($user, 'delete');
    }

    public function deleteAny(User $user): bool
    {
        return $this->allows($user, 'delete');
    }

    public function reorder(User $user): bool
    {
        return $this->allows($user, 'update');
    }

    public function replicate(User $user, $model): bool
    {
        return $this->allows($user, 'create');
    }

    public function restore(User $user, $model): bool
    {
        return $this->allows($user, 'delete');
    }

    public function forceDelete(User $user, $model): bool
    {
        return $this->allows($user, 'delete');
    }
}
