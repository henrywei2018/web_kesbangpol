<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'user_id',
        'otp_code',
        'type',
        'attempts',
        'is_verified',
        'expires_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Alternative relation by email (useful when user doesn't exist yet)
    public function userByEmail()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                    ->where('is_verified', false)
                    ->where('attempts', '<', 3);
    }

    public function scopeForEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // Methods
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function canAttempt(): bool
    {
        return $this->attempts < 3 && !$this->isExpired() && !$this->is_verified;
    }

    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    public function markAsVerified(): void
    {
        $this->update([
            'is_verified' => true,
        ]);
    }

    public function getRemainingTime(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        return now()->diffInSeconds($this->expires_at, false);
    }

    public function linkToUser(User $user): void
    {
        if ($this->email === $user->email && !$this->user_id) {
            $this->update(['user_id' => $user->id]);
        }
    }
}