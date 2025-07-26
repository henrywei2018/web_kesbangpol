<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Auth\Access\HandlesAuthorization;

class WilayahPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_wilayah');
    }

    public function view(User $user, Wilayah $model): bool
    {
        return $user->can('view_wilayah');
    }

    public function create(User $user): bool
    {
        return $user->can('create_wilayah');
    }

    public function update(User $user, Wilayah $model): bool
    {
        return $user->can('update_wilayah');
    }

    public function delete(User $user, Wilayah $model): bool
    {
        return $user->can('delete_wilayah');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_wilayah');
    }

    public function restore(User $user, Wilayah $model): bool
    {
        return $user->can('restore_wilayah');
    }

    public function forceDelete(User $user, Wilayah $model): bool
    {
        return $user->can('force_delete_wilayah');
    }
}