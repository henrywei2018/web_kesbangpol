<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LaporATHG extends Model
{
    use HasFactory;

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

        return sprintf('ATHG-%s%s%04d', $year, $month, $newNumber);
    }

    // Configuration arrays for better organization
    public static function getBidangOptions(): array
    {
        return [
            'ekonomi' => [
                'label' => 'Ekonomi',
                'icon' => 'heroicon-o-banknotes',
                'color' => 'emerald',
                'description' => 'Ekonomi, perdagangan, investasi, UMKM'
            ],
            'budaya' => [
                'label' => 'Budaya',
                'icon' => 'heroicon-o-academic-cap',
                'color' => 'purple',
                'description' => 'Budaya, adat, tradisi, seni'
            ],
            'politik' => [
                'label' => 'Politik',
                'icon' => 'heroicon-o-building-library',
                'color' => 'blue',
                'description' => 'Politik, pemerintahan, kebijakan publik'
            ],
            'keamanan' => [
                'label' => 'Keamanan',
                'icon' => 'heroicon-o-shield-check',
                'color' => 'red',
                'description' => 'Keamanan, ketertiban, pertahanan'
            ],
            'lingkungan' => [
                'label' => 'Lingkungan',
                'icon' => 'heroicon-o-globe-alt',
                'color' => 'green',
                'description' => 'Lingkungan hidup, alam, konservasi'
            ],
            'kesehatan' => [
                'label' => 'Kesehatan',
                'icon' => 'heroicon-o-heart',
                'color' => 'pink',
                'description' => 'Kesehatan masyarakat, pandemi, kesehatan mental'
            ],
        ];
    }

    public static function getJenisATHGOptions(): array
    {
        return [
            'ancaman' => [
                'label' => 'Ancaman',
                'color' => 'danger',
                'description' => 'Hal yang berpotensi merugikan atau membahayakan'
            ],
            'tantangan' => [
                'label' => 'Tantangan', 
                'color' => 'warning',
                'description' => 'Kondisi yang memerlukan upaya khusus untuk diatasi'
            ],
            'hambatan' => [
                'label' => 'Hambatan',
                'color' => 'info',
                'description' => 'Faktor yang menghambat pencapaian tujuan'
            ],
            'gangguan' => [
                'label' => 'Gangguan',
                'color' => 'primary',
                'description' => 'Hal yang mengganggu stabilitas atau ketertiban'
            ],
        ];
    }

    public static function getTingkatUrgensiOptions(): array
    {
        return [
            'rendah' => [
                'label' => 'Rendah',
                'color' => 'success',
                'description' => 'Tidak mendesak, dapat ditangani secara rutin'
            ],
            'sedang' => [
                'label' => 'Sedang',
                'color' => 'warning',
                'description' => 'Perlu perhatian dalam waktu dekat'
            ],
            'tinggi' => [
                'label' => 'Tinggi',
                'color' => 'danger',
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

    // Helper methods
    public function getBidangInfo(): array
    {
        return self::getBidangOptions()[$this->bidang] ?? [];
    }

    public function getJenisInfo(): array
    {
        return self::getJenisATHGOptions()[$this->jenis_athg] ?? [];
    }

    public function getStatusInfo(): array
    {
        return self::getStatusOptions()[$this->status_athg] ?? [];
    }

    public function getTingkatUrgensiInfo(): array
    {
        return self::getTingkatUrgensiOptions()[$this->tingkat_urgensi] ?? [];
    }

    // Scopes
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $query->where('user_id', $userId);
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('tingkat_urgensi', ['tinggi', 'kritis']);
    }

    public function scopeByBidang($query, $bidang)
    {
        return $query->where('bidang', $bidang);
    }
}