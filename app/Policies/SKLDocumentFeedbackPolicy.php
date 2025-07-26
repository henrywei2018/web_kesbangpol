<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SKLDocumentFeedback;
use Illuminate\Auth\Access\HandlesAuthorization;

class SKLDocumentFeedbackPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_skldocumentfeedback');
    }

    public function view(User $user, SKLDocumentFeedback $model): bool
    {
        return $user->can('view_skldocumentfeedback');
    }

    public function create(User $user): bool
    {
        return $user->can('create_skldocumentfeedback');
    }

    public function update(User $user, SKLDocumentFeedback $model): bool
    {
        return $user->can('update_skldocumentfeedback');
    }

    public function delete(User $user, SKLDocumentFeedback $model): bool
    {
        return $user->can('delete_skldocumentfeedback');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_skldocumentfeedback');
    }

    public function restore(User $user, SKLDocumentFeedback $model): bool
    {
        return $user->can('restore_skldocumentfeedback');
    }

    public function forceDelete(User $user, SKLDocumentFeedback $model): bool
    {
        return $user->can('force_delete_skldocumentfeedback');
    }
}