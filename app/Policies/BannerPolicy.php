<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Banner;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_banner');
    }

    public function view(User $user, Banner $model): bool
    {
        return $user->can('view_banner');
    }

    public function create(User $user): bool
    {
        return $user->can('create_banner');
    }

    public function update(User $user, Banner $model): bool
    {
        return $user->can('update_banner');
    }

    public function delete(User $user, Banner $model): bool
    {
        return $user->can('delete_banner');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_banner');
    }

    public function restore(User $user, Banner $model): bool
    {
        return $user->can('restore_banner');
    }

    public function forceDelete(User $user, Banner $model): bool
    {
        return $user->can('force_delete_banner');
    }
}