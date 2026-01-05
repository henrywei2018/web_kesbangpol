<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class OrmasTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Template Data ORMAS' => new OrmasDataSheet(),
            'Panduan Pengisian' => new PanduanPengisianSheet(),
            'Referensi Pilihan' => new ReferensiPilihanSheet(),
        ];
    }
}

class OrmasDataSheet implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function array(): array
    {
        // Return sample data with 2 example rows
        return [
            [
                'ORM-KALTARA-2025-0001', // nomor_registrasi
                'Contoh ORMAS Pertama', // nama_ormas
                'COP1', // nama_singkatan
                'belum_selesai', // status_administrasi
                'skt', // sumber_registrasi
                'Jakarta', // tempat_pendirian
                '01/01/2020', // tanggal_pendirian
                'Sosial', // bidang_kegiatan
                'Kepemudaan', // ciri_khusus
                'Membantu pemberdayaan masyarakat', // tujuan_ormas
                'Jl. Contoh No. 123', // alamat_sekretariat
                'DKI Jakarta', // provinsi
                'Jakarta Pusat', // kabupatenkota
                '10110', // kode_pos
                '08123456789', // nomor_handphone
                '021-12345678', // nomor_fax
                'contoh@ormas.org', // email
                '123/NOT/2020', // nomor_akta_notaris
                '15/01/2020', // tanggal_akta_notaris
                'Pendirian', // jenis_akta
                '12.345.678.9-001.000', // nomor_npwp
                'Bank BCA', // nama_bank
                '1234567890', // nomor_rekening_bank
                'John Doe', // nama_ketua
                '1234567890123456', // nik_ketua
                '31/12/2025', // masa_bakti_ketua
                'Jane Smith', // nama_sekretaris
                '6543210987654321', // nik_sekretaris
                '31/12/2025', // masa_bakti_sekretaris
                'Bob Johnson', // nama_bendahara
                '1122334455667788', // nik_bendahara
                '31/12/2025', // masa_bakti_bendahara
                'Data contoh untuk testing', // keterangan_status
            ],
            [
                'ORM-KALTARA-2025-0002',
                'Contoh ORMAS Kedua',
                'COP2',
                'selesai',
                'skl',
                'Surabaya',
                '15/06/2019',
                'Pendidikan',
                'Keagamaan',
                'Meningkatkan pendidikan agama',
                'Jl. Pendidikan No. 456',
                'Jawa Timur',
                'Surabaya',
                '60111',
                '08987654321',
                '031-87654321',
                'contoh2@ormas.org',
                '456/NOT/2019',
                '01/07/2019',
                'Perubahan',
                '98.765.432.1-002.000',
                'Bank Mandiri',
                '0987654321',
                'Alice Johnson',
                '9876543210123456',
                '30/06/2026',
                'Charlie Brown',
                '5432109876543210',
                '30/06/2026',
                'Diana Prince',
                '8899001122334455',
                '30/06/2026',
                'Status administrasi sudah selesai',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'nomor_registrasi *',
            'nama_ormas *',
            'nama_singkatan',
            'status_administrasi',
            'sumber_registrasi *',
            'tempat_pendirian',
            'tanggal_pendirian',
            'bidang_kegiatan',
            'ciri_khusus',
            'tujuan_ormas',
            'alamat_sekretariat',
            'provinsi',
            'kabupatenkota',
            'kode_pos',
            'nomor_handphone',
            'nomor_fax',
            'email',
            'nomor_akta_notaris',
            'tanggal_akta_notaris',
            'jenis_akta',
            'nomor_npwp',
            'nama_bank',
            'nomor_rekening_bank',
            'nama_ketua',
            'nik_ketua',
            'masa_bakti_ketua',
            'nama_sekretaris',
            'nik_sekretaris',
            'masa_bakti_sekretaris',
            'nama_bendahara',
            'nik_bendahara',
            'masa_bakti_bendahara',
            'keterangan_status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:AG1')->applyFromArray([
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

        // Style for sample data
        $sheet->getStyle('A2:AG3')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E6F3FF'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->getRowDimension('1')->setRowHeight(25);
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25, 'B' => 30, 'C' => 15, 'D' => 18, 'E' => 18,
            'F' => 15, 'G' => 15, 'H' => 15, 'I' => 15, 'J' => 30,
            'K' => 30, 'L' => 15, 'M' => 15, 'N' => 10, 'O' => 15,
            'P' => 15, 'Q' => 25, 'R' => 20, 'S' => 15, 'T' => 15,
            'U' => 20, 'V' => 15, 'W' => 20, 'X' => 20, 'Y' => 18,
            'Z' => 15, 'AA' => 20, 'AB' => 18, 'AC' => 15, 'AD' => 20,
            'AE' => 18, 'AF' => 15, 'AG' => 25,
        ];
    }

    public function title(): string
    {
        return 'Template Data ORMAS';
    }
}

class PanduanPengisianSheet implements FromArray, WithTitle
{
    public function array(): array
    {
        return [
            ['PANDUAN PENGISIAN TEMPLATE IMPORT DATA ORMAS'],
            [''],
            ['1. KOLOM WAJIB (ditandai dengan *)'],
            ['   - nomor_registrasi: Nomor registrasi unik ORMAS (format: ORM-KALTARA-YYYY-NNNN)'],
            ['   - nama_ormas: Nama lengkap organisasi'],
            ['   - sumber_registrasi: skt atau skl'],
            [''],
            ['2. FORMAT TANGGAL'],
            ['   Gunakan format: dd/mm/yyyy (contoh: 15/01/2020)'],
            ['   Kolom tanggal: tanggal_pendirian, tanggal_akta_notaris, masa_bakti_*'],
            [''],
            ['3. STATUS ADMINISTRASI'],
            ['   - belum_selesai: untuk ORMAS yang belum selesai administrasi'],
            ['   - selesai: untuk ORMAS yang sudah selesai administrasi'],
            [''],
            ['4. SUMBER REGISTRASI'],
            ['   - skt: jika berasal dari Surat Keterangan Terdaftar'],
            ['   - skl: jika berasal dari Surat Keterangan Lainnya'],
            [''],
            ['5. CIRI KHUSUS (pilih salah satu)'],
            ['   - Keagamaan'],
            ['   - Kewanitaan'],
            ['   - Kepemudaan'],
            ['   - Kesamaan Profesi'],
            ['   - Kesamaan Kegiatan'],
            ['   - Kesamaan Bidang'],
            ['   - Mitra K/L'],
            [''],
            ['6. TIPS PENGISIAN'],
            ['   - Pastikan nomor_registrasi unik dan tidak duplikat'],
            ['   - Email harus dalam format yang valid'],
            ['   - NIK harus 16 digit'],
            ['   - Hapus baris contoh sebelum import data sesungguhnya'],
            ['   - Validasi file terlebih dahulu sebelum import'],
        ];
    }

    public function title(): string
    {
        return 'Panduan Pengisian';
    }
}

class ReferensiPilihanSheet implements FromArray, WithTitle
{
    public function array(): array
    {
        return [
            ['REFERENSI PILIHAN UNTUK KOLOM TERTENTU'],
            [''],
            ['STATUS ADMINISTRASI:'],
            ['belum_selesai'],
            ['selesai'],
            [''],
            ['SUMBER REGISTRASI:'],
            ['skt'],
            ['skl'],
            [''],
            ['CIRI KHUSUS:'],
            ['Keagamaan'],
            ['Kewanitaan'],
            ['Kepemudaan'],
            ['Kesamaan Profesi'],
            ['Kesamaan Kegiatan'],
            ['Kesamaan Bidang'],
            ['Mitra K/L'],
            [''],
            ['JENIS AKTA (contoh):'],
            ['Pendirian'],
            ['Perubahan'],
            ['Perubahan Anggaran Dasar'],
            [''],
            ['FORMAT NOMOR REGISTRASI:'],
            ['ORM-KALTARA-YYYY-NNNN'],
            ['Contoh: ORM-KALTARA-2025-0001'],
        ];
    }

    public function title(): string
    {
        return 'Referensi Pilihan';
    }
}