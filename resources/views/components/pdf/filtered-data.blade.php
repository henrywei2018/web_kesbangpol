<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Cetak Rekapitulasi</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.2;
            margin: 0;
            padding: 4mm;
            position: relative;
            height: 210mm;
            width: 260mm;
        }

        .header {
            text-align: center;
            margin-bottom: 5mm;
            line-height: 0.70;
        }

        .logo {
            position: absolute;
            top: 4mm;
            left: 12mm;
            width: 20mm;
            height: 25mm;
        }

        h1 {
            font-size: 12pt;
            font-weight: bold;
            margin: 2mm 0;
        }

        .content {
            margin-top: 2mm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-left: 5mm;
            margin-right: 5mm;
            font-size: 9pt;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            
        }

        th {
            background-color: #f4f4f4;
        }

        .signature {
            margin-top: 2mm;
            text-align: center;
            float: right;
            width: 40%;
            line-height: 0.70;
        }
    </style>
</head>

<body>
    <div class="header" style="margin-left: 20mm;">
        <h1 style="position: absolute; top: 28mm; left: 12mm;">
            ___________________________________________________________________________________________________________
        </h1>
        <img src="{{ public_path('images/logokaltara.png') }}" alt="Logo" class="logo">
        <h1>PEMERINTAH PROVINSI KALIMANTAN UTARA</h1>
        <h1>BADAN KESATUAN BANGSA DAN POLITIK</h1>
        <p>Jalan Rambutan Gedung Gabungan Dinas Lt. 5 Kode Pos 77212</p>
        <p>E-mail: kesbangpol@kaltaraprov.go.id | Website: kesbangpol.kaltaraprov.go.id</p>
        <p><strong>TANJUNG SELOR</strong></p>
    </div>

    <div class="content">
        <h1 style="text-align: center; margin-left: 20mm; font-weight: bold;">Rekapitulasi Surat Perintah Tugas</h1>
        <p style="text-align: right; margin-left: 20mm; font-weight: bold;" >Periode: 
    {{ \Carbon\Carbon::create(null, request('start_month'))->translatedFormat('F') }} 
    - 
    {{ \Carbon\Carbon::create(null, request('end_month'))->translatedFormat('F') }} 
    Tahun {{ request('year') }}
</p>

        <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor SPT</th>
                <th>Nomor SPPD</th>
                <th>Tujuan</th>
                <th>Tanggal Berangkat</th>
                <th>Tanggal Kembali</th>
                <th>Jenis</th>
                <th>Nama Pegawai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sptPegawai as $index => $record)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $record->spt->nomor_spt ?? '-' }}</td>
                    <td>{{ $record->nomor_sppd ?? '-' }}</td>
                    <td>
                        @if ($record->spt->tempat_tujuan)
                            {{ is_array($record->spt->tempat_tujuan) ? implode(', ', $record->spt->tempat_tujuan) : $record->spt->tempat_tujuan }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $record->spt->tanggal_berangkat ? $record->spt->tanggal_berangkat->format('d-m-Y') : '-' }}</td>
                    <td style="text-align: center;">{{ $record->spt->tanggal_kembali ? $record->spt->tanggal_kembali->format('d-m-Y') : '-' }}</td>
                    <td style="text-align: center;">{{ $record->spt->kategori_perjalanan ?? '-' }}</td>
                    <td>{{ $record->pegawai->nama_pegawai ?? '-' }}</td>
                    <td style="text-align: center;">{{ $record->spt->status_spt ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    </div>
</body>

</html>
