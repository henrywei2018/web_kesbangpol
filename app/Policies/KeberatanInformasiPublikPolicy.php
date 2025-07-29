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
        return $user->hasAnyRole(['super_admin', 'admin', 'editor', 'public']);
    }

    public function view(User $user, KeberatanInformasiPublik $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        if ($user->hasRole('public')) {
            return $model->user_id === $user->id;
        }
        
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'editor', 'public']);
    }

    public function update(User $user, KeberatanInformasiPublik $model): bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }
        
        if ($user->hasRole('public')) {
            $latestStatus = $model->statuses()->latest()->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            
            return $model->user_id === $user->id && $status === 'Pending';
        }
        
        return $user->hasAnyRole(['admin', 'editor']);
    }

    public function delete(User $user, KeberatanInformasiPublik $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function restore(User $user, KeberatanInformasiPublik $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function forceDelete(User $user, KeberatanInformasiPublik $model): bool
    {
        return $user->hasRole('super_admin');
    }
}