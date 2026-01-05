<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SKL;
use App\Traits\HasPublicOwnership;
use Illuminate\Auth\Access\HandlesAuthorization;

class SKLPolicy
{
    use HandlesAuthorization, HasPublicOwnership;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->canViewAny($user, 'view_any_s::k::l');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SKL $sKL): bool
    {
        return $this->canView($user, $sKL, 'view_s::k::l', 'id_pemohon');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->canCreate($user, 'create_s::k::l');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SKL $sKL): bool
    {
        return $this->canPerformAction($user, $sKL, 'update_s::k::l', 'id_pemohon');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SKL $sKL): bool
    {
        return $this->canPerformAction($user, $sKL, 'delete_s::k::l', 'id_pemohon');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_s::k::l');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, SKL $sKL): bool
    {
        return $user->can('force_delete_s::k::l');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_s::k::l');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, SKL $sKL): bool
    {
        return $user->can('restore_s::k::l');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_s::k::l');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, SKL $sKL): bool
    {
        return $user->can('replicate_s::k::l');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_s::k::l');
    }
}
