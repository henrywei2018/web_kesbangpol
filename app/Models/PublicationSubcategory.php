<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationSubcategory extends Model
{
    protected $fillable = ['name', 'slug', 'category_id'];

    // Relasi ke kategori utama
    public function category()
    {
        return $this->belongsTo(PublicationCategory::class, 'category_id');
    }

    // Relasi ke publikasi
    public function publications()
    {
        return $this->hasMany(Publication::class, 'subcategory_id');
    }
}