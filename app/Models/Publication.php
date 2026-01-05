<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publication extends Model implements HasMedia
{
    use InteractsWithMedia, HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'content', 'category_id', 'subcategory_id', 'publication_date', 'status'];

    // Relationship to category
    public function category()
    {
        return $this->belongsTo(PublicationCategory::class, 'category_id');
    }

    // Relationship to subcategory
    public function subcategory()
    {
        return $this->belongsTo(PublicationSubcategory::class, 'subcategory_id');
    }

    // Register media collections
    public function registerMediaCollections(): void
    {
        // Register default collection
        $this->addMediaCollection('default')
            ->useDisk('public');

        // If we have category/subcategory, register those collections too
        if ($this->subcategory) {
            $this->addMediaCollection($this->getCollectionName())
                ->useDisk('public');
        } elseif ($this->category) {
            $this->addMediaCollection($this->getCollectionName())
                ->useDisk('public');
        }
    }

    // Get the appropriate collection name
    public function getCollectionName(): string
    {
        if ($this->subcategory) {
            return str_replace(' ', '_', strtolower($this->subcategory->name));
        }
        return $this->category ? str_replace(' ', '_', strtolower($this->category->name)) : 'default';
    }

    // Helper method to get PDF URL regardless of collection
    public function getPdfUrl(): ?string
    {
        // Try subcategory collection first
        if ($this->subcategory) {
            $media = $this->getFirstMedia($this->getCollectionName());
            if ($media) return $media->getUrl();
        }

        // Try category collection next
        if ($this->category) {
            $media = $this->getFirstMedia($this->getCollectionName());
            if ($media) return $media->getUrl();
        }

        // Finally, try default collection
        $media = $this->getFirstMedia('default');
        return $media ? $media->getUrl() : null;
    }

    // Helper method to check if publication has PDF
    public function hasPdf(): bool
    {
        if ($this->subcategory) {
            if ($this->hasMedia($this->getCollectionName())) return true;
        }
        if ($this->category) {
            if ($this->hasMedia($this->getCollectionName())) return true;
        }
        return $this->hasMedia('default');
    }
}