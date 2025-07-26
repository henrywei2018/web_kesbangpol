<?php

// app/Models/Portfolio.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Cviebrock\EloquentSluggable\Sluggable;

class Galeri extends Model implements HasMedia
{
    use HasTags, InteractsWithMedia, Sluggable;

    protected $fillable = ['judul', 'kategori', 'deskripsi','images', 'slug'];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'judul'
            ]
        ];
    }
    protected $casts = [
        'images' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}