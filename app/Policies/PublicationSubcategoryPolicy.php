<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PublicationSubcategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class PublicationSubcategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_publicationsubcategory');
    }

    public function view(User $user, PublicationSubcategory $model): bool
    {
        return $user->can('view_publicationsubcategory');
    }

    public function create(User $user): bool
    {
        return $user->can('create_publicationsubcategory');
    }

    public function update(User $user, PublicationSubcategory $model): bool
    {
        return $user->can('update_publicationsubcategory');
    }

    public function delete(User $user, PublicationSubcategory $model): bool
    {
        return $user->can('delete_publicationsubcategory');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_publicationsubcategory');
    }

    public function restore(User $user, PublicationSubcategory $model): bool
    {
        return $user->can('restore_publicationsubcategory');
    }

    public function forceDelete(User $user, PublicationSubcategory $model): bool
    {
        return $user->can('force_delete_publicationsubcategory');
    }
}