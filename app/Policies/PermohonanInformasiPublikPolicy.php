<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PermohonanInformasiPublik;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermohonanInformasiPublikPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_permohonaninformasipublik');
    }

    public function view(User $user, PermohonanInformasiPublik $model): bool
    {
        return $user->can('view_permohonaninformasipublik');
    }

    public function create(User $user): bool
    {
        return $user->can('create_permohonaninformasipublik');
    }

    public function update(User $user, PermohonanInformasiPublik $model): bool
    {
        return $user->can('update_permohonaninformasipublik');
    }

    public function delete(User $user, PermohonanInformasiPublik $model): bool
    {
        return $user->can('delete_permohonaninformasipublik');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_permohonaninformasipublik');
    }

    public function restore(User $user, PermohonanInformasiPublik $model): bool
    {
        return $user->can('restore_permohonaninformasipublik');
    }

    public function forceDelete(User $user, PermohonanInformasiPublik $model): bool
    {
        return $user->can('force_delete_permohonaninformasipublik');
    }
}