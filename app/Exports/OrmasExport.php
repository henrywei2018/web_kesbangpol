<?php

namespace App\Exports;

use App\Models\OrmasMaster;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Database\Eloquent\Builder;

class OrmasExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = OrmasMaster::query()->with(['skt', 'skl']);

        // Apply filters
        if (!empty($this->filters['status_filter']) && $this->filters['status_filter'] !== 'all') {
            $query->where('status_administrasi', $this->filters['status_filter']);
        }

        if (!empty($this->filters['source_filter']) && $this->filters['source_filter'] !== 'all') {
            $query->where('sumber_registrasi', $this->filters['source_filter']);
        }

        if (!empty($this->filters['ciri_khusus'])) {
            $query->where('ciri_khusus', $this->filters['ciri_khusus']);
        }

        if (!empty($this->filters['kab_kota'])) {
            $query->where('kab_kota', $this->filters['kab_kota']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Registrasi',
            'Nama ORMAS',
            'Nama Singkatan',
            'Status Administrasi',
            'Sumber Registrasi',
            'ID Sumber',
            'Status Sumber',
            'Tempat Pendirian',
            'Tanggal Pendirian',
            'Bidang Kegiatan',
            'Ciri Khusus',
            'Tujuan ORMAS',
            'Alamat Sekretariat',
            'Provinsi',
            'Kabupaten/Kota',
            'Kode Pos',
            'Nomor Handphone',
            'Nomor Fax',
            'Email',
            'Nomor Akta Notaris',
            'Tanggal Akta Notaris',
            'Jenis Akta',
            'Nomor NPWP',
            'Nama Bank',
            'Nomor Rekening Bank',
            'Nama Ketua',
            'NIK Ketua',
            'Masa Bakti Ketua',
            'Nama Sekretaris',
            'NIK Sekretaris',
            'Masa Bakti Sekretaris',
            'Nama Bendahara',
            'NIK Bendahara',
            'Masa Bakti Bendahara',
            'Keterangan Status',
            'Tanggal Selesai Administrasi',
            'Tanggal Terdaftar',
            'Terakhir Diperbarui',
        ];
    }

    public function map($ormas): array
    {
        static $counter = 0;
        $counter++;

        // Get source status
        $sourceStatus = 'N/A';
        if ($ormas->sumber_registrasi === 'skt' && $ormas->skt) {
            $sourceStatus = $ormas->skt->status ?? 'N/A';
        } elseif ($ormas->sumber_registrasi === 'skl' && $ormas->skl) {
            $sourceStatus = $ormas->skl->status ?? 'N/A';
        }

        // Get source ID
        $sourceId = $ormas->sumber_registrasi === 'skt' ? $ormas->skt_id : $ormas->skl_id;

        return [
            $counter,
            $ormas->nomor_registrasi,
            $ormas->nama_ormas,
            $ormas->nama_singkatan_ormas,
            $ormas->status_administrasi === 'selesai' ? 'Selesai' : 'Belum Selesai',
            strtoupper($ormas->sumber_registrasi),
            $sourceId,
            $sourceStatus,
            $ormas->tempat_pendirian,
            $ormas->tanggal_pendirian ? $ormas->tanggal_pendirian->format('d/m/Y') : '',
            $ormas->bidang_kegiatan,
            $ormas->ciri_khusus,
            $ormas->tujuan_ormas,
            $ormas->alamat_sekretariat,
            $ormas->provinsi,
            $ormas->kab_kota,
            $ormas->kode_pos,
            $ormas->nomor_handphone,
            $ormas->nomor_fax,
            $ormas->email,
            $ormas->nomor_akta_notaris,
            $ormas->tanggal_akta_notaris ? $ormas->tanggal_akta_notaris->format('d/m/Y') : '',
            $ormas->jenis_akta,
            $ormas->nomor_npwp,
            $ormas->nama_bank,
            $ormas->nomor_rekening_bank,
            $ormas->ketua_nama_lengkap,
            $ormas->ketua_nik,
            $ormas->ketua_masa_bakti_akhir ? $ormas->ketua_masa_bakti_akhir->format('d/m/Y') : '',
            $ormas->sekretaris_nama_lengkap,
            $ormas->sekretaris_nik,
            $ormas->sekretaris_masa_bakti_akhir ? $ormas->sekretaris_masa_bakti_akhir->format('d/m/Y') : '',
            $ormas->bendahara_nama_lengkap,
            $ormas->bendahara_nik,
            $ormas->bendahara_masa_bakti_akhir ? $ormas->bendahara_masa_bakti_akhir->format('d/m/Y') : '',
            $ormas->keterangan_status,
            $ormas->tanggal_selesai_administrasi ? $ormas->tanggal_selesai_administrasi->format('d/m/Y H:i') : '',
            $ormas->first_registered_at ? $ormas->first_registered_at->format('d/m/Y H:i') : '',
            $ormas->updated_at ? $ormas->updated_at->format('d/m/Y H:i') : '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:AM1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Auto-fit row height for header
        $sheet->getRowDimension('1')->setRowHeight(25);

        // Apply borders to all data
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:AM{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 20,  // Nomor Registrasi
            'C' => 30,  // Nama ORMAS
            'D' => 15,  // Nama Singkatan
            'E' => 15,  // Status Administrasi
            'F' => 12,  // Sumber Registrasi
            'G' => 8,   // ID Sumber
            'H' => 12,  // Status Sumber
            'I' => 15,  // Tempat Pendirian
            'J' => 15,  // Tanggal Pendirian
            'K' => 15,  // Bidang Kegiatan
            'L' => 15,  // Ciri Khusus
            'M' => 30,  // Tujuan ORMAS
            'N' => 30,  // Alamat Sekretariat
            'O' => 12,  // Provinsi
            'P' => 15,  // Kabupaten/Kota
            'Q' => 8,   // Kode Pos
            'R' => 15,  // Nomor Handphone
            'S' => 15,  // Nomor Fax
            'T' => 25,  // Email
            'U' => 20,  // Nomor Akta Notaris
            'V' => 15,  // Tanggal Akta Notaris
            'W' => 15,  // Jenis Akta
            'X' => 20,  // Nomor NPWP
            'Y' => 15,  // Nama Bank
            'Z' => 20,  // Nomor Rekening Bank
            'AA' => 20, // Nama Ketua
            'AB' => 18, // NIK Ketua
            'AC' => 15, // Masa Bakti Ketua
            'AD' => 20, // Nama Sekretaris
            'AE' => 18, // NIK Sekretaris
            'AF' => 15, // Masa Bakti Sekretaris
            'AG' => 20, // Nama Bendahara
            'AH' => 18, // NIK Bendahara
            'AI' => 15, // Masa Bakti Bendahara
            'AJ' => 25, // Keterangan Status
            'AK' => 20, // Tanggal Selesai Administrasi
            'AL' => 20, // Tanggal Terdaftar
            'AM' => 20, // Terakhir Diperbarui
        ];
    }

    public function title(): string
    {
        return 'Data ORMAS Master';
    }
}