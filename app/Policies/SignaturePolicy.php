<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Signature;
use Illuminate\Auth\Access\HandlesAuthorization;

class SignaturePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_signature');
    }

    public function view(User $user, Signature $model): bool
    {
        return $user->can('view_signature');
    }

    public function create(User $user): bool
    {
        return $user->can('create_signature');
    }

    public function update(User $user, Signature $model): bool
    {
        return $user->can('update_signature');
    }

    public function delete(User $user, Signature $model): bool
    {
        return $user->can('delete_signature');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_signature');
    }

    public function restore(User $user, Signature $model): bool
    {
        return $user->can('restore_signature');
    }

    public function forceDelete(User $user, Signature $model): bool
    {
        return $user->can('force_delete_signature');
    }
}