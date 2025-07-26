<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Aduan;
use Illuminate\Auth\Access\HandlesAuthorization;

class AduanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_aduan');
    }

    public function view(User $user, Aduan $model): bool
    {
        return $user->can('view_aduan');
    }

    public function create(User $user): bool
    {
        return $user->can('create_aduan');
    }

    public function update(User $user, Aduan $model): bool
    {
        return $user->can('update_aduan');
    }

    public function delete(User $user, Aduan $model): bool
    {
        return $user->can('delete_aduan');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_aduan');
    }

    public function restore(User $user, Aduan $model): bool
    {
        return $user->can('restore_aduan');
    }

    public function forceDelete(User $user, Aduan $model): bool
    {
        return $user->can('force_delete_aduan');
    }
}