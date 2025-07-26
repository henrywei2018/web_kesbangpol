<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BannerCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class BannerCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_bannercategory');
    }

    public function view(User $user, BannerCategory $model): bool
    {
        return $user->can('view_bannercategory');
    }

    public function create(User $user): bool
    {
        return $user->can('create_bannercategory');
    }

    public function update(User $user, BannerCategory $model): bool
    {
        return $user->can('update_bannercategory');
    }

    public function delete(User $user, BannerCategory $model): bool
    {
        return $user->can('delete_bannercategory');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_bannercategory');
    }

    public function restore(User $user, BannerCategory $model): bool
    {
        return $user->can('restore_bannercategory');
    }

    public function forceDelete(User $user, BannerCategory $model): bool
    {
        return $user->can('force_delete_bannercategory');
    }
}