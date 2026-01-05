<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Perjalanan Dinas</title>
    <style>
    @page {
        size: F4;
        margin: 0;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 10pt;        
        margin: 0;
        padding: 10mm;
        height: 277mm;
        width: 190mm;
    }

    .header {
        text-align: center;
        margin-bottom: 5mm;
        line-height: 0.45;
        border-bottom: 1px solid black;
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
        line-height: 0.45;

    }

    .content {
        margin-top: 0mm;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black;
        table-layout: fixed;

    }

    td {
        padding: 1mm 1mm;
        vertical-align: top;
        border: 1px solid black;
    }

    .numbered-item {
        margin-bottom: 0mm;
        font-size: 8pt;
        line-height: 1.2;


    }
    .numbered-item1 {
        margin-bottom: 0mm;
        font-size: 8pt;

    }

    .signature {
        margin-top: 2mm;
        text-align: center;
        width: 35%;
        float: right;
    }
    .signature-image-wrapper {
        text-align: center;
    }

    .bordered-table {
        border: 1px solid black;
        border-collapse: collapse;
        line-height: 1.2;
    }

    .bordered-table td {
        border: 1px solid black;
    }

    .page-break {
        page-break-after: always;
    }
    </style>
</head>

<body>
    @foreach ($sptPegawaiRecords as $sptPegawai)
    <div class="header">
        <img src="{{ public_path('images/logokaltara.png') }}" alt="Logo" class="logo">
        <h1>PEMERINTAH PROVINSI KALIMANTAN UTARA</h1>
        <h1>DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN</h1>
        <p>Jalan Rambutan Gedung Gabungan Dinas Lt. 5 Kode Pos 77212</p>
        <p>TANJUNG SELOR</p>
    </div>

    <div class="content">
        <h1 style="text-align: center; text-decoration: underline;">SURAT PERINTAH PERJALANAN DINAS</h1>

        <p style="text-align: left; margin-left: 100mm; line-height: 0.35; margin-top: 4mm;">Lembar ke : ____</p>
        <p style="text-align: left; margin-left: 100mm; line-height: 0.35;">Kode No : ____</p>
        <p style="text-align: left; margin-left: 100mm; line-height: 0.35;">Nomor :{{ $sptPegawai->nomor_sppd ?? 'N/A' }}</p>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">1.</td>
                    <td style="width: 48%;">Pengguna Anggaran/Kuasa Pengguna Anggaran</td>
                    <td style="width: 48%;">Kepala Dinas Komunikasi, Informatika, Statistik dan Persandian Provinsi
                        Kalimantan Utara</td>
                </tr>
            </table>
        </div>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">2.</td>
                    <td style="width: 48%;">Nama/NIP Pegawai yang melaksanakan perjalanan dinas</td>
                    <td style="width: 48%;">{{ $sptPegawai->pegawai->nama_pegawai ?? 'N/A' }}<span> /</span><br>{{ $sptPegawai->pegawai->nip ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">3.</td>
                    <td style="width: 48%;">
                        a. Pangkat dan Golongan<br>
                        b. Jabatan/ Instansi<br>
                        c. Tingkat Biaya Perjalanan Dinas
                    </td>
                    <td style="width: 48%;">
                        a. {{ $sptPegawai->pegawai->pangkat_gologan ?? 'N/A' }}<br>
                        b. {{ $sptPegawai->pegawai->jabatan ?? 'N/A' }}<br>
                        c. ________________
                    </td>
                </tr>
            </table>
        </div>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">4.</td>
                    <td style="width: 48%;">Maksud Perjalanan Dinas</td>
                    <td style="width: 48%;">{{ $spt->perihal_spt ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">5.</td>
                    <td style="width: 48%;">Alat angkut yang dipergunakan</td>
                    <td style="width: 48%;">Taxi Udara, Taxi Darat, Taxi Air</td>
                </tr>
            </table>
        </div>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">6.</td>
                    <td style="width: 48%;">
                        a. Tempat berangkat<br>
                        b. Tempat tujuan
                    </td>
                    <td style="width: 48%;">
                        a. {{ $spt->tempat_berangkat ?? 'N/A' }}<br>
                        b.
                        {{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">7.</td>
                    <td style="width: 48%;">
                        a. Lamanya Perjalanan Dinas<br>
                        b. Tanggal berangkat<br>
                        c. Tanggal harus kembali/tiba di tempat baru * )
                    </td>
                    <td style="width: 48%;">
                        a.
                        {{ $spt->tanggal_berangkat && $spt->tanggal_kembali ? $spt->tanggal_berangkat->diffInDays($spt->tanggal_kembali) + 1 . ' Hari' : 'N/A' }}<br>
                        b. {{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}<br>
                        c. {{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">8.</td>
                    <td style="width: 96%;">
                        Pengikut: Nama<br>
                        1. ________________<br>
                        2. ________________<br>
                        3. ________________
                    </td>
                </tr>
            </table>
        </div>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">9.</td>
                    <td style="width: 48%;">
                        Pembebanan Anggaran<br>
                        a. SKPD<br>
                        b. Kode Rekening
                    </td>
                    <td style="width: 48%;">
                        a. Dinas Komunikasi, Informatika, Statistik dan Persandian Provinsi Kalimantan Utara<br>
                        b.
                        {{ $spt->rekening->nomor_rekening ? $spt->rekening->nomor_rekening . ".5.1.02.04.01.0001" : 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="numbered-item">
            <table class="bordered-table">
                <tr>
                    <td style="width: 4%;">10.</td>
                    <td style="width: 96%;">Keterangan lain-lain</td>
                </tr>
            </table>
        </div>

        <p>*coret yang tidak perlu</p>

        <div class="signature">
            <p style="text-align: left;">Dikeluarkan di : Tanjung Selor <br>Tanggal : {{ $spt->tanggal_spt ? $spt->tanggal_spt->format('d F Y') : 'N/A' }} </p>            
            <p>Pengguna Anggaran/Kuasa Pengguna Anggaran
                <br>@if ($pa_sign_path)
            <div class="signature-image-wrapper">
                <img src="{{ $pa_sign_path }}" alt="Signature" style="width: auto; height: 70px;">
            </div>
            @else
            <p>No signature available</p>
            @endif
                <strong>{{ $spt->pengesah->nama_pegawai ?? 'N/A' }}</strong><br>
                NIP. {{ $spt->pengesah->nip ?? 'N/A' }}</p>
        </div>
    </div>

    <div class="page-break"></div>

    <div class="content">  

    <div class="numbered-item1">
        <table>
            <tr>
                
                <td style="width: 52%; border-bottom-style: hidden;"></td>
                <td style="width: 4%; border-bottom-style: hidden; border-right-style: hidden;">I.</td>
                <td style="width: 22%; border-bottom-style: hidden; border-right-style: hidden; border-left-style: hidden;">Berangkat Dari<br>Ke<br>Pada Tanggal</td>
                <td style="width: 22%; border-bottom-style: hidden; border-left-style: hidden;">: {{ $spt->tempat_berangkat ?? 'N/A' }}<br>: {{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}<br>: {{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }} </td>
            </tr>
        </table>
    </div>
    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%; border-top-style: hidden; border-right-style: hidden;"></td>
                <td style="width: 48%; border-top-style: hidden;"></td>
                <td style="width: 48%; text-align: center; border-top-style: hidden;">PPTK
                <br>@if ($pptk_sign_path)
            <div class="signature-image-wrapper">
                <img src="{{ $pptk_sign_path }}" alt="Signature" style="width: auto; height: 50px;">
            </div>
            @else
            <p>No signature available</p>
            @endif
        <strong>{{ $spt->rekening->pegawai->nama_pegawai ?? 'N/A'  }}</strong><br>
        NIP. {{ $spt->rekening->pegawai->nip ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;">II.</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ $spt->tempat_berangkat ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="width: 4%;"></td>
                <td style="width: 24%;">Pada Tanggal</td>
                <td style="width: 24%;">{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
                <td style="width: 24%;">Pada Tanggal</td>
                <td style="width: 24%;">{{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>
    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;"></td>
                <td style="width: 48%;">Kepala<br><br><br><br><br> NIP</td>
                <td style="width: 48%;">Kepala<br><br><br><br><br> NIP</td>
            </tr>
        </table>
    </div>
    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;">III.</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ $spt->tempat_berangkat ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="width: 4%;"></td>
                <td style="width: 24%;">Pada Tanggal</td>
                <td style="width: 24%;">{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>
    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;"></td>
                <td style="width: 48%;">Kepala<br><br><br><br><br> NIP</td>
                <td style="width: 48%;">Kepala<br><br><br><br><br> NIP</td>
            </tr>
        </table>
    </div>
    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;">IV.</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ $spt->tempat_berangkat ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="width: 4%;"></td>
                <td style="width: 24%;">Pada Tanggal</td>
                <td style="width: 24%;">{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>
    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;"></td>
                <td style="width: 48%;">Kepala<br><br><br><br><br> NIP</td>
                <td style="width: 48%;">Kepala<br><br><br><br><br> NIP</td>
            </tr>
        </table>
    </div>
    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;">V.</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ $spt->tempat_berangkat ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="width: 4%;"></td>
                <td style="width: 24%;">Pada Tanggal</td>
                <td style="width: 24%;">{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;">{{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>
    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;"></td>
                <td style="width: 48%;">Kepala<br><br><br><br><br> NIP</td>
                <td style="width: 48%;">Kepala<br><br><br><br><br> NIP</td>
            </tr>         
        </table>
    </div>
    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;">VI.</td>
                <td style="width: 24%;">Tiba</td>
                <td style="width: 24%;" >{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}</td>
                <td rowspan="2" style="width: 48%; border-bottom-style: hidden; font-size:7.5pt; text-align: justify; line-height: 1.1;" >Telah diperiksa dengan keterangan bahwa perjalanan tersebut di atas
                    dilakukan atas perintahnya dan semata-mata untuk kepentingan jabatan dalam waktu yang sesingkat-singkatnya.</td>                
            </tr>
            <tr>
                <td style="width: 4%;"></td>
                <td style="width: 24%;">Pada Tanggal</td>
                <td style="width: 24%;" >{{ $spt->tempat_berangkat ?? 'N/A' }}</td>
                               
            </tr>
        </table>
    </div>

    <div>
        <Table>
            <tr>
                <td style="width: 4%;border-bottom-style: hidden;"></td>
                <td style="width: 48%; border-bottom-style: hidden; "></td>
                <td style="width: 48%; border-bottom-style: hidden; border-top-style: hidden; font-size:6pt;"></td>
            </tr>
        </Table>
    </div>
    
    <div class="numbered-item1" style="text-align: center;">
        <table>
            <tr>
                <td style="width: 4%; border-top-style: hidden"></td>
                <td style="width: 48%; border-top-style: hidden">Kepala<br><br><br><br><br> NIP</td>
                <td style="width: 48%; text-align: center; border-top-style: hidden;">Pengguna Anggaran/Kuasa Pengguna Anggaran
                <br>@if ($pa_sign_path)
            <div class="signature-image-wrapper">
                <img src="{{ $pa_sign_path }}" alt="Signature" style="width: auto; height: 70px;">
            </div>
            @else
            <p>No signature available</p>
            @endif
                <strong>{{ $spt->pengesah->nama_pegawai ?? 'N/A' }}</strong><br>
                NIP. {{ $spt->pengesah->nip ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="numbered-item1">
        <table>
            <tr>
                <td style="width: 4%;">VII.</td>
                <td style="width: 96%;">
                    Catatan Lain :
                </td>
            </tr>
        </table>
    </div>

    <div class="numbered-item11">
        <table>
            <tr>
                <td style="width: 4%;">VIII.</td>
                <td style="width: 96%; font-size:8.5pt;">
                    PERHATIAN : <br>
                        PPK yang menerbitkan SPD, pegawai yang melakukan perjalanan dinas, para pejabat yang mengesahkan tanggal berangkat/tiba, serta bendahara pengeluaran bertanggungjawab berdasarkan peraturan-peraturan Keuangan Negara apabila negara menderita rugi akibat kesalahan, kelalaian dan kealpaannya.
                    
                </td>
            </tr>
        </table>
    </div>
</div>
@if (!$loop->last)
        <div class="page-break"></div>
    @endif

@endforeach
</body>

</html>