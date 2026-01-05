<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StatusLayanan;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;
use App\Traits\HasStatusUpdateNotifications;


class KeberatanInformasiPublik extends Model implements HasMedia
{
    use HasFactory, HasStatusUpdateNotifications, InteractsWithMedia;

    protected $table = 'keberatan_informasi_publik';
    
    protected $fillable = [
        'id_pemohon',
        'permohonan_id',
        'nomor_registrasi',
        'nik_no_identitas',
        'no_telp',
        'pekerjaan',
        'alamat',
        'rincian_informasi',
        'tujuan_keberatan',
        'content_surat',
        'alasan_keberatan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');  // Assuming a foreign key of 'user_id'
    }

    public function permohonan()
    {
        return $this->belongsTo(PermohonanInformasiPublik::class, 'permohonan_id');
    }

    public function statuses()
    {
        return $this->morphMany(StatusLayanan::class, 'layanan')->orderBy('created_at', 'desc');
    }
    public function getCustomStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'diproses' => 'Diproses',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
        ];
    }

    public function getLatestStatusAttribute()
    {
        $latestStatus = $this->statuses->first();
        return $latestStatus ? $latestStatus->status : 'Pending';
    }

    public function getLatestDeskripsiStatusAttribute()
    {
        $latestStatus = $this->statuses->first();
        return $latestStatus ? $latestStatus->deskripsi_status : '';
    }
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('keberatan-docs')
            ->acceptsMimeTypes(['application/pdf'])  // Limit accepted file types if necessary
            ->useDisk('public');  // Use the 'public' disk for file storage
    }
    public function scopeForUser($query)
    {
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            return $query;
        }
        return $query->where('id_pemohon', auth()->user()->id);
    }
    protected static function boot()
    {
        parent::boot();

        static::created(function ($keberatan) {
            $keberatan->sendCreationNotification();
        });
    }
    
}