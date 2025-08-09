<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StatusLayanan;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia; 
use App\Traits\HasStatusUpdateNotifications;


class PermohonanInformasiPublik extends Model implements HasMedia
{
    use HasFactory, HasStatusUpdateNotifications, InteractsWithMedia;    
    protected $table = 'permohonan_informasi_publik';
    protected $fillable = [
        'id_pemohon',
        'nomor_register',
        'nik_no_identitas',
        'ktp_path',
        'alamat',
        'no_telp',
        'pekerjaan',
        'tujuan_penggunaan_informasi',
        'rincian_informasi',
        'cara_memperoleh_informasi',
        'mendapatkan_salinan_informasi',
        'cara_mendapatkan_salinan',
        'content_surat',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }
    protected static function boot()
    {
        parent::boot();
    }
    public function statuses()
    {
        return $this->morphMany(StatusLayanan::class, 'layanan')
            ->latest('created_at')
            ->take(1);
    }
    public function getLatestStatusAttribute()
    {
        $latestStatus = $this->statuses->first();  // Since it's already ordered in descending order
        return $latestStatus ? $latestStatus->status : 'Pending';
    }

    public function getLatestDeskripsiStatusAttribute()
    {
        $latestStatus = $this->statuses->first();  // No need to call latest() again
        return $latestStatus ? $latestStatus->deskripsi_status : '';
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('statuses');
    }
    public function scopeForUser($query)
    {
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            return $query;
        }
        return $query->where('id_pemohon', auth()->user()->id);
    }
}
