<?php

namespace App\Observers;

use App\Models\SKT;
use App\Models\SKL;
use App\Models\OrmasMaster;

class SKTObserver
{
    /**
     * Handle the SKT "created" event.
     */
    public function created(SKT $skt): void
    {
        // Automatically register to ORMAS master when SKT is created
        OrmasMaster::createOrUpdateFromSKT($skt, 'belum_selesai');
    }

    /**
     * Handle the SKT "updated" event.
     */
    public function updated(SKT $skt): void
    {
        // Update ORMAS master data when SKT is updated
        $existingOrmas = OrmasMaster::where('skt_id', $skt->id)->first();
        
        if ($existingOrmas) {
            OrmasMaster::createOrUpdateFromSKT($skt, $existingOrmas->status_administrasi);
        }
    }

    /**
     * Handle the SKT "deleted" event.
     */
    public function deleted(SKT $skt): void
    {
        // Optionally handle when SKT is deleted
        // You might want to keep the ORMAS master record for historical purposes
        // Or mark it as inactive
        $ormas = OrmasMaster::where('skt_id', $skt->id)->first();
        if ($ormas) {
            $ormas->update([
                'keterangan_status' => 'SKT telah dihapus pada ' . now()->format('d/m/Y H:i')
            ]);
        }
    }
}
