<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KonfigurasiAplikasi;
use Illuminate\Auth\Access\HandlesAuthorization;

class KonfigurasiAplikasiPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_konfigurasiaplikasi');
    }

    public function view(User $user, KonfigurasiAplikasi $model): bool
    {
        return $user->can('view_konfigurasiaplikasi');
    }

    public function create(User $user): bool
    {
        return $user->can('create_konfigurasiaplikasi');
    }

    public function update(User $user, KonfigurasiAplikasi $model): bool
    {
        return $user->can('update_konfigurasiaplikasi');
    }

    public function delete(User $user, KonfigurasiAplikasi $model): bool
    {
        return $user->can('delete_konfigurasiaplikasi');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_konfigurasiaplikasi');
    }

    public function restore(User $user, KonfigurasiAplikasi $model): bool
    {
        return $user->can('restore_konfigurasiaplikasi');
    }

    public function forceDelete(User $user, KonfigurasiAplikasi $model): bool
    {
        return $user->can('force_delete_konfigurasiaplikasi');
    }
}