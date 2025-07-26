<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\Fit;
use Illuminate\Support\Str;
use Cviebrock\EloquentSluggable\Sluggable;

class Infographic extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['judul', 'kategori', 'deskripsi', 'slug'];

    /**
     * Register media collection for infographics images.
     */
    public function registerMediaCollections(): void
    {
        // Define a single file collection for the infographic
        $this->addMediaCollection('infographics')
            ->singleFile() // Ensures only one file is kept, old file is deleted when a new one is added
            ->useFallbackUrl('/images/default-infographic.jpg') // Fallback if no media is present
            ->acceptsMimeTypes(['image/jpeg', 'image/png']); // Only allow JPEG and PNG files
    }

    public function registerMediaConversions(Media $media = null): void
{
    // Resize the image while maintaining the aspect ratio
    $this->addMediaConversion('compressed')
        ->fit(Fit::Contain, 800, 2000) // Resize to 800x2000, keeping aspect ratio
        ->sharpen(10) // Sharpen the image
        ->quality(80) // Reduce image quality to 80% to compress
        ->nonQueued() // Process instantly without queuing
        ->performOnCollections('infographics'); // Apply to the 'infographics' collection

    // Define a smaller thumbnail conversion for faster loading in edit views
    $this->addMediaConversion('thumb')
        ->width(200) // Set a smaller width for the thumbnail
        ->height(500) // Set a smaller height for the thumbnail
        ->keepOriginalImageFormat() // Keep the original format (e.g., JPEG/PNG)
        ->quality(60) // Lower quality for thumbnails to optimize size
        ->nonQueued() // Process instantly without queuing
        ->performOnCollections('infographics'); // Apply to the 'infographics' collection
}

    public function setFileName(Media $media): string
    {
        return Str::random(8) . '_' . $media->file_name; // Prefix filename with a unique string
    }

    public function deleteMedia(): void
    {
        $this->clearMediaCollection('infographics');
    }
    public static function boot()
    {
        parent::boot();

        // Generate slug automatically when creating a new record
        static::creating(function ($model) {
            $model->slug = Str::slug($model->judul);
        });
        // Update slug if title is updated
        static::updating(function ($model) {
            if ($model->isDirty('title')) {
                $model->slug = Str::slug($model->judul);
            }
        });

    }
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'judul'
            ]
        ];
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
