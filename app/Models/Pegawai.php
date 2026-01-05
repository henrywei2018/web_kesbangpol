<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;
use Illuminate\Support\Str;

class Pegawai extends Model implements HasMedia
{
    use InteractsWithMedia;
    
    protected $table = 'pegawai';

    protected $fillable = ['nip', 'nama_pegawai', 'jabatan', 'pangkat_gologan', 'kontak'];

        
    public $timestamps = false;
    public function registerMediaCollections(): void
    {
        // Define a single file collection for the pegawai photo (same pattern as infographics)
        $this->addMediaCollection('pegawai_photos')
            ->singleFile() // Ensures only one file is kept, old file is deleted when a new one is added
            ->useFallbackUrl('/images/default-avatar.jpg') // Fallback if no media is present
            ->acceptsMimeTypes(['image/jpeg', 'image/png']); // Only allow JPEG and PNG files
    }

    public function registerMediaConversions(Media $media = null): void
    {
        // Same pattern as Infographic model
        $this->addMediaConversion('compressed')
            ->fit(Fit::Contain, 400, 400) // Square format for profile photos
            ->sharpen(10) // Sharpen the image
            ->quality(80) // Reduce image quality to 80% to compress
            ->nonQueued() // Process instantly without queuing
            ->performOnCollections('pegawai_photos'); // Apply to the 'pegawai_photos' collection

        // Define a smaller thumbnail conversion for faster loading in table views
        $this->addMediaConversion('thumb')
            ->width(150) // Set a smaller width for the thumbnail
            ->height(150) // Set a smaller height for the thumbnail
            ->keepOriginalImageFormat() // Keep the original format (e.g., JPEG/PNG)
            ->quality(60) // Lower quality for thumbnails to optimize size
            ->nonQueued() // Process instantly without queuing
            ->performOnCollections('pegawai_photos'); // Apply to the 'pegawai_photos' collection
    }

    public function setFileName(Media $media): string
    {
        return Str::random(8) . '_' . time() . '.' . $media->getExtensionFromMime($media->mime_type);
    }

    

    protected $casts = [
        'nip' => 'string',
        'kontak' => 'string',
    ];

    public function spts()
    {
        return $this->belongsToMany(Spt::class, 'spt_pegawai', 'pegawai_id', 'spt_id')
                    ->withPivot('id');  // Include the pivot ID for 'spt_pegawai'
    }

    // Define the one-to-many relationship with SPPD through 'spt_pegawai'
    public function sppds()
    {
        return $this->hasManyThrough(Sppd::class, 'spt_pegawai', 'pegawai_id', 'spt_pegawai_id', 'id', 'id');
    }

    public function konfigurasiAplikasiPengesah()
    {
        return $this->hasMany(KonfigurasiAplikasi::class, 'pengesah_spt_id');
    }

    public function konfigurasiAplikasiPa()
    {
        return $this->hasMany(KonfigurasiAplikasi::class, 'pa_id');
    }
    public function namapptk()
    {
        return $this->hasMany(Rekening::class, 'pptk','id');
    }
    public function signatures()
    {
        return $this->hasMany(Signature::class, 'id_pegawai');
    }
    
}
