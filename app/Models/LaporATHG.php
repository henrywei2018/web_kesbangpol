<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\HasWhatsAppNotifications; // ADD THIS LINE

class LaporATHG extends Model
{
    use HasFactory, HasWhatsAppNotifications; // ADD HasWhatsAppNotifications

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

        // ADD THIS: Send WhatsApp notification when LaporATHG is created
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
}