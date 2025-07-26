<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusLayanan extends Model
{
    protected $table = 'mt_status';
    protected $fillable = ['status', 'deskripsi_status', 'layanan_id', 'layanan_type'];
    public function layanan()
    {
        return $this->morphTo();
    }
}
