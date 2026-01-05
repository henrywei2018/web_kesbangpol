<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrmasMaster;
use App\Models\SKT;
use App\Models\SKL;
use Illuminate\Support\Facades\DB;

class DetectOrmasDuplicatesCommand extends Command
{
    protected $signature = 'ormas:detect-duplicates {--cleanup : Automatically merge or cleanup duplicates} {--dry-run : Show what would be done without making changes}';
    protected $description = 'Detect and optionally cleanup duplicate ORMAS data across SKT, SKL, and OrmasMaster tables';

    public function handle()
    {
        $this->info('🔍 Mendeteksi duplikasi data ORMAS...');
        $this->newLine();

        // Detect duplicates in ORMAS Master
        $this->detectOrmasMasterDuplicates();
        
        // Detect empty names in SKT and SKL
        $this->detectEmptyNames();
        
        // Detect cross-references duplicates
        $this->detectCrossReferenceDuplicates();

        if ($this->option('cleanup') && !$this->option('dry-run')) {
            $this->handleCleanup();
        } elseif ($this->option('dry-run')) {
            $this->info('🔍 Mode dry-run aktif - tidak ada perubahan yang dilakukan');
        }

        $this->newLine();
        $this->info('✅ Proses deteksi duplikasi selesai!');
    }

    private function detectOrmasMasterDuplicates()
    {
        $this->warn('📋 Mencari duplikasi nama di ORMAS Master...');
        
        // Use Eloquent to get fresh data from database
        $duplicateGroups = OrmasMaster::whereNotNull('nama_ormas')
            ->where('nama_ormas', '!=', '')
            ->get()
            ->groupBy(function($item) {
                return strtolower(trim($item->nama_ormas));
            })
            ->filter(function($group) {
                return $group->count() > 1;
            });

        $duplicates = $duplicateGroups->map(function($group) {
            $first = $group->first();
            return (object)[
                'nama_ormas' => $first->nama_ormas,
                'count' => $group->count(),
                'ids' => $group->pluck('id')->implode(', '),
                'sources' => $group->pluck('sumber_registrasi')->implode(', ')
            ];
        });

        if ($duplicates->isEmpty()) {
            $this->info('✅ Tidak ada duplikasi nama ditemukan di ORMAS Master');
            return [];
        }

        $this->table(
            ['Nama ORMAS', 'Jumlah Duplikat', 'IDs', 'Sumber'],
            $duplicates->map(function ($duplicate) {
                return [
                    $duplicate->nama_ormas,
                    $duplicate->count,
                    $duplicate->ids,
                    $duplicate->sources
                ];
            })->toArray()
        );

        return $duplicates->values()->toArray();
    }

    private function detectEmptyNames()
    {
        $this->warn('📋 Mencari nama kosong di SKT dan SKL...');
        
        $emptySKT = SKT::whereNull('nama_ormas')
            ->orWhere('nama_ormas', '')
            ->orWhere('nama_ormas', 'like', '%   %')
            ->count();

        $emptySKL = SKL::whereNull('nama_organisasi')
            ->orWhere('nama_organisasi', '')
            ->orWhere('nama_organisasi', 'like', '%   %')
            ->count();

        if ($emptySKT > 0) {
            $this->error("❌ Ditemukan {$emptySKT} record SKT dengan nama kosong/tidak valid");
        }

        if ($emptySKL > 0) {
            $this->error("❌ Ditemukan {$emptySKL} record SKL dengan nama kosong/tidak valid");
        }

        if ($emptySKT === 0 && $emptySKL === 0) {
            $this->info('✅ Tidak ada nama kosong ditemukan');
        }
    }

    private function detectCrossReferenceDuplicates()
    {
        $this->warn('📋 Mencari duplikasi nama lintas SKT dan SKL...');
        
        // For MySQL compatibility (doesn't support FULL OUTER JOIN)
        $sktNames = SKT::whereNotNull('nama_ormas')
            ->where('nama_ormas', '!=', '')
            ->pluck('nama_ormas', 'id')
            ->map(function($name) {
                return strtolower(trim($name));
            });

        $sklNames = SKL::whereNotNull('nama_organisasi')
            ->where('nama_organisasi', '!=', '')
            ->pluck('nama_organisasi', 'id')
            ->map(function($name) {
                return strtolower(trim($name));
            });

        $intersect = $sktNames->intersect($sklNames);
        
        if ($intersect->isNotEmpty()) {
            $this->error("❌ Ditemukan {$intersect->count()} nama yang duplikat antara SKT dan SKL:");
            
            foreach ($intersect as $duplicateName) {
                // Find all SKT IDs with this name
                $sktIds = $sktNames->filter(function($name) use ($duplicateName) {
                    return $name === $duplicateName;
                })->keys()->toArray();
                
                // Find all SKL IDs with this name  
                $sklIds = $sklNames->filter(function($name) use ($duplicateName) {
                    return $name === $duplicateName;
                })->keys()->toArray();
                
                $this->line("  - Nama: " . $duplicateName);
                $this->line("    SKT IDs: " . implode(', ', $sktIds));
                $this->line("    SKL IDs: " . implode(', ', $sklIds));
            }
        } else {
            $this->info('✅ Tidak ada duplikasi nama antara SKT dan SKL');
        }
    }

    private function handleCleanup()
    {
        // Skip confirmation when running from UI/web context
        if (!$this->option('dry-run')) {
            $this->info('🧹 Memulai proses pembersihan...');

            // Clean empty names first
            $this->cleanEmptyNames();
            
            // Clean ORMAS duplicates
            $this->cleanOrmasDuplicates();

            $this->info('✅ Pembersihan selesai!');
        }
    }

    private function cleanEmptyNames()
    {
        $this->info('🧹 Membersihkan nama kosong...');
        
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

        $this->info("✅ Diperbarui {$updatedSKT} record SKT dan {$updatedSKL} record SKL dengan nama kosong");
    }

    private function cleanOrmasDuplicates()
    {
        $this->info('🧹 Menangani duplikasi ORMAS...');
        
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
        
        if ($duplicateGroups->isEmpty()) {
            $this->info('✅ Tidak ada duplikasi ditemukan');
            return;
        }

        foreach ($duplicateGroups as $normalizedName => $group) {
            $records = $group->sortBy('created_at'); // Keep the oldest record
            $keepRecord = $records->first();
            $deleteRecords = $records->skip(1);
            
            foreach ($deleteRecords as $record) {
                $this->line("  Menghapus duplikat: {$record->nama_ormas} (ID: {$record->id})");
                $record->delete();
            }
        }
    }
}