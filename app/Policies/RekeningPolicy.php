<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rekening;
use Illuminate\Auth\Access\HandlesAuthorization;

class RekeningPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_rekening');
    }

    public function view(User $user, Rekening $model): bool
    {
        return $user->can('view_rekening');
    }

    public function create(User $user): bool
    {
        return $user->can('create_rekening');
    }

    public function update(User $user, Rekening $model): bool
    {
        return $user->can('update_rekening');
    }

    public function delete(User $user, Rekening $model): bool
    {
        return $user->can('delete_rekening');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_rekening');
    }

    public function restore(User $user, Rekening $model): bool
    {
        return $user->can('restore_rekening');
    }

    public function forceDelete(User $user, Rekening $model): bool
    {
        return $user->can('force_delete_rekening');
    }
}