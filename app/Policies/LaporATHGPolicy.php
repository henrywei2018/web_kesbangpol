<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LaporATHG;
use Illuminate\Auth\Access\HandlesAuthorization;

class LaporATHGPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('public') || $user->can('view_any_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LaporATHG $laporATHG): bool
    {
        return $user->hasRole('public') || $user->can('view_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('public') || $user->can('create_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LaporATHG $laporATHG): bool
    {
        return ($user->hasRole('public') && $laporATHG->user_id == $user->id) || $user->can('update_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LaporATHG $laporATHG): bool
    {
        return ($user->hasRole('public') && $laporATHG->user_id == $user->id) || $user->can('delete_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, LaporATHG $laporATHG): bool
    {
        return $user->can('force_delete_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, LaporATHG $laporATHG): bool
    {
        return $user->can('restore_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, LaporATHG $laporATHG): bool
    {
        return $user->can('replicate_lapor::a::t::h::g');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_lapor::a::t::h::g');
    }
}
