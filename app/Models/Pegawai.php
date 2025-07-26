<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Pegawai extends Model
{
    protected $table = 'pegawai';

    protected $fillable = ['nip', 'nama_pegawai', 'jabatan', 'pangkat_gologan', 'kontak'];

        
    public $timestamps = false;
    

    protected $casts = [
        'nip' => 'string',
        'kontak' => 'string',
    ];

    public function spts()
    {
        return $this->belongsToMany(Spt::class, 'spt_pegawai', 'pegawai_id', 'spt_id')
                    ->withPivot('id');  // Include the pivot ID for 'spt_pegawai'
    }

    // Define the one-to-many relationship with SPPD through 'spt_pegawai'
    public function sppds()
    {
        return $this->hasManyThrough(Sppd::class, 'spt_pegawai', 'pegawai_id', 'spt_pegawai_id', 'id', 'id');
    }

    public function konfigurasiAplikasiPengesah()
    {
        return $this->hasMany(KonfigurasiAplikasi::class, 'pengesah_spt_id');
    }

    public function konfigurasiAplikasiPa()
    {
        return $this->hasMany(KonfigurasiAplikasi::class, 'pa_id');
    }
    public function namapptk()
    {
        return $this->hasMany(Rekening::class, 'pptk','id');
    }
    public function signatures()
    {
        return $this->hasMany(Signature::class, 'id_pegawai');
    }
    
}
