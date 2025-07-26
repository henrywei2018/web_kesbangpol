<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KeberatanInformasiPublik;
use Illuminate\Auth\Access\HandlesAuthorization;

class KeberatanInformasiPublikPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_keberataninformasipublik');
    }

    public function view(User $user, KeberatanInformasiPublik $model): bool
    {
        return $user->can('view_keberataninformasipublik');
    }

    public function create(User $user): bool
    {
        return $user->can('create_keberataninformasipublik');
    }

    public function update(User $user, KeberatanInformasiPublik $model): bool
    {
        return $user->can('update_keberataninformasipublik');
    }

    public function delete(User $user, KeberatanInformasiPublik $model): bool
    {
        return $user->can('delete_keberataninformasipublik');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_keberataninformasipublik');
    }

    public function restore(User $user, KeberatanInformasiPublik $model): bool
    {
        return $user->can('restore_keberataninformasipublik');
    }

    public function forceDelete(User $user, KeberatanInformasiPublik $model): bool
    {
        return $user->can('force_delete_keberataninformasipublik');
    }
}