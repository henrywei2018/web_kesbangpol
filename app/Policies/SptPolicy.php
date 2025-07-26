<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Spt;
use Illuminate\Auth\Access\HandlesAuthorization;

class SptPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_spt');
    }

    public function view(User $user, Spt $model): bool
    {
        return $user->can('view_spt');
    }

    public function create(User $user): bool
    {
        return $user->can('create_spt');
    }

    public function update(User $user, Spt $model): bool
    {
        return $user->can('update_spt');
    }

    public function delete(User $user, Spt $model): bool
    {
        return $user->can('delete_spt');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_spt');
    }

    public function restore(User $user, Spt $model): bool
    {
        return $user->can('restore_spt');
    }

    public function forceDelete(User $user, Spt $model): bool
    {
        return $user->can('force_delete_spt');
    }
}