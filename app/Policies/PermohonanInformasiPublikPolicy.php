<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PermohonanInformasiPublik;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermohonanInformasiPublikPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('public') || $user->can('view_any_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PermohonanInformasiPublik $permohonanInformasiPublik): bool
    {
        return $user->hasRole('public') || $user->can('view_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('public') || $user->can('create_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PermohonanInformasiPublik $permohonanInformasiPublik): bool
    {
        return ($user->hasRole('public') && $permohonanInformasiPublik->user_id == $user->id) || $user->can('update_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PermohonanInformasiPublik $permohonanInformasiPublik): bool
    {
        return ($user->hasRole('public') && $permohonanInformasiPublik->user_id == $user->id) || $user->can('delete_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, PermohonanInformasiPublik $permohonanInformasiPublik): bool
    {
        return $user->can('force_delete_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, PermohonanInformasiPublik $permohonanInformasiPublik): bool
    {
        return $user->can('restore_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, PermohonanInformasiPublik $permohonanInformasiPublik): bool
    {
        return $user->can('replicate_permohonan::informasi::publik');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_permohonan::informasi::publik');
    }
}
