<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Auth\Access\HandlesAuthorization;

class PegawaiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pegawai');
    }

    public function view(User $user, Pegawai $model): bool
    {
        return $user->can('view_pegawai');
    }

    public function create(User $user): bool
    {
        return $user->can('create_pegawai');
    }

    public function update(User $user, Pegawai $model): bool
    {
        return $user->can('update_pegawai');
    }

    public function delete(User $user, Pegawai $model): bool
    {
        return $user->can('delete_pegawai');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pegawai');
    }

    public function restore(User $user, Pegawai $model): bool
    {
        return $user->can('restore_pegawai');
    }

    public function forceDelete(User $user, Pegawai $model): bool
    {
        return $user->can('force_delete_pegawai');
    }
}