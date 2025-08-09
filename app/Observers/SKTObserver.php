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
        $ormas = OrmasMaster::where('skt_id', $skt->id)->first();
        if ($ormas) {
            $ormas->update([
                'keterangan_status' => 'SKT telah dihapus pada ' . now()->format('d/m/Y H:i')
            ]);
        }
    }
}
