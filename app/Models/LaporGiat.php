<?php
// File: app/Models/LaporGiat.php - Updated with WhatsApp notification trait

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Traits\HasStatusUpdateNotifications;

class LaporGiat extends Model
{
    use HasFactory, HasStatusUpdateNotifications;

    protected $table = 'lapor_giat';

    protected $fillable = [
        'user_id',
        'nama_ormas',
        'ketua_nama_lengkap',
        'nomor_handphone',
        'tanggal_kegiatan',
        'laporan_kegiatan_path',
        'images_paths',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'images_paths' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    const STATUS_OPTIONS = [
        self::STATUS_PENDING => 'Menunggu Review',
        self::STATUS_APPROVED => 'Disetujui',
        self::STATUS_REJECTED => 'Ditolak',
    ];

    /**
     * Relationship with User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full URL for laporan kegiatan PDF
     */
    public function getLaporanKegiatanUrlAttribute(): ?string
    {
        if (!$this->laporan_kegiatan_path) {
            return null;
        }

        return route('lapor-giat.view-laporan', ['laporGiat' => $this->id]);
    }

    /**
     * Get full URLs for all images
     */
    public function getImageUrlsAttribute(): array
    {
        if (!$this->images_paths || !is_array($this->images_paths)) {
            return [];
        }

        return array_map(function ($path, $index) {
            return route('lapor-giat.view-image', ['laporGiat' => $this->id, 'imageIndex' => $index]);
        }, $this->images_paths, array_keys($this->images_paths));
    }

    /**
     * Check if has laporan file
     */
    public function hasLaporanFile(): bool
    {
        return !empty($this->laporan_kegiatan_path);
    }

    /**
     * Check if has images
     */
    public function hasImages(): bool
    {
        return !empty($this->images_paths) && is_array($this->images_paths) && count($this->images_paths) > 0;
    }

    /**
     * Get image count
     */
    public function getImageCountAttribute(): int
    {
        return $this->hasImages() ? count($this->images_paths) : 0;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::STATUS_OPTIONS[$this->status] ?? $this->status;
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by user (for public panel)
     */
    public function scopeForUser($query, ?int $userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeByDateRange($query, $startDate = null, $endDate = null)
    {
        if ($startDate) {
            $query->where('tanggal_kegiatan', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('tanggal_kegiatan', '<=', $endDate);
        }
        
        return $query;
    }

    /**
     * Delete associated files when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            // Delete PDF file
            if ($model->laporan_kegiatan_path && Storage::exists($model->laporan_kegiatan_path)) {
                Storage::delete($model->laporan_kegiatan_path);
            }

            // Delete image files
            if ($model->images_paths && is_array($model->images_paths)) {
                foreach ($model->images_paths as $imagePath) {
                    if (Storage::exists($imagePath)) {
                        Storage::delete($imagePath);
                    }
                }
            }
        });
    }
}