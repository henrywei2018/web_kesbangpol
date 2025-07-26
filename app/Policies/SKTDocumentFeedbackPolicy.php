<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SKTDocumentFeedback;
use Illuminate\Auth\Access\HandlesAuthorization;

class SKTDocumentFeedbackPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sktdocumentfeedback');
    }

    public function view(User $user, SKTDocumentFeedback $model): bool
    {
        return $user->can('view_sktdocumentfeedback');
    }

    public function create(User $user): bool
    {
        return $user->can('create_sktdocumentfeedback');
    }

    public function update(User $user, SKTDocumentFeedback $model): bool
    {
        return $user->can('update_sktdocumentfeedback');
    }

    public function delete(User $user, SKTDocumentFeedback $model): bool
    {
        return $user->can('delete_sktdocumentfeedback');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_sktdocumentfeedback');
    }

    public function restore(User $user, SKTDocumentFeedback $model): bool
    {
        return $user->can('restore_sktdocumentfeedback');
    }

    public function forceDelete(User $user, SKTDocumentFeedback $model): bool
    {
        return $user->can('force_delete_sktdocumentfeedback');
    }
}