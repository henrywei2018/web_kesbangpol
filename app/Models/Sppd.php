<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sppd extends Model
{
    protected $table = 'sppd';

    protected $fillable = ['spt_pegawai_id', 'nomor_sppd', 'tanggal_sppd', 'status'];

    public $timestamps = true;
    
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $casts = [
        'tanggal_sppd' => 'date',
        'status' => 'string',
    ];

    public function sptPegawai()
    {
        return $this->belongsTo(SptPegawai::class, 'spt_pegawai_id');
    }
}
