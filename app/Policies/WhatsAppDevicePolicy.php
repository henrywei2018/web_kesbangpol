<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhatsAppDevice;
use Illuminate\Auth\Access\HandlesAuthorization;

class WhatsAppDevicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_whatsappdevice');
    }

    public function view(User $user, WhatsAppDevice $model): bool
    {
        return $user->can('view_whatsappdevice');
    }

    public function create(User $user): bool
    {
        return $user->can('create_whatsappdevice');
    }

    public function update(User $user, WhatsAppDevice $model): bool
    {
        return $user->can('update_whatsappdevice');
    }

    public function delete(User $user, WhatsAppDevice $model): bool
    {
        return $user->can('delete_whatsappdevice');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_whatsappdevice');
    }

    public function restore(User $user, WhatsAppDevice $model): bool
    {
        return $user->can('restore_whatsappdevice');
    }

    public function forceDelete(User $user, WhatsAppDevice $model): bool
    {
        return $user->can('force_delete_whatsappdevice');
    }
}