<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $table = 'rekening';

    // Allow mass assignment for these fields
    protected $fillable = ['tahun', 'nama_rekening', 'nomor_rekening', 'jenis_rekening', 'kode_instansi_id', 'pptk'];

    public $timestamps = true;

    // Define field types
    protected $casts = [
        'tahun' => 'integer',
        'nomor_rekening' => 'string',
    ];

    // Relationship to KodeInstansi model
    public function kodeInstansi()
    {
        return $this->belongsTo(KodeInstansi::class, 'kode_instansi_id');
    }

    // Relationship to Pegawai model for pptk
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pptk', 'id');
    }


    public function getCombinedLabelAttribute()
    {
        $pegawaiName = $this->pegawai?->nama_pegawai ?? 'Unknown';

        return "{$this->nomor_rekening}-{$this->nama_rekening} - {$pegawaiName}";
    }
    
}
