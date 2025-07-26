<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Galeri;
use Illuminate\Auth\Access\HandlesAuthorization;

class GaleriPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_galeri');
    }

    public function view(User $user, Galeri $model): bool
    {
        return $user->can('view_galeri');
    }

    public function create(User $user): bool
    {
        return $user->can('create_galeri');
    }

    public function update(User $user, Galeri $model): bool
    {
        return $user->can('update_galeri');
    }

    public function delete(User $user, Galeri $model): bool
    {
        return $user->can('delete_galeri');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_galeri');
    }

    public function restore(User $user, Galeri $model): bool
    {
        return $user->can('restore_galeri');
    }

    public function forceDelete(User $user, Galeri $model): bool
    {
        return $user->can('force_delete_galeri');
    }
}