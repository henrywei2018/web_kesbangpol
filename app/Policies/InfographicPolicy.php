<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Infographic;
use Illuminate\Auth\Access\HandlesAuthorization;

class InfographicPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_infographic');
    }

    public function view(User $user, Infographic $model): bool
    {
        return $user->can('view_infographic');
    }

    public function create(User $user): bool
    {
        return $user->can('create_infographic');
    }

    public function update(User $user, Infographic $model): bool
    {
        return $user->can('update_infographic');
    }

    public function delete(User $user, Infographic $model): bool
    {
        return $user->can('delete_infographic');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_infographic');
    }

    public function restore(User $user, Infographic $model): bool
    {
        return $user->can('restore_infographic');
    }

    public function forceDelete(User $user, Infographic $model): bool
    {
        return $user->can('force_delete_infographic');
    }
}