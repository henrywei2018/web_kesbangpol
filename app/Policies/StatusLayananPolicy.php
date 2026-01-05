<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StatusLayanan;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusLayananPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_statuslayanan');
    }

    public function view(User $user, StatusLayanan $model): bool
    {
        return $user->can('view_statuslayanan');
    }

    public function create(User $user): bool
    {
        return $user->can('create_statuslayanan');
    }

    public function update(User $user, StatusLayanan $model): bool
    {
        return $user->can('update_statuslayanan');
    }

    public function delete(User $user, StatusLayanan $model): bool
    {
        return $user->can('delete_statuslayanan');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_statuslayanan');
    }

    public function restore(User $user, StatusLayanan $model): bool
    {
        return $user->can('restore_statuslayanan');
    }

    public function forceDelete(User $user, StatusLayanan $model): bool
    {
        return $user->can('force_delete_statuslayanan');
    }
}