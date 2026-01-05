<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KodeInstansi extends Model
{
    protected $table = 'kode_instansi';

    protected $fillable = ['nama_instansi'];

    public $timestamps = true;

    protected $casts = [
        'nama_instansi' => 'string',
    ];

    public function rekenings()
    {
        return $this->hasMany(Rekening::class, 'kode_instansi_id');
    }
}
