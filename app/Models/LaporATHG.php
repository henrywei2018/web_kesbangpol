<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\HasStatusUpdateNotifications;

class LaporATHG extends Model
{
    use HasFactory, HasStatusUpdateNotifications;

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
                'label' => 'Pertahanan dan Keamanan',
                'color' => 'danger',
                'description' => 'Ancaman keamanan dan ketertiban umum'
            ],
            'ideologi' => [
                'label' => 'Ideologi',
                'color' => 'success',
                'description' => 'Munculnya paham-paham radikal yang mengancam persatuan dan kesatuan bangsa'
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

    // ===== FIXED INSTANCE METHODS (REQUIRED BY RESOURCE) =====

    /**
     * Get bidang information with color and label
     * FIXED: Proper null checking and type casting
     */
    public function getBidangInfo(): array
    {
        // Cast to string and handle null/empty values
        $bidang = (string) ($this->bidang ?? '');
        
        if (empty($bidang)) {
            return [
                'label' => 'Tidak Diketahui',
                'color' => 'gray',
                'description' => 'Bidang belum ditentukan'
            ];
        }
        
        $options = self::getBidangOptions();
        
        // Check if the bidang exists in options and is an array
        if (!isset($options[$bidang]) || !is_array($options[$bidang])) {
            return [
                'label' => ucfirst($bidang),
                'color' => 'gray',
                'description' => 'Bidang tidak valid'
            ];
        }
        
        return $options[$bidang];
    }

    /**
     * Get jenis ATHG information with color and label
     * FIXED: Proper null checking and type casting
     */
    public function getJenisInfo(): array
    {
        $jenis = (string) ($this->jenis_athg ?? '');
        
        if (empty($jenis)) {
            return [
                'label' => 'Tidak Diketahui',
                'color' => 'gray',
                'description' => 'Jenis ATHG belum ditentukan'
            ];
        }
        
        $options = self::getJenisATHGOptions();
        
        if (!isset($options[$jenis]) || !is_array($options[$jenis])) {
            return [
                'label' => ucfirst($jenis),
                'color' => 'gray',
                'description' => 'Jenis ATHG tidak valid'
            ];
        }
        
        return $options[$jenis];
    }

    /**
     * Get status information with color and label
     * FIXED: Proper null checking and type casting
     */
    public function getStatusInfo(): array
    {
        $status = (string) ($this->status_athg ?? '');
        
        if (empty($status)) {
            return [
                'label' => 'Tidak Diketahui',
                'color' => 'gray',
                'description' => 'Status belum ditentukan'
            ];
        }
        
        $options = self::getStatusOptions();
        
        if (!isset($options[$status]) || !is_array($options[$status])) {
            return [
                'label' => ucfirst(str_replace('_', ' ', $status)),
                'color' => 'gray',
                'description' => 'Status tidak valid'
            ];
        }
        
        return $options[$status];
    }

    /**
     * Get tingkat urgensi information with color and label
     * FIXED: Proper null checking and type casting
     */
    public function getTingkatUrgensiInfo(): array
    {
        $urgensi = (string) ($this->tingkat_urgensi ?? '');
        
        if (empty($urgensi)) {
            return [
                'label' => 'Tidak Diketahui',
                'color' => 'gray',
                'description' => 'Tingkat urgensi belum ditentukan'
            ];
        }
        
        $options = self::getTingkatUrgensiOptions();
        
        if (!isset($options[$urgensi]) || !is_array($options[$urgensi])) {
            return [
                'label' => ucfirst($urgensi),
                'color' => 'gray',
                'description' => 'Tingkat urgensi tidak valid'
            ];
        }
        
        return $options[$urgensi];
    }

    // ===== SCOPE METHODS =====

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