<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Publikasi;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublikasiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_publikasi');
    }

    public function view(User $user, Publikasi $model): bool
    {
        return $user->can('view_publikasi');
    }

    public function create(User $user): bool
    {
        return $user->can('create_publikasi');
    }

    public function update(User $user, Publikasi $model): bool
    {
        return $user->can('update_publikasi');
    }

    public function delete(User $user, Publikasi $model): bool
    {
        return $user->can('delete_publikasi');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_publikasi');
    }

    public function restore(User $user, Publikasi $model): bool
    {
        return $user->can('restore_publikasi');
    }

    public function forceDelete(User $user, Publikasi $model): bool
    {
        return $user->can('force_delete_publikasi');
    }
}