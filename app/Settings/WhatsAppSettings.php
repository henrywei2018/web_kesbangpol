<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class WhatsAppSettings extends Settings
{
    public bool $enabled;
    public ?string $api_url;
    public ?string $token;
    
    // Admin phone numbers
    public ?string $admin_main;
    public ?string $admin_backup;
    public ?string $admin_skt;
    public ?string $admin_skl;
    public ?string $admin_ppid;
    public ?string $admin_athg;

    public static function group(): string
    {
        return 'whatsapp';
    }
}