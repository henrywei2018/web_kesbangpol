<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SKT;

class Wilayah extends Model
{
    use HasFactory;

    // Specify the table name if it's not the default plural form
    protected $table = 'wilayah_indonesia';

    // Define fillable fields
    protected $fillable = ['kode', 'nama', 'level', 'parent_id'];

    // Define the parent-child relationship (self-referencing)
    public function parent()
    {
        return $this->belongsTo(Wilayah::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Wilayah::class, 'parent_id');
    }

    // Query scope to filter by level
    public function scopeOfLevel($query, $level)
    {
        return $query->where('level', $level);
    }
    public function skts()
{
    return $this->hasMany(SKT::class, 'wilayah_id');
}
}
