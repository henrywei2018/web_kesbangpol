<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sppd;
use Illuminate\Auth\Access\HandlesAuthorization;

class SppdPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sppd');
    }

    public function view(User $user, Sppd $model): bool
    {
        return $user->can('view_sppd');
    }

    public function create(User $user): bool
    {
        return $user->can('create_sppd');
    }

    public function update(User $user, Sppd $model): bool
    {
        return $user->can('update_sppd');
    }

    public function delete(User $user, Sppd $model): bool
    {
        return $user->can('delete_sppd');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_sppd');
    }

    public function restore(User $user, Sppd $model): bool
    {
        return $user->can('restore_sppd');
    }

    public function forceDelete(User $user, Sppd $model): bool
    {
        return $user->can('force_delete_sppd');
    }
}