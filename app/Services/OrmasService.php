<?php

namespace App\Services;

use App\Models\OrmasMaster;
use App\Models\SKT;
use App\Models\SKL;
use App\Models\SKTDocumentFeedback;
use App\Models\SKLDocumentFeedback;

class OrmasService
{
    /**
     * Check if SKT process is completed and update ORMAS status
     */
    public function checkAndUpdateSKTStatus(SKT $skt): void
    {
        // Check if SKT document feedback indicates completion
        $feedback = SKTDocumentFeedback::where('skt_id', $skt->id)->first();
        
        if ($feedback) {
            // Check if all required documents are approved/completed
            // You can customize this logic based on your business rules
            $isCompleted = $this->isSKTProcessCompleted($feedback);
            
            $ormas = OrmasMaster::where('skt_id', $skt->id)->first();
            
            if ($ormas) {
                if ($isCompleted) {
                    $ormas->markAsCompleted('Proses SKT telah selesai');
                } else {
                    $ormas->markAsIncomplete('Proses SKT masih dalam tahap penyelesaian');
                }
            }
        }
    }

    /**
     * Check if SKL process is completed and update ORMAS status
     */
    public function checkAndUpdateSKLStatus(SKL $skl): void
    {
        // Check if SKL document feedback indicates completion
        $feedback = SKLDocumentFeedback::where('skl_id', $skl->id)->first();
        
        if ($feedback) {
            $isCompleted = $this->isSKLProcessCompleted($feedback);
            
            $ormas = OrmasMaster::where('skl_id', $skl->id)->first();
            
            if ($ormas) {
                if ($isCompleted) {
                    $ormas->markAsCompleted('Proses SKL telah selesai');
                } else {
                    $ormas->markAsIncomplete('Proses SKL masih dalam tahap penyelesaian');
                }
            }
        }
    }

    /**
     * Determine if SKT process is completed
     * Customize this logic based on your business requirements
     */
    private function isSKTProcessCompleted(SKTDocumentFeedback $feedback): bool
    {
        // Example logic - customize based on your needs
        // Check if status indicates completion, or all documents are approved, etc.
        
        // You might check:
        // - If status is 'approved' or 'selesai'
        // - If all required documents have been submitted and approved
        // - If final certificate/letter has been issued
        
        // Example implementation:
        if (isset($feedback->status) && in_array($feedback->status, ['approved', 'selesai', 'diterima'])) {
            return true;
        }

        // Or check if feedback indicates completion
        if (isset($feedback->keterangan) && 
            str_contains(strtolower($feedback->keterangan), 'selesai') ||
            str_contains(strtolower($feedback->keterangan), 'disetujui') ||
            str_contains(strtolower($feedback->keterangan), 'diterima')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if SKL process is completed
     */
    private function isSKLProcessCompleted(SKLDocumentFeedback $feedback): bool
    {
        // Similar logic for SKL
        if (isset($feedback->status) && in_array($feedback->status, ['approved', 'selesai', 'diterima'])) {
            return true;
        }

        if (isset($feedback->keterangan) && 
            str_contains(strtolower($feedback->keterangan), 'selesai') ||
            str_contains(strtolower($feedback->keterangan), 'disetujui') ||
            str_contains(strtolower($feedback->keterangan), 'diterima')) {
            return true;
        }

        return false;
    }

    /**
     * Get ORMAS statistics
     */
    public function getOrmasStatistics(): array
    {
        return [
            'total_ormas' => OrmasMaster::count(),
            'selesai_administrasi' => OrmasMaster::completed()->count(),
            'belum_selesai_administrasi' => OrmasMaster::incomplete()->count(),
            'dari_skt' => OrmasMaster::fromSKT()->count(),
            'dari_skl' => OrmasMaster::fromSKL()->count(),
            'per_kabupaten' => OrmasMaster::groupBy('kab_kota')
                ->selectRaw('kab_kota, count(*) as total')
                ->pluck('total', 'kab_kota'),
            'per_ciri_khusus' => OrmasMaster::groupBy('ciri_khusus')
                ->selectRaw('ciri_khusus, count(*) as total')
                ->pluck('total', 'ciri_khusus'),
        ];
    }

    /**
     * Manually mark ORMAS as completed
     */
    public function markOrmasCompleted(string $ormasId, string $keterangan = null): bool
    {
        $ormas = OrmasMaster::find($ormasId);
        
        if ($ormas) {
            $ormas->markAsCompleted($keterangan ?? 'Ditandai selesai secara manual');
            return true;
        }
        
        return false;
    }

    /**
     * Manually mark ORMAS as incomplete
     */
    public function markOrmasIncomplete(string $ormasId, string $keterangan = null): bool
    {
        $ormas = OrmasMaster::find($ormasId);
        
        if ($ormas) {
            $ormas->markAsIncomplete($keterangan ?? 'Ditandai belum selesai secara manual');
            return true;
        }
        
        return false;
    }

    /**
     * Sync all existing SKT to ORMAS master
     * Useful for initial migration
     */
    public function syncAllSKTToOrmas(): int
    {
        $skts = SKT::all();
        $count = 0;
        
        foreach ($skts as $skt) {
            OrmasMaster::createOrUpdateFromSKT($skt, 'belum_selesai');
            
            // Check if should be marked as completed
            $this->checkAndUpdateSKTStatus($skt);
            
            $count++;
        }
        
        return $count;
    }

    /**
     * Sync all existing SKL to ORMAS master
     */
    public function syncAllSKLToOrmas(): int
    {
        $skls = SKL::all();
        $count = 0;
        
        foreach ($skls as $skl) {
            OrmasMaster::createOrUpdateFromSKL($skl, 'belum_selesai');
            
            // Check if should be marked as completed
            $this->checkAndUpdateSKLStatus($skl);
            
            $count++;
        }
        
        return $count;
    }
}