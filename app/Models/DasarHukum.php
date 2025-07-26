<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DasarHukum extends Model
{
    protected $table = 'dasar_hukum';

    protected $fillable = ['tahun', 'deskripsi'];

    public $timestamps = true;

    protected $casts = [
        'tahun' => 'integer',
    ];

    // Relationships and additional constraints can be defined here if needed
}
