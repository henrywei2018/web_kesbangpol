<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SKT;
use App\Traits\HasPublicOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;

class SKTPolicy
{
    use HandlesAuthorization, HasPublicOwnership;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('public') || $user->can('view_any_s::k::t');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SKT $sKT): bool
    {
        return $user->hasRole('public') || $user->can('view_s::k::t');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('public') || $user->can('create_s::k::t');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SKT $sKT): bool
    {
        return $this->canPerformAction($user, $sKT, 'update_s::k::t', 'id_pemohon');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SKT $sKT): bool
    {
        return $this->canPerformAction($user, $sKT, 'delete_s::k::t', 'id_pemohon');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_s::k::t');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, SKT $sKT): bool
    {
        return $user->can('force_delete_s::k::t');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_s::k::t');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, SKT $sKT): bool
    {
        return $user->can('restore_s::k::t');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_s::k::t');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, SKT $sKT): bool
    {
        return $user->can('replicate_s::k::t');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_s::k::t');
    }
}
