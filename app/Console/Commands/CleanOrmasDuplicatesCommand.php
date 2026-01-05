<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrmasMaster;
use App\Models\SKT;
use App\Models\SKL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanOrmasDuplicatesCommand extends Command
{
    protected $signature = 'ormas:clean-duplicates {--force : Force cleanup without confirmation}';
    protected $description = 'Clean duplicate ORMAS data automatically';

    public function handle()
    {
        try {
            $this->info('🔍 Memulai pembersihan duplikasi ORMAS...');
            
            // Clean ORMAS Master duplicates
            $cleanedOrmas = $this->cleanOrmasMasterDuplicates();
            
            // Clean empty names
            $cleanedEmpty = $this->cleanEmptyNames();
            
            $this->info("✅ Pembersihan selesai!");
            $this->info("📊 Hasil: {$cleanedOrmas} duplikat ORMAS dibersihkan, {$cleanedEmpty} nama kosong diperbaiki");
            
            // Log the results
            Log::info("ORMAS Duplicates Cleaned", [
                'duplicates_cleaned' => $cleanedOrmas,
                'empty_names_fixed' => $cleanedEmpty,
                'timestamp' => now()
            ]);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('ORMAS Cleanup Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function cleanOrmasMasterDuplicates()
    {
        $cleaned = 0;
        
        try {
            // Get duplicate groups using Eloquent
            $duplicateGroups = OrmasMaster::whereNotNull('nama_ormas')
                ->where('nama_ormas', '!=', '')
                ->get()
                ->groupBy(function($item) {
                    return strtolower(trim($item->nama_ormas));
                })
                ->filter(function($group) {
                    return $group->count() > 1;
                });

            foreach ($duplicateGroups as $normalizedName => $group) {
                $records = $group->sortBy('created_at'); // Keep the oldest record
                $keepRecord = $records->first();
                $deleteRecords = $records->skip(1);
                
                foreach ($deleteRecords as $record) {
                    $this->line("  Menghapus duplikat: {$record->nama_ormas} (ID: {$record->id})");
                    $record->delete();
                    $cleaned++;
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Error cleaning ORMAS duplicates: " . $e->getMessage());
        }
        
        return $cleaned;
    }

    private function cleanEmptyNames()
    {
        $cleaned = 0;
        
        try {
            // Update empty SKT names
            $updatedSKT = SKT::where(function($query) {
                    $query->whereNull('nama_ormas')
                          ->orWhere('nama_ormas', '')
                          ->orWhere('nama_ormas', 'like', '%   %');
                })
                ->update(['nama_ormas' => 'Nama Organisasi Tidak Diisi']);

            // Update empty SKL names
            $updatedSKL = SKL::where(function($query) {
                    $query->whereNull('nama_organisasi')
                          ->orWhere('nama_organisasi', '')
                          ->orWhere('nama_organisasi', 'like', '%   %');
                })
                ->update(['nama_organisasi' => 'Nama Organisasi Tidak Diisi']);

            $cleaned = $updatedSKT + $updatedSKL;
            
            if ($cleaned > 0) {
                $this->line("✅ Diperbaiki {$updatedSKT} record SKT dan {$updatedSKL} record SKL dengan nama kosong");
            }
            
        } catch (\Exception $e) {
            $this->error("Error cleaning empty names: " . $e->getMessage());
        }
        
        return $cleaned;
    }
}