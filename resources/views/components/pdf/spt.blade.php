<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Tugas</title>
    <style>
    @page {
        size: A4;
        margin: 0;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 9pt;
        line-height: 0.6;
        margin: 0;
        padding: 8mm;
        position: relative;
        height: 277mm;
        width: 190mm;
    }

    .header {
        text-align: center;
        margin-bottom: 5mm;
        line-height: 0.35;
    }

    .logo {
        position: absolute;
        top: 5mm;
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
    }

    td {
        padding: 2mm 0;
    }

    .indent {
        padding-left: 10mm;
    }

    .signature {
        margin-top: 2mm;
        text-align: left;
        /* Keep the text left aligned */
        float: right;
        width: 16%;
        padding-right: 10mm;
    }

    .signature-image-wrapper {
        text-align: center;
        /* Center the image */
        margin-bottom: 10px;
        /* Optional: Add spacing between image and text */
    }
    </style>
</head>

<body>
    <div class="header" style=" margin-left: 20mm; ">
        <h1 style="position: absolute;
            top: 26mm;
            left: 12mm;">________________________________________________________________________________</h1>
        <img src="{{ public_path('images/logokaltara.png') }}" alt="Logo" class="logo">
        <h1>PEMERINTAH PROVINSI KALIMANTAN UTARA</h1>
        <h1>BADAN KESATUAN BANGSA DAN POLITIK</h1>
        <p>Jalan Kolonel Soetadji Gedung Gubernur Lt. 4 Kode Pos 77212</p>
        <p>E-mail : kesbangpol@kaltaraprov.go.id Website : diskominfo.kaltaraprov.go.id</p>
        <p><strong>TANJUNG SELOR</strong></p>
    </div>

    <div class="content">
        <h1 style="text-align: center; text-decoration: underline; margin-left: 20mm;">SURAT PERINTAH TUGAS</h1>
        <p style="text-align: center; margin-left: 20mm;">Nomor : {{ $spt->nomor_spt ?? 'N/A' }}</p>


        <p style="line-height: 0.8; margin-left: 8mm;">Dasar :<span>
                <ol>
                    <li style="line-height: 1.1; margin-left: 45mm; text-align: justify;">Peraturan Gubernur Kalimantan
                        Utara Nomor :12 Tahun 2016 Tentang Perubahan Atas Peraturan Gubernur Nomor. 47 Tahun 2015
                        tentang Pedoman Pelaksanaan Perjalanan Dinas Bagi Gubernur/ Wakil Gubernur, Pimpinan dan Anggota
                        DPRD, Pegawai Negeri Sipil Daerah, Calon Pegawai Negeri Sipil Daerah, dan Tenaga Non Pegawai
                        Negeri Sipil di Lingkungan Pemerintah Provinsi Kalimantan Utara.</li>
                    <li style="line-height: 1.1; margin-left: 45mm; text-align: justify;">Peraturan Gubernur Kalimantan
                        Utara Nomor: 21 Tahun 2023 tentang Perubahan Kedua Atas Peraturan Gubernur Kalimantana Utara
                        Nomor 60 Tahun 2020 Tentang Standar Harga Satuan Provinsi Kalimantan Utara.</li>
                    <li style="line-height: 1.1; margin-left: 45mm; text-align: justify;">Telaahan Staf</li>
                </ol>
            </span> </p>


        <p style="text-align: center;"><strong>MENUGASKAN :</strong></p>

        <p style="margin-left: 8mm;">Kepada :</p>

        <table style="line-height: 0.9; margin-left: 50mm; ">
            @if($pegawais && $pegawais->count() > 0)
            @foreach($pegawais as $index => $pegawai)
            <tr>
                <td>{{ $index + 1 }}.</td>
                <td>
                    Nama<br>
                    Pangkat/ Gol.<br>
                    N I P<br>
                    Jabatan
                </td>
                <td>
                    : {{ $pegawai->pegawai->nama_pegawai ?? 'N/A' }}<br>
                    : {{ $pegawai->pegawai->pangkat_gologan ?? 'N/A' }}<br>
                    : {{ $pegawai->pegawai->nip ?? 'N/A' }}<br>
                    : {{ $pegawai->pegawai->jabatan ?? 'N/A' }}
                </td>
            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="3">Tidak ada data pegawai tersedia.</td>
            </tr>
            @endif
        </table>

        <p style="margin-left: 5mm;">Untuk <span style="margin-left: 35mm;">
                {{ ": ".$spt->perihal_spt ?? 'N/A' }}</span></p>

        <table style="width: 100%; border-collapse: collapse; line-height: 0.3; margin-left: 5mm;">
            <tbody>
                <tr>
                    <td style="width: 25%; border: none;">Tempat Berangkat</td>
                    <td style="width: 80%; border: none; ;">: {{ $spt->tempat_berangkat ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border: none;">Tempat Tujuan</td>
                    <td style="border: none; ;">:
                        {{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">Lamanya Perjalanan Dinas</td>
                    <td style="border: none; ;">:
                        {{ $spt->tanggal_berangkat && $spt->tanggal_kembali ? $spt->tanggal_berangkat->diffInDays($spt->tanggal_kembali) + 1 . ' Hari' : 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none;">Tanggal Berangkat</td>
                    <td style="border: none; ;">:
                        {{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border: none;">Tanggal Kembali</td>
                    <td style="border: none; ;">:
                        {{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="border: none;">Beban Biaya</td>
<td style="border: none;">:
    @if($spt->rekening)
        {{ $spt->rekening->nomor_rekening . ".5.1.02.04.01.0001" }}
    @else
        -
    @endif
</td>
                </tr>
            </tbody>
        </table>


        <p style="margin-left: 50mm; line-height: 1.0;">Setelah melaksanakan tugas segera membuat laporan. Demikian
            surat perintah tugas ini diberikan agar dipergunakan sebagaimana mestinya</p>
        <p></p>

        <div class="signature" style="margin-top: 2mm; text-align: left; float: right; width: 28%;">
            <p>Dikeluarkan di : Tanjung Selor</p>
            <p>Pada Tanggal : {{ $spt->tanggal_spt ? $spt->tanggal_spt->format('d F Y') : 'N/A' }}</p>
            <p><strong>{{ $spt->status_pengesah ?? 'N/A' }}</strong></p>

            @if ($pa_sign_path)
            <div class="signature-image-wrapper">
                <img src="{{ $pa_sign_path }}" alt="Signature" style="width: 250px; height: auto;">
            </div>
            @else
            <p><br><br><br><br></p>
            @endif

            <p><strong>{{ $spt->pengesah->nama_pegawai ?? 'N/A' }}</strong></p>
            <p>{{ $spt->pengesah->pangkat_gologan ?? 'N/A' }}</p>
            <p>NIP. {{ $spt->pengesah->nip ?? 'N/A' }}</p>
        </div>

    </div>
</body>

</html>