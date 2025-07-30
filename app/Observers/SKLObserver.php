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
        // Optionally handle when SKL is deleted
        $ormas = OrmasMaster::where('skl_id', $skl->id)->first();
        if ($ormas) {
            $ormas->update([
                'keterangan_status' => 'SKL telah dihapus pada ' . now()->format('d/m/Y H:i')
            ]);
        }
    }
}