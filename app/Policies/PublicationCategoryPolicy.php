<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PublicationCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicationCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_publicationcategory');
    }

    public function view(User $user, PublicationCategory $model): bool
    {
        return $user->can('view_publicationcategory');
    }

    public function create(User $user): bool
    {
        return $user->can('create_publicationcategory');
    }

    public function update(User $user, PublicationCategory $model): bool
    {
        return $user->can('update_publicationcategory');
    }

    public function delete(User $user, PublicationCategory $model): bool
    {
        return $user->can('delete_publicationcategory');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_publicationcategory');
    }

    public function restore(User $user, PublicationCategory $model): bool
    {
        return $user->can('restore_publicationcategory');
    }

    public function forceDelete(User $user, PublicationCategory $model): bool
    {
        return $user->can('force_delete_publicationcategory');
    }
}