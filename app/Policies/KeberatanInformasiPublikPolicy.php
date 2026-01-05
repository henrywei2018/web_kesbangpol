<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KeberatanInformasiPublik;
use Illuminate\Auth\Access\HandlesAuthorization;

class KeberatanInformasiPublikPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('public') || $user->can('view_any_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KeberatanInformasiPublik $keberatanInformasiPublik): bool
    {
        return $user->hasRole('public') || $user->can('view_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('public') || $user->can('create_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KeberatanInformasiPublik $keberatanInformasiPublik): bool
    {
        return ($user->hasRole('public') && $keberatanInformasiPublik->user_id == $user->id) || $user->can('update_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KeberatanInformasiPublik $keberatanInformasiPublik): bool
    {
        return ($user->hasRole('public') && $keberatanInformasiPublik->user_id == $user->id) || $user->can('delete_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, KeberatanInformasiPublik $keberatanInformasiPublik): bool
    {
        return $user->can('force_delete_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, KeberatanInformasiPublik $keberatanInformasiPublik): bool
    {
        return $user->can('restore_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, KeberatanInformasiPublik $keberatanInformasiPublik): bool
    {
        return $user->can('replicate_keberatan::informasi::publik');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_keberatan::informasi::publik');
    }
}
