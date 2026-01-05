<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonfigurasiAplikasi extends Model
{
    protected $table = 'konfigurasi_aplikasi';

    protected $fillable = ['pengesah_spt_id', 'pa_id', 'skpd', 'lokasi_asal'];

    public $timestamps = true;

    public function pengesah()
    {
        return $this->belongsTo(Pegawai::class, 'pengesah_spt_id');
    }

    public function pa()
    {
        return $this->belongsTo(Pegawai::class, 'pa_id');
    }

}
