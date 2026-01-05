<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    protected $table = 'signatures';
    protected $fillable = ['spt_id', 'pegawai_id', 'signed_path', 'signed_as'];
    public function spt()
    {
        return $this->belongsTo(Spt::class, 'spt_id');
    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}