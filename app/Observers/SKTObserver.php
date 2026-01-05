<?php

namespace App\Observers;

use App\Models\SKT;
use App\Models\SKL;
use App\Models\OrmasMaster;

class SKTObserver
{
    public function created(SKT $skt): void
    {
        OrmasMaster::createOrUpdateFromSKT($skt, 'belum_selesai');
    }

    public function updated(SKT $skt): void
    {
        $existingOrmas = OrmasMaster::where('skt_id', $skt->id)->first();
        
        if ($existingOrmas) {
            OrmasMaster::createOrUpdateFromSKT($skt, $existingOrmas->status_administrasi);
            
            // Update ORMAS status based on SKT status
            if ($skt->status === 'terbit') {
                $existingOrmas->markAsCompleted('SKT telah diterbitkan');
            } elseif ($skt->status === 'ditolak') {
                $existingOrmas->markAsIncomplete('SKT ditolak');
            }
        }
    }

    public function deleted(SKT $skt): void
    {
        \Log::info("SKTObserver::deleted called for SKT ID: {$skt->id}");
        
        // Hapus ORMAS yang terkait dengan SKT yang dihapus
        $ormas = OrmasMaster::where('skt_id', $skt->id)->first();
        if ($ormas) {
            \Log::info("Found ORMAS to cleanup: {$ormas->id}");
            
            // Cek apakah ORMAS ini juga terkait dengan SKL
            if ($ormas->skl_id) {
                // Jika masih ada SKL, hanya hapus relasi SKT
                $ormas->update([
                    'skt_id' => null,
                    'keterangan_status' => 'SKT terkait telah dihapus pada ' . now()->format('d/m/Y H:i'),
                    'sumber_registrasi' => 'skl' // Update sumber ke SKL
                ]);
                \Log::info("Updated ORMAS (kept SKL): {$ormas->id}");
            } else {
                // Jika tidak ada SKL, hapus ORMAS sepenuhnya
                $ormas->delete();
                \Log::info("Deleted ORMAS completely: {$ormas->id}");
            }
        } else {
            \Log::info("No ORMAS found for SKT ID: {$skt->id}");
        }
    }
}
