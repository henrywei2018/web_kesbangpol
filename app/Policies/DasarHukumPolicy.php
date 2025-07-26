<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DasarHukum;
use Illuminate\Auth\Access\HandlesAuthorization;

class DasarHukumPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_dasarhukum');
    }

    public function view(User $user, DasarHukum $model): bool
    {
        return $user->can('view_dasarhukum');
    }

    public function create(User $user): bool
    {
        return $user->can('create_dasarhukum');
    }

    public function update(User $user, DasarHukum $model): bool
    {
        return $user->can('update_dasarhukum');
    }

    public function delete(User $user, DasarHukum $model): bool
    {
        return $user->can('delete_dasarhukum');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_dasarhukum');
    }

    public function restore(User $user, DasarHukum $model): bool
    {
        return $user->can('restore_dasarhukum');
    }

    public function forceDelete(User $user, DasarHukum $model): bool
    {
        return $user->can('force_delete_dasarhukum');
    }
}