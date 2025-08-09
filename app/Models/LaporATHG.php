<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\HasWhatsAppNotifications;

class LaporATHG extends Model
{
    use HasFactory, HasWhatsAppNotifications;

    protected $table = 'lapor_athg';

    protected $fillable = [
        'lapathg_id',
        'user_id',
        'bidang',
        'jenis_athg',
        'perihal',
        'tanggal',
        'lokasi',
        'deskripsi_singkat',
        'detail_kejadian',
        'sumber_informasi',
        'dampak_potensial',
        'nama_pelapor',
        'kontak_pelapor',
        'tingkat_urgensi',
        'status_athg',
        'catatan_admin',
        'tanggal_verifikasi',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_verifikasi' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Auto-generate ID on creation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->lapathg_id)) {
                $model->lapathg_id = self::generateLapathgId();
            }
        });

        // Send WhatsApp notification when LaporATHG is created
        static::created(function ($lapor) {
            $lapor->sendCreationNotification();
        });
    }

    public static function generateLapathgId(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        
        $lastRecord = self::where('lapathg_id', 'like', "ATHG-{$year}{$month}%")
            ->orderBy('lapathg_id', 'desc')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->lapathg_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf("ATHG-%s%s%04d", $year, $month, $newNumber);
    }

    // ===== STATIC OPTION METHODS (REQUIRED BY RESOURCE) =====

    public static function getBidangOptions(): array
    {
        return [
            'ekonomi' => [
                'label' => 'Ekonomi',
                'color' => 'success',
                'description' => 'Gangguan aktivitas ekonomi dan perdagangan'
            ],
            'budaya' => [
                'label' => 'Sosial Budaya',
                'color' => 'info',
                'description' => 'Konflik sosial dan budaya masyarakat'
            ],
            'politik' => [
                'label' => 'Politik',
                'color' => 'warning',
                'description' => 'Gangguan stabilitas politik dan pemerintahan'
            ],
            'keamanan' => [
                'label' => 'Keamanan',
                'color' => 'danger',
                'description' => 'Ancaman keamanan dan ketertiban umum'
            ],
            'lingkungan' => [
                'label' => 'Lingkungan',
                'color' => 'success',
                'description' => 'Kerusakan dan pencemaran lingkungan'
            ],
            'kesehatan' => [
                'label' => 'Kesehatan',
                'color' => 'primary',
                'description' => 'Ancaman terhadap kesehatan masyarakat'
            ],
        ];
    }

    public static function getJenisATHGOptions(): array
    {
        return [
            'ancaman' => [
                'label' => 'Ancaman',
                'color' => 'danger',
                'description' => 'Hal atau usaha yang bersifat mengubah atau merombak kebijaksanaan yang dilakukan secara konsepsional'
            ],
            'tantangan' => [
                'label' => 'Tantangan',
                'color' => 'warning',
                'description' => 'Hal atau usaha yang bersifat menggugah kemampuan'
            ],
            'hambatan' => [
                'label' => 'Hambatan',
                'color' => 'info',
                'description' => 'Hal atau usaha yang bersifat melemahkan atau menghalangi secara tidak konsepsional'
            ],
            'gangguan' => [
                'label' => 'Gangguan',
                'color' => 'primary',
                'description' => 'Hal atau usaha yang bersifat melemahkan atau menghalangi yang tidak konsepsional'
            ],
        ];
    }

    public static function getTingkatUrgensiOptions(): array
    {
        return [
            'rendah' => [
                'label' => 'Rendah',
                'color' => 'success',
                'description' => 'Tidak memerlukan tindakan segera'
            ],
            'sedang' => [
                'label' => 'Sedang',
                'color' => 'info',
                'description' => 'Perlu perhatian dalam waktu dekat'
            ],
            'normal' => [
                'label' => 'Normal',
                'color' => 'gray',
                'description' => 'Tingkat urgensi standar'
            ],
            'tinggi' => [
                'label' => 'Tinggi',
                'color' => 'warning',
                'description' => 'Memerlukan tindakan segera'
            ],
            'kritis' => [
                'label' => 'Kritis',
                'color' => 'danger',
                'description' => 'Situasi darurat, butuh respons immediate'
            ],
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => [
                'label' => 'Pending',
                'color' => 'gray',
                'description' => 'Laporan baru, belum diverifikasi'
            ],
            'verifikasi' => [
                'label' => 'Verifikasi',
                'color' => 'info',
                'description' => 'Sedang diverifikasi tim'
            ],
            'investigasi' => [
                'label' => 'Investigasi',
                'color' => 'warning',
                'description' => 'Dalam tahap investigasi'
            ],
            'tindak_lanjut' => [
                'label' => 'Tindak Lanjut',
                'color' => 'primary',
                'description' => 'Sedang dalam tindak lanjut'
            ],
            'selesai' => [
                'label' => 'Selesai',
                'color' => 'success',
                'description' => 'Laporan telah selesai ditangani'
            ],
            'ditolak' => [
                'label' => 'Ditolak',
                'color' => 'danger',
                'description' => 'Laporan ditolak karena tidak memenuhi kriteria'
            ],
        ];
    }

    // ===== INSTANCE METHODS (REQUIRED BY RESOURCE) =====

    /**
     * Get bidang information with color and label
     */
    public function getBidangInfo(): array
    {
        return self::getBidangOptions()[$this->bidang] ?? [
            'label' => ucfirst($this->bidang),
            'color' => 'gray',
            'description' => 'Bidang tidak diketahui'
        ];
    }

    /**
     * Get jenis ATHG information with color and label
     */
    public function getJenisInfo(): array
    {
        return self::getJenisATHGOptions()[$this->jenis_athg] ?? [
            'label' => ucfirst($this->jenis_athg),
            'color' => 'gray',
            'description' => 'Jenis tidak diketahui'
        ];
    }

    /**
     * Get status information with color and label
     */
    public function getStatusInfo(): array
    {
        return self::getStatusOptions()[$this->status_athg] ?? [
            'label' => ucfirst(str_replace('_', ' ', $this->status_athg)),
            'color' => 'gray',
            'description' => 'Status tidak diketahui'
        ];
    }

    /**
     * Get tingkat urgensi information with color and label
     */
    public function getTingkatUrgensiInfo(): array
    {
        return self::getTingkatUrgensiOptions()[$this->tingkat_urgensi] ?? [
            'label' => ucfirst($this->tingkat_urgensi),
            'color' => 'gray',
            'description' => 'Tingkat urgensi tidak diketahui'
        ];
    }

    // ===== ADDITIONAL HELPER METHODS =====

    /**
     * Check if the report is urgent
     */
    public function isUrgent(): bool
    {
        return in_array($this->tingkat_urgensi, ['tinggi', 'kritis']);
    }

    /**
     * Check if the report is critical
     */
    public function isCritical(): bool
    {
        return $this->tingkat_urgensi === 'kritis';
    }

    /**
     * Check if the report is completed
     */
    public function isCompleted(): bool
    {
        return in_array($this->status_athg, ['selesai', 'ditolak']);
    }

    /**
     * Check if the report is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this->status_athg, ['verifikasi', 'investigasi', 'tindak_lanjut']);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClass(): string
    {
        $statusInfo = $this->getStatusInfo();
        return match($statusInfo['color']) {
            'success' => 'bg-green-100 text-green-800',
            'danger' => 'bg-red-100 text-red-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'info' => 'bg-blue-100 text-blue-800',
            'primary' => 'bg-indigo-100 text-indigo-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get urgency badge class for UI
     */
    public function getUrgencyBadgeClass(): string
    {
        $urgencyInfo = $this->getTingkatUrgensiInfo();
        return match($urgencyInfo['color']) {
            'success' => 'bg-green-100 text-green-800',
            'danger' => 'bg-red-100 text-red-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'info' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // ===== QUERY SCOPES =====

    /**
     * Scope for filtering by user
     */
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for urgent reports
     */
    public function scopeUrgent($query)
    {
        return $query->whereIn('tingkat_urgensi', ['tinggi', 'kritis']);
    }

    /**
     * Scope for critical reports
     */
    public function scopeCritical($query)
    {
        return $query->where('tingkat_urgensi', 'kritis');
    }

    /**
     * Scope for filtering by bidang
     */
    public function scopeByBidang($query, $bidang)
    {
        return $query->where('bidang', $bidang);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_athg', $status);
    }

    /**
     * Scope for pending reports
     */
    public function scopePending($query)
    {
        return $query->where('status_athg', 'pending');
    }

    /**
     * Scope for completed reports
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status_athg', ['selesai', 'ditolak']);
    }

    /**
     * Scope for reports in progress
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn('status_athg', ['verifikasi', 'investigasi', 'tindak_lanjut']);
    }

    /**
     * Scope for recent reports (last 30 days)
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // ===== ACCESSORS =====

    /**
     * Get formatted tanggal attribute
     */
    public function getFormattedTanggalAttribute(): string
    {
        return $this->tanggal ? $this->tanggal->format('d F Y') : '-';
    }

    /**
     * Get time since created
     */
    public function getTimeSinceCreatedAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get days since created
     */
    public function getDaysSinceCreatedAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    // ===== MUTATORS =====

    /**
     * Set perihal attribute (capitalize first letter)
     */
    public function setPerihalAttribute($value)
    {
        $this->attributes['perihal'] = ucfirst($value);
    }

    /**
     * Set lokasi attribute (capitalize first letter)
     */
    public function setLokasiAttribute($value)
    {
        $this->attributes['lokasi'] = ucfirst($value);
    }
}