<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AduanKomentar;
use Illuminate\Auth\Access\HandlesAuthorization;

class AduanKomentarPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_aduankomentar');
    }

    public function view(User $user, AduanKomentar $model): bool
    {
        return $user->can('view_aduankomentar');
    }

    public function create(User $user): bool
    {
        return $user->can('create_aduankomentar');
    }

    public function update(User $user, AduanKomentar $model): bool
    {
        return $user->can('update_aduankomentar');
    }

    public function delete(User $user, AduanKomentar $model): bool
    {
        return $user->can('delete_aduankomentar');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_aduankomentar');
    }

    public function restore(User $user, AduanKomentar $model): bool
    {
        return $user->can('restore_aduankomentar');
    }

    public function forceDelete(User $user, AduanKomentar $model): bool
    {
        return $user->can('force_delete_aduankomentar');
    }
}