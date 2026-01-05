<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SptPegawai extends Model
{
    protected $table = 'spt_pegawai';

    protected $fillable = ['spt_id', 'pegawai_id'];

    public $timestamps = true;

    public function spt()
    {
        return $this->belongsTo(Spt::class, 'spt_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
    public function signatures()
    {
        return $this->hasMany(Signature::class, 'id_sppd');
    }
}
