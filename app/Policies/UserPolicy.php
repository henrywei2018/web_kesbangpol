<?php

namespace App\Policies;

use App\Models\User as AppUser;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(AppUser $user): bool
    {
        return $user->can('view_any_user');
    }

    public function view(AppUser $user, AppUser $model): bool
    {
        return $user->can('view_user');
    }

    public function create(AppUser $user): bool
    {
        return $user->can('create_user');
    }

    public function update(AppUser $user, AppUser $model): bool
    {
        return $user->can('update_user');
    }

    public function delete(AppUser $user, AppUser $model): bool
    {
        return $user->can('delete_user');
    }

    public function deleteAny(AppUser $user): bool
    {
        return $user->can('delete_any_user');
    }

    public function restore(AppUser $user, AppUser $model): bool
    {
        return $user->can('restore_user');
    }

    public function forceDelete(AppUser $user, AppUser $model): bool
    {
        return $user->can('force_delete_user');
    }
}
