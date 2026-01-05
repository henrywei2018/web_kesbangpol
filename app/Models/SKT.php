<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\SKTDocumentLabel;
use App\Models\SKTDocumentFeedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\HasStatusUpdateNotifications;

class SKT extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory, HasStatusUpdateNotifications;

    // The table associated with the model
    protected $table = 'skts';
    const STATUS_PENGAJUAN = 'pengajuan';

    // The attributes that are mass assignable
    protected $fillable = [
        
        //formulir permohonan
        'id_pemohon',
        'jenis_permohonan',
        'nama_ormas',
        'nama_singkatan_ormas',
        'tempat_pendirian',
        'tanggal_pendirian',
        'nomor_surat_permohonan',
        'tanggal_surat_permohonan',

        //Data Umum Organisasi
        'bidang_kegiatan',
        'ciri_khusus', //enum/select options > Keagamaan/Kewanitaan/Kepemudaan/Kesamaan Profesi/Kesamaan Kegiatan/Kesamaan Bidang/Mitra K/L
        'tujuan_ormas',
        'alamat_sekretariat',
        'provinsi', //query tb wilayah prov
        'kab_kota', //query tb wilayah kab
        'kode_pos', 
        'nomor_handphone',
        'nomor_fax',
        'email',

        //Data Legal Organisasi
        'nomor_akta_notaris',
        'tanggal_akta_notaris',
        'jenis_akta', //enum select options > akta pendirian/akta perubahan
        'nomor_npwp',
        'nama_bank',
        'nomor_rekening_bank',

        //data struktur organisasi
        'ketua_nama_lengkap',
        'ketua_nik',
        'ketua_masa_bakti_akhir',
        'sekretaris_nama_lengkap',
        'sekretaris_nik',
        'sekretaris_masa_bakti_akhir',
        'bendahara_nama_lengkap',
        'bendahara_nik',
        'bendahara_masa_bakti_akhir',

        // data tambahan
        'nama_pendiri',
        'nik_pendiri',
        'nama_pembina',
        'nik_pembina',
        'nama_penasihat',
        'nik_penasihat'

    ];
    protected $casts = [
        'nama_pendiri' => 'json',
        'nik_pendiri' => 'json',
        'nama_pembina' => 'json',
        'nik_pembina' => 'json',
        'nama_penasihat' => 'json',
        'nik_penasihat' => 'json'
    ];
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Dapatkan id dari user yang sedang login dan set nilai id_pemohon
        $data['id_pemohon'] = Auth::id(); // atau auth()->user()->id;

        return $data;
    }
    public function getCustomStatusOptions(): array
    {
        return [
            'pengajuan' => 'Pengajuan',
            'perbaikan' => 'Perbaikan',
            'diproses' => 'Diproses',
            'terbit' => 'Terbit',
            'ditolak' => 'Ditolak',
        ];
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150);
    }
    public function registerMediaCollections(): void
    {
        // Retrieve document labels from cache to avoid N+1 queries
        $sktdocumentLabels = Cache::remember('skt_document_labels', 3600, function () {
            return SKTDocumentLabel::all();
        });

        // Dynamically register each document as a media collection
        foreach ($sktdocumentLabels as $label) {
            $this->addMediaCollection($label->collection_name)
                 ->singleFile(); // Single file upload for each collection
        }
    }
    public function addDocument($collectionName, $file)
    {
        $this->addMedia($file)->toMediaCollection($collectionName);
    }
    public function getDocumentUrl($collectionName)
    {
        $media = $this->getFirstMedia($collectionName);
        return $media ? $media->getUrl() : null;
    }
    public function sktdocumentFeedbacks()
    {
        return $this->hasMany(SKTDocumentFeedback::class, 'skt_id');
    }
    public function scopeForUser($query)
    {
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            return $query;
        }
        return $query->where('id_pemohon', auth()->user()->id);
    }
    public function user()
    {
        return $this->belongsTo(User::class,'id_pemohon');
    }

    /**
     * Relationship dengan OrmasMaster
     */
    public function ormasMaster()
    {
        return $this->hasOne(OrmasMaster::class, 'skt_id');
    }
    public function getAvatarUrl(): string
    {
        $avatarUrl = $this->user?->getFilamentAvatarUrl();
        if (!$avatarUrl) {
            return asset('assets/img/defaultavatar.png');
        }
        
        // Clean up the URL if needed
        $avatarUrl = str_replace(config('app.url'), '', $avatarUrl);
        return $avatarUrl;
    }
    protected static function boot()
    {
        parent::boot();
    }
    

}
