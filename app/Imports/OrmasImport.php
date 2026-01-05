<?php

namespace App\Imports;

use App\Models\OrmasMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Validators\ValidationException;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Rules\UniqueOrmasNameRule;

class OrmasImport implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    WithBatchInserts, 
    WithChunkReading, 
    SkipsEmptyRows,
    SkipsErrors,
    SkipsFailures
{
    use Importable;

    protected $updateExisting;
    protected $validateOnly;
    protected $importResults = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => [],
    ];

    public function __construct(bool $updateExisting = false, bool $validateOnly = false)
    {
        $this->updateExisting = $updateExisting;
        $this->validateOnly = $validateOnly;
    }

    public function model(array $row)
    {
        // Skip if validate only
        if ($this->validateOnly) {
            return null;
        }

        // Normalize data
        $data = $this->normalizeData($row);

        // Check if ORMAS already exists
        $existingOrmas = null;
        if (!empty($data['nomor_registrasi'])) {
            $existingOrmas = OrmasMaster::where('nomor_registrasi', $data['nomor_registrasi'])->first();
        }

        if ($existingOrmas) {
            if ($this->updateExisting) {
                // Update existing ORMAS
                $existingOrmas->update($data);
                $this->importResults['updated']++;
                return null; // Don't create new model
            } else {
                // Skip existing ORMAS
                $this->importResults['skipped']++;
                return null;
            }
        }

        // Create new ORMAS
        $this->importResults['created']++;
        return new OrmasMaster($data);
    }

    protected function normalizeData(array $row): array
    {
        return [
            'nomor_registrasi' => $this->getValue($row, 'nomor_registrasi'),
            'nama_ormas' => $this->getValue($row, 'nama_ormas'),
            'nama_singkatan_ormas' => $this->getValue($row, 'nama_singkatan'),
            'status_administrasi' => $this->normalizeStatus($this->getValue($row, 'status_administrasi')),
            'sumber_registrasi' => $this->normalizeSource($this->getValue($row, 'sumber_registrasi')),
            'tempat_pendirian' => $this->getValue($row, 'tempat_pendirian'),
            'tanggal_pendirian' => $this->parseDate($this->getValue($row, 'tanggal_pendirian')),
            'bidang_kegiatan' => $this->getValue($row, 'bidang_kegiatan'),
            'ciri_khusus' => $this->normalizeCiriKhusus($this->getValue($row, 'ciri_khusus')),
            'tujuan_ormas' => $this->getValue($row, 'tujuan_ormas'),
            'alamat_sekretariat' => $this->getValue($row, 'alamat_sekretariat'),
            'provinsi' => $this->getValue($row, 'provinsi'),
            'kab_kota' => $this->getValue($row, 'kabupatenkota'),
            'kode_pos' => $this->getValue($row, 'kode_pos'),
            'nomor_handphone' => $this->getValue($row, 'nomor_handphone'),
            'nomor_fax' => $this->getValue($row, 'nomor_fax'),
            'email' => $this->getValue($row, 'email'),
            'nomor_akta_notaris' => $this->getValue($row, 'nomor_akta_notaris'),
            'tanggal_akta_notaris' => $this->parseDate($this->getValue($row, 'tanggal_akta_notaris')),
            'jenis_akta' => $this->getValue($row, 'jenis_akta'),
            'nomor_npwp' => $this->getValue($row, 'nomor_npwp'),
            'nama_bank' => $this->getValue($row, 'nama_bank'),
            'nomor_rekening_bank' => $this->getValue($row, 'nomor_rekening_bank'),
            'ketua_nama_lengkap' => $this->getValue($row, 'nama_ketua'),
            'ketua_nik' => $this->getValue($row, 'nik_ketua'),
            'ketua_masa_bakti_akhir' => $this->parseDate($this->getValue($row, 'masa_bakti_ketua')),
            'sekretaris_nama_lengkap' => $this->getValue($row, 'nama_sekretaris'),
            'sekretaris_nik' => $this->getValue($row, 'nik_sekretaris'),
            'sekretaris_masa_bakti_akhir' => $this->parseDate($this->getValue($row, 'masa_bakti_sekretaris')),
            'bendahara_nama_lengkap' => $this->getValue($row, 'nama_bendahara'),
            'bendahara_nik' => $this->getValue($row, 'nik_bendahara'),
            'bendahara_masa_bakti_akhir' => $this->parseDate($this->getValue($row, 'masa_bakti_bendahara')),
            'keterangan_status' => $this->getValue($row, 'keterangan_status'),
            'first_registered_at' => now(),
        ];
    }

    protected function getValue(array $row, string $key)
    {
        // Try different possible column name variations
        $possibleKeys = [
            $key,
            str_replace('_', ' ', $key),
            ucwords(str_replace('_', ' ', $key)),
            strtoupper($key),
            strtolower($key),
        ];

        foreach ($possibleKeys as $possibleKey) {
            if (isset($row[$possibleKey]) && !empty(trim($row[$possibleKey]))) {
                return trim($row[$possibleKey]);
            }
        }

        return null;
    }

    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Try different date formats
            $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y'];
            
            foreach ($formats as $format) {
                $date = Carbon::createFromFormat($format, $value);
                if ($date !== false) {
                    return $date;
                }
            }

            // Try Carbon's flexible parsing
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function normalizeStatus($value)
    {
        if (empty($value)) {
            return 'belum_selesai';
        }

        $value = strtolower(trim($value));
        
        if (in_array($value, ['selesai', 'completed', 'done', '1'])) {
            return 'selesai';
        }

        return 'belum_selesai';
    }

    protected function normalizeSource($value)
    {
        if (empty($value)) {
            return 'skt'; // Default to SKT
        }

        $value = strtolower(trim($value));
        
        if (in_array($value, ['skl', 'surat keterangan lainnya'])) {
            return 'skl';
        }

        return 'skt';
    }

    protected function normalizeCiriKhusus($value)
    {
        if (empty($value)) {
            return null;
        }

        $validOptions = [
            'Keagamaan',
            'Kewanitaan',
            'Kepemudaan',
            'Kesamaan Profesi',
            'Kesamaan Kegiatan',
            'Kesamaan Bidang',
            'Mitra K/L'
        ];

        $value = trim($value);
        
        // Try exact match first
        if (in_array($value, $validOptions)) {
            return $value;
        }

        // Try case-insensitive match
        foreach ($validOptions as $option) {
            if (strtolower($value) === strtolower($option)) {
                return $option;
            }
        }

        // Try partial match
        foreach ($validOptions as $option) {
            if (str_contains(strtolower($option), strtolower($value))) {
                return $option;
            }
        }

        return $validOptions[0]; // Default to first option
    }

    public function rules(): array
    {
        return [
            'nomor_registrasi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('ormas_master', 'nomor_registrasi')->when($this->updateExisting === false)
            ],
            'nama_ormas' => [
                'required',
                'string',
                'max:255',
                new UniqueOrmasNameRule(null, null)
            ],
            'status_administrasi' => 'nullable|in:selesai,belum_selesai',
            'sumber_registrasi' => 'required|in:skt,skl',
            'email' => 'nullable|email|max:255',
            'nomor_handphone' => 'nullable|string|max:20',
            'nomor_npwp' => 'nullable|string|max:20',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nomor_registrasi.required' => 'Nomor registrasi wajib diisi',
            'nomor_registrasi.unique' => 'Nomor registrasi sudah ada dalam database',
            'nama_ormas.required' => 'Nama ORMAS wajib diisi',
            'email.email' => 'Format email tidak valid',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getImportResults(): array
    {
        return $this->importResults;
    }

    public function onError(\Throwable $error)
    {
        $this->importResults['errors'][] = $error->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->importResults['errors'][] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }
}