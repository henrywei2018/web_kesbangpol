<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KontakKami;
use Illuminate\Auth\Access\HandlesAuthorization;

class KontakKamiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_kontakkami');
    }

    public function view(User $user, KontakKami $model): bool
    {
        return $user->can('view_kontakkami');
    }

    public function create(User $user): bool
    {
        return $user->can('create_kontakkami');
    }

    public function update(User $user, KontakKami $model): bool
    {
        return $user->can('update_kontakkami');
    }

    public function delete(User $user, KontakKami $model): bool
    {
        return $user->can('delete_kontakkami');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_kontakkami');
    }

    public function restore(User $user, KontakKami $model): bool
    {
        return $user->can('restore_kontakkami');
    }

    public function forceDelete(User $user, KontakKami $model): bool
    {
        return $user->can('force_delete_kontakkami');
    }
}