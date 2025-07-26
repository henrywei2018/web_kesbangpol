<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KodeInstansi;
use Illuminate\Auth\Access\HandlesAuthorization;

class KodeInstansiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_kodeinstansi');
    }

    public function view(User $user, KodeInstansi $model): bool
    {
        return $user->can('view_kodeinstansi');
    }

    public function create(User $user): bool
    {
        return $user->can('create_kodeinstansi');
    }

    public function update(User $user, KodeInstansi $model): bool
    {
        return $user->can('update_kodeinstansi');
    }

    public function delete(User $user, KodeInstansi $model): bool
    {
        return $user->can('delete_kodeinstansi');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_kodeinstansi');
    }

    public function restore(User $user, KodeInstansi $model): bool
    {
        return $user->can('restore_kodeinstansi');
    }

    public function forceDelete(User $user, KodeInstansi $model): bool
    {
        return $user->can('force_delete_kodeinstansi');
    }
}