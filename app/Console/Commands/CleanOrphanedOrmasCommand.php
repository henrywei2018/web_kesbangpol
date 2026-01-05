<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OrmasMaster;
use App\Models\SKT;
use App\Models\SKL;

class CleanOrphanedOrmasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ormas:clean-orphaned {--dry-run : Show what would be cleaned without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned ORMAS data (ORMAS without valid SKT or SKL)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('🔍 Scanning for orphaned ORMAS data...');
        
        // Find ORMAS with invalid SKT reference
        $orphanedBySkt = OrmasMaster::whereNotNull('skt_id')
            ->whereNotExists(function ($query) {
                $query->select('id')
                    ->from('skts')
                    ->whereColumn('skts.id', 'ormas_master.skt_id');
            })
            ->get();

        // Find ORMAS with invalid SKL reference
        $orphanedBySkl = OrmasMaster::whereNotNull('skl_id')
            ->whereNotExists(function ($query) {
                $query->select('id')
                    ->from('skls')
                    ->whereColumn('skls.id', 'ormas_master.skl_id');
            })
            ->get();

        // Find ORMAS with both SKT and SKL null (completely orphaned)
        $completelyOrphaned = OrmasMaster::whereNull('skt_id')
            ->whereNull('skl_id')
            ->get();

        $totalOrphaned = $orphanedBySkt->count() + $orphanedBySkl->count() + $completelyOrphaned->count();

        if ($totalOrphaned === 0) {
            $this->info('✅ No orphaned ORMAS data found!');
            return 0;
        }

        $this->warn("⚠️  Found {$totalOrphaned} orphaned ORMAS records:");
        
        if ($orphanedBySkt->count() > 0) {
            $this->line("  - {$orphanedBySkt->count()} with invalid SKT references");
        }
        
        if ($orphanedBySkl->count() > 0) {
            $this->line("  - {$orphanedBySkl->count()} with invalid SKL references");
        }
        
        if ($completelyOrphaned->count() > 0) {
            $this->line("  - {$completelyOrphaned->count()} with no valid references");
        }

        if ($dryRun) {
            $this->info('🔍 DRY RUN - No data will be deleted');
            $this->displayOrphanedData($orphanedBySkt, $orphanedBySkl, $completelyOrphaned);
            return 0;
        }

        if (!$this->confirm('Do you want to clean up this orphaned data?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $deletedCount = 0;

        // Clean up ORMAS with invalid SKT
        foreach ($orphanedBySkt as $ormas) {
            if ($ormas->skl_id && SKL::find($ormas->skl_id)) {
                // Has valid SKL, just remove SKT reference
                $ormas->update([
                    'skt_id' => null,
                    'sumber_registrasi' => 'skl',
                    'keterangan_status' => 'SKT reference cleaned up on ' . now()->format('d/m/Y H:i')
                ]);
                $this->line("  ✅ Updated ORMAS {$ormas->nama_ormas} (kept SKL reference)");
            } else {
                // No valid SKL, delete completely
                $ormas->delete();
                $deletedCount++;
                $this->line("  🗑️  Deleted ORMAS {$ormas->nama_ormas}");
            }
        }

        // Clean up ORMAS with invalid SKL
        foreach ($orphanedBySkl as $ormas) {
            if ($ormas->skt_id && SKT::find($ormas->skt_id)) {
                // Has valid SKT, just remove SKL reference
                $ormas->update([
                    'skl_id' => null,
                    'sumber_registrasi' => 'skt',
                    'keterangan_status' => 'SKL reference cleaned up on ' . now()->format('d/m/Y H:i')
                ]);
                $this->line("  ✅ Updated ORMAS {$ormas->nama_ormas} (kept SKT reference)");
            } else {
                // No valid SKT, delete completely
                $ormas->delete();
                $deletedCount++;
                $this->line("  🗑️  Deleted ORMAS {$ormas->nama_ormas}");
            }
        }

        // Delete completely orphaned ORMAS
        foreach ($completelyOrphaned as $ormas) {
            $ormas->delete();
            $deletedCount++;
            $this->line("  🗑️  Deleted completely orphaned ORMAS {$ormas->nama_ormas}");
        }

        $this->info("✅ Cleanup completed! Deleted {$deletedCount} orphaned ORMAS records.");
        
        return 0;
    }

    private function displayOrphanedData($orphanedBySkt, $orphanedBySkl, $completelyOrphaned)
    {
        if ($orphanedBySkt->count() > 0) {
            $this->line("\n📋 ORMAS with invalid SKT references:");
            foreach ($orphanedBySkt as $ormas) {
                $this->line("  - {$ormas->nama_ormas} (SKT ID: {$ormas->skt_id})");
            }
        }

        if ($orphanedBySkl->count() > 0) {
            $this->line("\n📋 ORMAS with invalid SKL references:");
            foreach ($orphanedBySkl as $ormas) {
                $this->line("  - {$ormas->nama_ormas} (SKL ID: {$ormas->skl_id})");
            }
        }

        if ($completelyOrphaned->count() > 0) {
            $this->line("\n📋 Completely orphaned ORMAS:");
            foreach ($completelyOrphaned as $ormas) {
                $this->line("  - {$ormas->nama_ormas}");
            }
        }
    }
}
