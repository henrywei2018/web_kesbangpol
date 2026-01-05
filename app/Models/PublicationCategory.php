<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationCategory extends Model
{
    protected $fillable = ['name', 'slug'];

    // Relasi ke sub kategori
    public function subcategories()
    {
        return $this->hasMany(PublicationSubcategory::class, 'category_id');
    }
}
