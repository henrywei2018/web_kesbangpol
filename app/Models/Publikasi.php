<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Tags\HasTags;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Cviebrock\EloquentSluggable\Sluggable;

class Publikasi extends Model implements HasMedia
{
    use HasFactory, Sluggable, HasTags, InteractsWithMedia;

    protected $fillable = ['judul', 'kategori', 'deskripsi', 'slug', 'tanggal'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'judul'
            ]
        ];
    }
    public function scopeFilterByKategori($query, $kategori)
    {
        if ($kategori) {
            return $query->where('kategori', $kategori);
        }
        return $query;
    }

    public function getDocumentUrlAttribute()
    {
        return $this->getFirstMediaUrl('publikasi');
    }
}