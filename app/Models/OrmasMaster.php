<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OrmasMaster extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $table = 'ormas_master';

    protected $fillable = [
        'nomor_registrasi',
        'nama_ormas',
        'nama_singkatan_ormas',
        'status_administrasi',
        'keterangan_status',
        'tanggal_selesai_administrasi',
        'skt_id',
        'skl_id',
        'sumber_registrasi',
        'tempat_pendirian',
        'tanggal_pendirian',
        'bidang_kegiatan',
        'ciri_khusus',
        'tujuan_ormas',
        'alamat_sekretariat',
        'provinsi',
        'kab_kota',
        'kode_pos',
        'nomor_handphone',
        'nomor_fax',
        'email',
        'nomor_akta_notaris',
        'tanggal_akta_notaris',
        'jenis_akta',
        'nomor_npwp',
        'nama_bank',
        'nomor_rekening_bank',
        'ketua_nama_lengkap',
        'ketua_nik',
        'ketua_masa_bakti_akhir',
        'sekretaris_nama_lengkap',
        'sekretaris_nik',
        'sekretaris_masa_bakti_akhir',
        'bendahara_nama_lengkap',
        'bendahara_nik',
        'bendahara_masa_bakti_akhir',
        'nama_pendiri',
        'nik_pendiri',
        'nama_pembina',
        'nik_pembina',
        'nama_penasihat',
        'nik_penasihat',
        'created_by',
        'updated_by',
        'first_registered_at',
        'last_updated_from_source_at',
    ];

    protected $casts = [
        'tanggal_pendirian' => 'date',
        'tanggal_akta_notaris' => 'date',
        'ketua_masa_bakti_akhir' => 'date',
        'sekretaris_masa_bakti_akhir' => 'date',
        'bendahara_masa_bakti_akhir' => 'date',
        'tanggal_selesai_administrasi' => 'datetime',
        'first_registered_at' => 'datetime',
        'last_updated_from_source_at' => 'datetime',
        'nama_pendiri' => 'json',
        'nik_pendiri' => 'json',
        'nama_pembina' => 'json',
        'nik_pembina' => 'json',
        'nama_penasihat' => 'json',
        'nik_penasihat' => 'json',
    ];

    /**
     * Boot method to auto-generate nomor_registrasi
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_registrasi)) {
                $model->nomor_registrasi = $model->generateNomorRegistrasi();
            }
            
            if (empty($model->first_registered_at)) {
                $model->first_registered_at = now();
            }
        });
    }

    /**
     * Generate nomor registrasi: ORM-KALTARA-YYYY-NNNN
     */
    public function generateNomorRegistrasi(): string
    {
        $year = date('Y');
        $prefix = "ORM-KALTARA-{$year}-";
        
        $lastNumber = static::where('nomor_registrasi', 'like', $prefix . '%')
            ->orderBy('nomor_registrasi', 'desc')
            ->first();
            
        if ($lastNumber) {
            $lastNum = (int) substr($lastNumber->nomor_registrasi, -4);
            $newNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNum = '0001';
        }
        
        return $prefix . $newNum;
    }

    /**
     * Relationship dengan SKT
     */
    public function skt()
    {
        return $this->belongsTo(SKT::class, 'skt_id');
    }

    /**
     * Relationship dengan SKL (jika ada)
     */
    public function skl()
    {
        return $this->belongsTo(SKL::class, 'skl_id');
    }

    /**
     * Relationship dengan User (creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship dengan User (updater)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Create or update ORMAS master dari SKT
     */
    public static function createOrUpdateFromSKT(SKT $skt, string $status = 'belum_selesai')
    {
        $data = [
            'nama_ormas' => $skt->nama_ormas,
            'nama_singkatan_ormas' => $skt->nama_singkatan_ormas,
            'sumber_registrasi' => 'skt',
            'skt_id' => $skt->id,
            'tempat_pendirian' => $skt->tempat_pendirian,
            'tanggal_pendirian' => $skt->tanggal_pendirian,
            'bidang_kegiatan' => $skt->bidang_kegiatan,
            'ciri_khusus' => $skt->ciri_khusus,
            'tujuan_ormas' => $skt->tujuan_ormas,
            'alamat_sekretariat' => $skt->alamat_sekretariat,
            'provinsi' => $skt->provinsi,
            'kab_kota' => $skt->kab_kota,
            'kode_pos' => $skt->kode_pos,
            'nomor_handphone' => $skt->nomor_handphone,
            'nomor_fax' => $skt->nomor_fax,
            'email' => $skt->email,
            'nomor_akta_notaris' => $skt->nomor_akta_notaris,
            'tanggal_akta_notaris' => $skt->tanggal_akta_notaris,
            'jenis_akta' => $skt->jenis_akta,
            'nomor_npwp' => $skt->nomor_npwp,
            'nama_bank' => $skt->nama_bank,
            'nomor_rekening_bank' => $skt->nomor_rekening_bank,
            'ketua_nama_lengkap' => $skt->ketua_nama_lengkap,
            'ketua_nik' => $skt->ketua_nik,
            'ketua_masa_bakti_akhir' => $skt->ketua_masa_bakti_akhir,
            'sekretaris_nama_lengkap' => $skt->sekretaris_nama_lengkap,
            'sekretaris_nik' => $skt->sekretaris_nik,
            'sekretaris_masa_bakti_akhir' => $skt->sekretaris_masa_bakti_akhir,
            'bendahara_nama_lengkap' => $skt->bendahara_nama_lengkap,
            'bendahara_nik' => $skt->bendahara_nik,
            'bendahara_masa_bakti_akhir' => $skt->bendahara_masa_bakti_akhir,
            'nama_pendiri' => $skt->nama_pendiri,
            'nik_pendiri' => $skt->nik_pendiri,
            'nama_pembina' => $skt->nama_pembina,
            'nik_pembina' => $skt->nik_pembina,
            'nama_penasihat' => $skt->nama_penasihat,
            'nik_penasihat' => $skt->nik_penasihat,
            'last_updated_from_source_at' => now(),
        ];

        // Update status jika selesai
        if ($status === 'selesai') {
            $data['status_administrasi'] = 'selesai';
            $data['tanggal_selesai_administrasi'] = now();
        }

        return static::updateOrCreate(
            ['skt_id' => $skt->id],
            $data
        );
    }

    /**
     * Create or update ORMAS master dari SKL
     */
    public static function createOrUpdateFromSKL($skl, string $status = 'belum_selesai')
    {
        // Implementasi sesuai dengan struktur SKL
        // Sesuaikan dengan field yang ada di SKL
        $data = [
            'nama_ormas' => $skl->nama_organisasi ?? $skl->nama_ormas,
            'sumber_registrasi' => 'skl',
            'skl_id' => $skl->id,
            'email' => $skl->email_organisasi ?? $skl->email,
            'last_updated_from_source_at' => now(),
        ];

        // Update status jika selesai
        if ($status === 'selesai') {
            $data['status_administrasi'] = 'selesai';
            $data['tanggal_selesai_administrasi'] = now();
        }

        return static::updateOrCreate(
            ['skl_id' => $skl->id],
            $data
        );
    }

    /**
     * Mark as completed administration
     */
    public function markAsCompleted(string $keterangan = null)
    {
        $this->update([
            'status_administrasi' => 'selesai',
            'tanggal_selesai_administrasi' => now(),
            'keterangan_status' => $keterangan,
        ]);
    }

    /**
     * Mark as incomplete administration
     */
    public function markAsIncomplete(string $keterangan = null)
    {
        $this->update([
            'status_administrasi' => 'belum_selesai',
            'tanggal_selesai_administrasi' => null,
            'keterangan_status' => $keterangan,
        ]);
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeCompleted($query)
    {
        return $query->where('status_administrasi', 'selesai');
    }

    public function scopeIncomplete($query)
    {
        return $query->where('status_administrasi', 'belum_selesai');
    }

    public function scopeFromSKT($query)
    {
        return $query->where('sumber_registrasi', 'skt');
    }

    public function scopeFromSKL($query)
    {
        return $query->where('sumber_registrasi', 'skl');
    }
}