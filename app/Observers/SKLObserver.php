<?php

namespace App\Observers;

use App\Models\SKL;
use App\Models\OrmasMaster;

class SKLObserver
{
    /**
     * Handle the SKL "created" event.
     */
    public function created(SKL $skl): void
    {
        // Automatically register to ORMAS master when SKL is created
        OrmasMaster::createOrUpdateFromSKL($skl, 'belum_selesai');
    }

    /**
     * Handle the SKL "updated" event.
     */
    public function updated(SKL $skl): void
    {
        // Update ORMAS master data when SKL is updated
        $existingOrmas = OrmasMaster::where('skl_id', $skl->id)->first();
        
        if ($existingOrmas) {
            OrmasMaster::createOrUpdateFromSKL($skl, $existingOrmas->status_administrasi);
        }
    }

    /**
     * Handle the SKL "deleted" event.
     */
    public function deleted(SKL $skl): void
    {
        // Hapus ORMAS yang terkait dengan SKL yang dihapus
        $ormas = OrmasMaster::where('skl_id', $skl->id)->first();
        if ($ormas) {
            // Cek apakah ORMAS ini juga terkait dengan SKT
            if ($ormas->skt_id) {
                // Jika masih ada SKT, hanya hapus relasi SKL
                $ormas->update([
                    'skl_id' => null,
                    'keterangan_status' => 'SKL terkait telah dihapus pada ' . now()->format('d/m/Y H:i'),
                    'sumber_registrasi' => 'skt' // Update sumber ke SKT
                ]);
            } else {
                // Jika tidak ada SKT, hapus ORMAS sepenuhnya
                $ormas->delete();
            }
        }
    }
}