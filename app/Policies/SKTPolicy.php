<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SKT;
use Illuminate\Auth\Access\HandlesAuthorization;

class SKTPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'editor', 'public']);
    }

    public function view(User $user, SKT $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        if ($user->hasRole('public')) {
            return $model->id_pemohon === $user->id;
        }
        
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'editor', 'public']);
    }

    public function update(User $user, SKT $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        if ($user->hasRole('public')) {
            return $model->id_pemohon === $user->id && 
                   in_array($model->status, ['pengajuan', 'perbaikan']);
        }
        
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function delete(User $user, SKT $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, SKT $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, SKT $model): bool
    {
        return $user->hasRole('super_admin');
    }
}