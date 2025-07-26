<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SURAT PERINTAH TUGAS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .logo {
            float: left;
            width: 100px;
            height: 100px;
        }
        .header h1 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .header p {
            margin: 0;
        }
        .content {
            margin-bottom: 20px;
        }
        .content p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 5px;
            vertical-align: top;
            border-bottom: 1px solid #ddd;
        }
        .signature {
            margin-top: 30px;
            text-align: right;
        }
        .line {
            border-top: 1px solid #000;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/logokaltara.png" alt="Logo" class="logo">
        <h1>PEMERINTAH PROVINSI KALIMANTAN UTARA</h1>
        <h2>DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN</h2>
        <p>Jalan Rambutan Gedung Gabungan Dinas Lt. 5 Kode Pos 77212</p>
        <p>E-mail : diskominfo@kaltaraprov.go.id Website : diskominfo.kaltaraprov.go.id</p>
    </div>

    <div class="content">
        <h1 style="text-align: center;">SURAT PERINTAH TUGAS</h1>
        <p style="text-align: center;">Nomor: {{ $spt->nomor_spt ?? 'N/A' }}</p>

        <div class="line"></div>

        <h2>Detail SPT:</h2>
        <table>
            <tr>
                <th>Kategori Perjalanan</th>
                <td>{{ $spt->kategori_perjalanan ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Tanggal SPT</th>
                <td>{{ $spt->tanggal_spt ? $spt->tanggal_spt->format('d F Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Perihal SPT</th>
                <td>{{ $spt->perihal_spt ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Tempat Berangkat</th>
                <td>{{ $spt->tempat_berangkat ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Tempat Tujuan</th>
                <td>{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}</td>
            </tr>
            <tr>
                <th>Tanggal Berangkat</th>
                <td>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <th>Tanggal Kembali</th>
                <td>{{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}</td>
            </tr>
        </table>

        <div class="line"></div>

        <h2>Pegawai:</h2>
        @if($spt->pegawai && $spt->pegawai->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($spt->pegawai as $pegawai)
                        <tr>
                            <td>{{ $pegawai->nama_pegawai ?? 'N/A' }}</td>
                            <td>{{ $pegawai->nip ?? 'N/A' }}</td>
                            <td>{{ $pegawai->jabatan ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Tidak ada data pegawai tersedia.</p>
        @endif

        <div class="line"></div>

        <h2>Rekening:</h2>
        <p>{{ $spt->rekening->nama_rekening ?? 'N/A' }} ({{ $spt->rekening->nomor_rekening ?? 'N/A' }})</p>

        <h2>Pengesah:</h2>
        <p>{{ $spt->pengesah->nama_pegawai ?? 'N/A' }} ({{ $spt->pengesah->nip ?? 'N/A' }})</p>
        <p>{{ $spt->status_pengesah ?? 'N/A' }}</p>

        <div class="signature">
            <p>Dikeluarkan di: Tanjung Selor</p>
            <p>Pada Tanggal: {{ $spt->tanggal_spt ? $spt->tanggal_spt->format('d F Y') : 'N/A' }}</p>
            <p><br><br><br><br>{{ $spt->pengesah->nama_pegawai ?? 'N/A' }}</p>
            <p>{{ $spt->pengesah->nip ?? 'N/A' }}</p>
        </div>
    </div>
</body>
</html>