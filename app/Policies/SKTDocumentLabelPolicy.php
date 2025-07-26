<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SKTDocumentLabel;
use Illuminate\Auth\Access\HandlesAuthorization;

class SKTDocumentLabelPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sktdocumentlabel');
    }

    public function view(User $user, SKTDocumentLabel $model): bool
    {
        return $user->can('view_sktdocumentlabel');
    }

    public function create(User $user): bool
    {
        return $user->can('create_sktdocumentlabel');
    }

    public function update(User $user, SKTDocumentLabel $model): bool
    {
        return $user->can('update_sktdocumentlabel');
    }

    public function delete(User $user, SKTDocumentLabel $model): bool
    {
        return $user->can('delete_sktdocumentlabel');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_sktdocumentlabel');
    }

    public function restore(User $user, SKTDocumentLabel $model): bool
    {
        return $user->can('restore_sktdocumentlabel');
    }

    public function forceDelete(User $user, SKTDocumentLabel $model): bool
    {
        return $user->can('force_delete_sktdocumentlabel');
    }
}