<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SKT;
use Illuminate\Auth\Access\HandlesAuthorization;

class SKTPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_skt');
    }

    public function view(User $user, SKT $model): bool
    {
        return $user->can('view_skt');
    }

    public function create(User $user): bool
    {
        return $user->can('create_skt');
    }

    public function update(User $user, SKT $model): bool
    {
        return $user->can('update_skt');
    }

    public function delete(User $user, SKT $model): bool
    {
        return $user->can('delete_skt');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_skt');
    }

    public function restore(User $user, SKT $model): bool
    {
        return $user->can('restore_skt');
    }

    public function forceDelete(User $user, SKT $model): bool
    {
        return $user->can('force_delete_skt');
    }
}