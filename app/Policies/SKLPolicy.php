<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SKL;
use Illuminate\Auth\Access\HandlesAuthorization;

class SKLPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        // Allow access for super_admin, admin, editor, and public users
        return $user->hasAnyRole(['super_admin', 'admin', 'editor', 'public']);
    }

    public function view(User $user, SKL $model): bool
    {
        // Super admin can view all
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        // Public users can only view their own records
        if ($user->hasRole('public')) {
            return $model->id_pemohon === $user->id;
        }
        
        // Admin and editor can view all
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function create(User $user): bool
    {
        // All authenticated users can create
        return $user->hasAnyRole(['super_admin', 'admin', 'editor', 'public']);
    }

    public function update(User $user, SKL $model): bool
    {
        // Super admin can update all
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        // Public users can only update their own records if status allows
        if ($user->hasRole('public')) {
            // Only allow updates if status is 'pengajuan' or 'perbaikan'
            return $model->id_pemohon === $user->id && 
                   in_array($model->status, ['pengajuan', 'perbaikan']);
        }
        
        // Admin and editor can update all
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function delete(User $user, SKL $model): bool
    {
        // Only super admin can delete
        return $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, SKL $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, SKL $model): bool
    {
        return $user->hasRole('super_admin');
    }
}