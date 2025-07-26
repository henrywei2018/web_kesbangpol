<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SKL;
use Illuminate\Auth\Access\HandlesAuthorization;

class SKLPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_skl');
    }

    public function view(User $user, SKL $model): bool
    {
        return $user->can('view_skl');
    }

    public function create(User $user): bool
    {
        return $user->can('create_skl');
    }

    public function update(User $user, SKL $model): bool
    {
        return $user->can('update_skl');
    }

    public function delete(User $user, SKL $model): bool
    {
        return $user->can('delete_skl');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_skl');
    }

    public function restore(User $user, SKL $model): bool
    {
        return $user->can('restore_skl');
    }

    public function forceDelete(User $user, SKL $model): bool
    {
        return $user->can('force_delete_skl');
    }
}