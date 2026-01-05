<?php

namespace App\Filament\Resources\SptResource\Pages;

use App\Filament\Resources\SptResource;
use App\Models\SptPegawai;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;      
use Illuminate\Support\Facades\Log;

class EditSpt extends EditRecord
{
    protected static string $resource = SptResource::class;
    protected function mutateFormDataBeforeSave(array $data): array
    {
    // Clean the data before saving
    $data = $this->cleanData($data);

    $sptId = $this->record->id;
    $sptIdCount = DB::table('spt_pegawai')
        ->where('spt_id', $sptId)
        ->count();
    $indexsptPegawaiCount = DB::table('spt_pegawai')->count();
    $sptPegawaiCount = $indexsptPegawaiCount - $sptIdCount;
    $data['spt_pegawai_count'] = $sptPegawaiCount;
    $this->data['spt_pegawai_count'] = $sptPegawaiCount;
    // Log::info('ARRAY: ' . print_r($data, true));
    return $data;    
    }
    protected function afterSave(): void
    {
        // Sync pegawai to the pivot table
        $this->syncPegawai();

        // Generate SPPD if 'generate_sppd' is checked
        if (!empty($this->data['generate_sppd'])) {
            $this->generateSppd();
        }

        // Redirect after processing
        $this->redirect($this->getResource()::getUrl('index'));
    }

    private function cleanData(array $data): array
    {
        unset($data['resume']); // Remove 'resume' from form data
        return $data;
    }
    private function syncPegawai(): void
    {
        if (!empty($this->data['pegawai'])) {
            $this->record->pegawai()->sync($this->data['pegawai']);
        }
    }
    public function generateSppd(): void
    {
        // Fetch pegawai IDs from the pivot table
        $pegawaiIds = DB::table('spt_pegawai')
            ->where('spt_id', $this->record->id)
            ->pluck('pegawai_id')
            ->toArray();

        // Check if there are pegawai linked
        if (empty($pegawaiIds)) {
            Log::error('No pegawai found for SPT ID: ' . $this->record->id);
            return;
        }

        Log::info('Kategori Perjalanan: ' . $this->data['kategori_perjalanan']);
    Log::info('Tanggal SPT: ' . $this->data['tanggal_spt']);
    Log::info('SPT Pegawai Count: ' . ($this->data['spt_pegawai_count'] ?? 0));
        $nomorSppds = $this->generateNomorSppd(
        $this->data['kategori_perjalanan'],
        $this->data['tanggal_spt'],
        $pegawaiIds,
        $this->data['spt_pegawai_count']?? 0
        );
        $this->syncSppd($pegawaiIds, $nomorSppds);
    }
    private function generateNomorSppd($kategori_perjalanan, $tanggal_spt, array $pegawaiIds, $spt_pegawai_count): array
    {
    $date = Carbon::parse($tanggal_spt);
    $bulan_romawi = self::monthToRoman($date->month);
    $currentYear = $date->year;
    $lastSppd = SptPegawai::orderBy('id', 'desc')->first();
    $lastYear = $lastSppd ? $lastSppd->created_at->year : null;
    Log::info('Mulai generate nomor SPPD, CountRows awal: ' . $spt_pegawai_count);

    // Gunakan spt_pegawai_count yang sudah dihitung
    $countRows = $lastYear !== $currentYear ? 1 : $spt_pegawai_count + 1;

    Log::info('Mulai generate nomor SPPD, CountRows awal: ' . $countRows);

    $nomorSppds = [];
    foreach ($pegawaiIds as $pegawaiId) {
        // Generate nomor_sppd in the format: 094/{count}/SPPD/DKISP-set/{kategori_perjalanan}/{bulan_romawi}/{year}
        $nomor_sppd = sprintf(
            '094/%d/SPPD/DKISP-set/%s/%s/%d',
            $countRows,
            Str::upper($kategori_perjalanan),
            $bulan_romawi,
            $currentYear
        );

        // Logging nomor_sppd yang dibuat
        Log::info("Nomor SPPD untuk Pegawai ID {$pegawaiId}: {$nomor_sppd}");

        // Simpan nomor_sppd untuk pegawai ini
        $nomorSppds[$pegawaiId] = $nomor_sppd;

        // Increment countRows untuk pegawai berikutnya
        $countRows++;

        // Logging setelah increment
        Log::info("CountRows setelah increment untuk pegawai berikutnya: " . $countRows);
    }

    return $nomorSppds;
    }
    private function syncSppd(array $pegawaiIds, array $nomorSppds): void
    {
        foreach ($pegawaiIds as $pegawaiId) {
            // Fetch the spt_pegawai pivot record ID
            $sptPegawaiId = DB::table('spt_pegawai')
                ->where('spt_id', $this->record->id)
                ->where('pegawai_id', $pegawaiId)
                ->value('id');

            if (!$sptPegawaiId) {
                Log::error('Missing spt_pegawai_id for pegawai_id: ' . $pegawaiId);
                continue;
            }

            // Update the SPPD number and status in the database
            DB::table('spt_pegawai')
                ->where('id', $sptPegawaiId)
                ->update([
                    'nomor_sppd' => $nomorSppds[$pegawaiId],
                    'status' => 'pending',
                    'updated_at' => Carbon::now(),
                ]);
        }
    }
    public static function monthToRoman($month): string
    {
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $romanMonths[$month] ?? '';
    }
}