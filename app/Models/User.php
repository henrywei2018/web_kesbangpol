<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasAvatar, HasName, HasMedia
{
    use InteractsWithMedia;
    use HasUuids, HasRoles;
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'firstname',
        'lastname',
        'password',
        'no_ktp',
        'domisili',
        'alamat',
        'no_telepon',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'no_ktp'=> 'hashed',
    ];

    public function getFilamentName(): string
    {
        return $this->username;
    }

    public function canAccessPanel(Panel $panel): bool
    {   
        // Admin panel - untuk super_admin, admin, editor
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['super_admin', 'admin', 'editor']) && $this->hasVerifiedEmail();
        }

        // Public panel - untuk role public
        if ($panel->getId() === 'public') {
            return $this->hasRole('public') && $this->hasVerifiedEmail();
        }
        
        return false;
    }

    public function otpVerifications()
    {
        return $this->hasMany(OtpVerification::class, 'email', 'email');
    }
    public function getLatestOtp(string $type = 'registration')
    {
        return $this->otpVerifications()
                   ->where('type', $type)
                   ->latest()
                   ->first();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->getMedia('avatars')?->first()?->getUrl() ?? $this->getMedia('avatars')?->first()?->getUrl('thumb') ?? null;
    }

    public function getKtpImageUrl(): ?string
    {
    // Try to get the first media file from 'ktp_image' collection
        return $this->getMedia('ktp_image')?->first()?->getUrl() 
            ?? $this->getMedia('ktp_image')?->first()?->getUrl('thumb') 
            ?? null;
    }

    // Define an accessor for the 'name' attribute
    public function getNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(config('filament-shield.super_admin.name'));
    }

    public function registerMediaConversions(Media|null $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
    public function skls()
    {
        return $this->hasMany(SKL::class, 'id_pemohon');
    }

    /**
     * Relationship with SKT model  
     * User can have many SKT submissions
     */
    public function skts()
    {
        return $this->hasMany(SKT::class, 'id_pemohon');
    }


    /**
     * Relationship with PermohonanInformasiPublik model
     * User can have many public information requests
     */
    public function permohonanInformasiPubliks()
    {
        return $this->hasMany(PermohonanInformasiPublik::class, 'id_pemohon');
    }

    /**
     * Relationship with KeberatanInformasiPublik model
     * User can have many public information objections
     */
    public function keberatanInformasiPubliks()
    {
        return $this->hasMany(KeberatanInformasiPublik::class, 'id_pemohon');
    }
    public function laporGiats()
    {
        return $this->hasMany(LaporGiat::class);
    }

    // Relasi ke komentar (komentar yang ditulis oleh user)
    public function komentars()
    {
        return $this->hasMany(AduanKomentar::class);
    }
    public function getWhatsAppPhoneAttribute(): ?string
    {
        if (!$this->no_telepon) {
            return null;
        }

        // Format phone number to international format
        $phone = preg_replace('/[^0-9]/', '', $this->no_telepon);
        
        if (str_starts_with($phone, '08')) {
            return '628' . substr($phone, 2);
        } elseif (str_starts_with($phone, '8')) {
            return '62' . $phone;
        } elseif (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            return '62' . $phone;
        }

        return $phone;
    }
    public function hasWhatsAppPhone(): bool
    {
        $phone = $this->getWhatsAppPhoneAttribute();
        return $phone && strlen($phone) >= 10 && strlen($phone) <= 15;
    }
}
