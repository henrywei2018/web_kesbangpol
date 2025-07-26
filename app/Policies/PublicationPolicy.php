<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Publication;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_publication');
    }

    public function view(User $user, Publication $model): bool
    {
        return $user->can('view_publication');
    }

    public function create(User $user): bool
    {
        return $user->can('create_publication');
    }

    public function update(User $user, Publication $model): bool
    {
        return $user->can('update_publication');
    }

    public function delete(User $user, Publication $model): bool
    {
        return $user->can('delete_publication');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_publication');
    }

    public function restore(User $user, Publication $model): bool
    {
        return $user->can('restore_publication');
    }

    public function forceDelete(User $user, Publication $model): bool
    {
        return $user->can('force_delete_publication');
    }
}