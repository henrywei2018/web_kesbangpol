<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\HasStatusUpdateNotifications; // ✅ Add this trait

class LaporATHG extends Model
{
    use HasFactory, HasStatusUpdateNotifications; // ✅ Use the trait

    protected $table = 'lapor_athg';

    protected $fillable = [
        'lapathg_id',
        'user_id',
        'bidang',
        'perihal',
        'tanggal',
        'lokasi',
        'detail_kejadian',
        'sumber_informasi',
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

    // ✅ ADD custom status options for ATHG
    public function getCustomStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'verifikasi' => 'Verifikasi',
            'investigasi' => 'Investigasi',
            'tindak_lanjut' => 'Tindak Lanjut',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
        ];
    }
    protected function getStatusAttributeName(): string
    {
        return 'status_athg';
    }

    protected function getKeteranganFieldName(): string
    {
        return 'catatan_admin'; // LaporATHG uses 'catatan_admin' instead of 'keterangan'
    }

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
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "ATHG-{$year}{$month}{$newNumber}";
    }

    // Status badge methods for Filament
    public function getStatusBadgeColor(): string
    {
        return match ($this->status_athg) {
            'pending' => 'warning',
            'verifikasi' => 'info',
            'investigasi' => 'primary',
            'tindak_lanjut' => 'success',
            'selesai' => 'success',
            'ditolak' => 'danger',
            default => 'gray',
        };
    }

    public function getUrgensiBadgeColor(): string
    {
        return match ($this->tingkat_urgensi) {
            'rendah' => 'success',
            'sedang' => 'warning',
            'tinggi' => 'danger',
            default => 'gray',
        };
    }

    // Static methods for form options
    public static function getBidangOptions(): array
    {
        return [
            'ekonomi' => [
                'label' => 'Ekonomi',
                'color' => 'green',
                'description' => 'Perdagangan, investasi, keuangan, UMKM'
            ],
            'budaya' => [
                'label' => 'Budaya',
                'color' => 'blue',
                'description' => 'Adat, tradisi, seni, bahasa daerah'
            ],
            'politik' => [
                'label' => 'Politik',
                'color' => 'red',
                'description' => 'Pemerintahan, kebijakan, demokrasi'
            ],
            'keamanan' => [
                'label' => 'Keamanan',
                'color' => 'orange',
                'description' => 'Ketertiban, kriminalitas, konflik'
            ],
            'lingkungan' => [
                'label' => 'Lingkungan',
                'color' => 'emerald',
                'description' => 'Alam, pencemaran, konservasi'
            ],
            'kesehatan' => [
                'label' => 'Kesehatan',
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
        ];
    }

    // Scope for user access
    public function scopeForUser($query)
    {
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            return $query;
        }
        return $query->where('user_id', auth()->user()->id);
    }
}