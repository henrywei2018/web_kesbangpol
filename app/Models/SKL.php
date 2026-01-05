<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\DocumentLabel;
use App\Models\SKLDocumentFeedback;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasStatusUpdateNotifications;

class SKL extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasStatusUpdateNotifications;
    protected $table = 'skls';
    const STATUS_PENGAJUAN = 'pengajuan';

    protected $fillable = [
        'id_pemohon', 
        'email_organisasi',
        'jenis_permohonan',
        'nama_organisasi',
        'nama_ketua',
        'nomor_hp',
        'status', // Status of the submission (pending, approved, etc.)
    ];

    // Dynamically register media collections based on document labels
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
    public function registerMediaCollections(): void
    {
        // Retrieve document labels from the database
        $documentLabels = DocumentLabel::all();

        // Dynamically register each document as a media collection
        foreach ($documentLabels as $label) {
            $this->addMediaCollection($label->collection_name)
                 ->singleFile(); // Single file upload for each collection
        }
    }
    
    public function getPathForMedia(Media $media): string
    {
        // Mengambil collection_name dari media dan menyesuaikan path
        return "uploads/skl/{$this->id_pemohon}/{$media->collection_name}/";
    }
    public function determineMediaPath(Media $media): string
    {
        // Tentukan path khusus berdasarkan collection_name dan id pemohon (atau atribut lainnya)
        return "uploads/skl/{$this->id_pemohon}/{$media->collection_name}/{$media->file_name}";
    }

    public function scopeForUser($query)
    {
        if (auth()->check() && auth()->user()->hasRole('super_admin')) {
            return $query;
        }
        return $query->where('id_pemohon', auth()->user()->id);
    }
    public function documentFeedbacks()
    {
        return $this->hasMany(SKLDocumentFeedback::class, 'skl_id');
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
        return $this->hasOne(OrmasMaster::class, 'skl_id');
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
