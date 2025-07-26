<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SptPegawai;
use Illuminate\Auth\Access\HandlesAuthorization;

class SptPegawaiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sptpegawai');
    }

    public function view(User $user, SptPegawai $model): bool
    {
        return $user->can('view_sptpegawai');
    }

    public function create(User $user): bool
    {
        return $user->can('create_sptpegawai');
    }

    public function update(User $user, SptPegawai $model): bool
    {
        return $user->can('update_sptpegawai');
    }

    public function delete(User $user, SptPegawai $model): bool
    {
        return $user->can('delete_sptpegawai');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_sptpegawai');
    }

    public function restore(User $user, SptPegawai $model): bool
    {
        return $user->can('restore_sptpegawai');
    }

    public function forceDelete(User $user, SptPegawai $model): bool
    {
        return $user->can('force_delete_sptpegawai');
    }
}