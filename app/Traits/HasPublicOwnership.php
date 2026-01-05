<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait HasPublicOwnership
{
    /**
     * Check if user can perform action based on ownership and roles
     */
    protected function canPerformAction(User $user, Model $model, string $permission, ?string $ownershipField = 'user_id'): bool
    {
        // Super admin dapat mengakses semua
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        // User public hanya dapat mengakses milik sendiri
        if ($user->hasRole('public') && $ownershipField && $model->{$ownershipField} == $user->id) {
            return true;
        }
        
        // User dengan permission khusus dapat mengakses
        return $user->can($permission);
    }

    /**
     * Check if user can view any records
     */
    protected function canViewAny(User $user, string $permission): bool
    {
        return $user->hasRole(['public', 'super_admin']) || $user->can($permission);
    }

    /**
     * Check if user can create records
     */
    protected function canCreate(User $user, string $permission): bool
    {
        return $user->hasRole(['public', 'super_admin']) || $user->can($permission);
    }

    /**
     * Check if user can view specific record
     */
    protected function canView(User $user, Model $model, string $permission, ?string $ownershipField = 'user_id'): bool
    {
        // Super admin dan public dapat melihat
        if ($user->hasRole(['public', 'super_admin'])) {
            return true;
        }
        
        return $user->can($permission);
    }
}