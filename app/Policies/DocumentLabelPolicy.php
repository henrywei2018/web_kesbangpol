<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DocumentLabel;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentLabelPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_documentlabel');
    }

    public function view(User $user, DocumentLabel $model): bool
    {
        return $user->can('view_documentlabel');
    }

    public function create(User $user): bool
    {
        return $user->can('create_documentlabel');
    }

    public function update(User $user, DocumentLabel $model): bool
    {
        return $user->can('update_documentlabel');
    }

    public function delete(User $user, DocumentLabel $model): bool
    {
        return $user->can('delete_documentlabel');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_documentlabel');
    }

    public function restore(User $user, DocumentLabel $model): bool
    {
        return $user->can('restore_documentlabel');
    }

    public function forceDelete(User $user, DocumentLabel $model): bool
    {
        return $user->can('force_delete_documentlabel');
    }
}