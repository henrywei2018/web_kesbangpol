<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spt extends Model
{
    protected $table = 'spt';

    protected $fillable = [
        'nomor_spt',
        'kategori_perjalanan', 
        'tanggal_spt', 
        'perihal_spt',
        'tempat_berangkat', 
        'tempat_tujuan', 
        'tanggal_berangkat', 
        'tanggal_kembali',
        'kode_rekening', 
        'pengesah_id', 
        'status_spt', 
        'signature_data', 
        'status_pengesah',
        'generate_sppd',
    ];

    public $timestamps = true;

    // Casting fields to appropriate data types
    protected $casts = [
        'tanggal_spt' => 'date',
        'tanggal_berangkat' => 'date',
        'tanggal_kembali' => 'date',
        'pegawai' => 'array', // If this is a multiple select, cast it as an array
        'tempat_tujuan' => 'array', // If this is a multiple select, cast it as an array
        'signature_data' => 'string',
    ];

    // Boot method to handle default date values
    protected static function boot()
    {
    parent::boot();

    static::creating(function ($model) {
        // Set default dates if not provided
        $model->tanggal_spt = $model->tanggal_spt ?? now()->toDateString();
        $model->tanggal_berangkat = $model->tanggal_berangkat ?? now()->toDateString();
        $model->tanggal_kembali = $model->tanggal_kembali ?? now()->toDateString();

        // Generate nomor_spt if not provided
        if (!$model->nomor_spt) {
            $currentYear = now()->year;
            $countRows = Spt::whereYear('created_at', $currentYear)->count() + 1;
            $model->nomor_spt = sprintf(
                '094/%d/SPT/DKISP-set/%s/%s/%d',
                $countRows,
                strtoupper($model->kategori_perjalanan),
                self::monthToRoman(now()->month),
                $currentYear
            );
        }
    });
}
    public static function monthToRoman($month)
    {
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $romanMonths[$month] ?? '';
    }    
    public function pegawai()
    {
        return $this->belongsToMany(Pegawai::class, 'spt_pegawai', 'spt_id', 'pegawai_id')
                    ->withPivot('id','nomor_sppd')
                    ->withTimestamps();  // Include the pivot ID for 'spt_pegawai'
    }
    public function sppds()
    {
        return $this->hasMany(SptPegawai::class, 'spt_id', 'id');
    } 
    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'kode_rekening', 'id');
    }
    public function getPegawaiWithSppdAttribute()
    {
        return $this->pegawai->map(function ($pegawai) {
            return [
                'nama' => $pegawai->nama,
                'nomor_sppd' => $pegawai->pivot->nomor_sppd,
                'status' => $pegawai->pivot->status,
            ];
        });
    }
    public function pengesah()
    {
        return $this->belongsTo(Pegawai::class, 'pengesah_id');
    }
    public function signatures()
    {
        return $this->hasMany(Signature::class, 'id_spt');
    }

}
