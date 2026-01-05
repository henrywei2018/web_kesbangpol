<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 330mm 215mm landscape;
            /* F4 size */
            margin: 0;
        }

        .logo {
            position: absolute;
            top: 4mm;
            left: 80mm;
            width: 12mm;
            height: 15.2mm;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 12mm;
            width: 330mm;
            /* F4 width */
            height: 215mm;
            /* F4 height */
            font-size: 11pt;
        }

        .header {
            text-align: center;
            padding-top: 3mm;
            margin-bottom: 2px;
        }

        .header h3,
        .header h4 {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
            font-size: 11px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 3px 5px;
            vertical-align: top;
        }

        .travel-table {
            margin-bottom: 10px;
        }

        .sign-section {
            text-align: center;
            width: 100%;
            line-height: 100%;
        }

        .no-border {
            border: none !important;
        }

        /* Added styles */
        .main-table {
            border: none;
            width: 100%;
        }

        .main-table td {
            vertical-align: top;
        }

        .section-left {
            width: 50%;
            padding-right: 2mm;
        }

        .section-right {
            width: 50%;
            padding-left: 2mm;
        }

        @media print {
            body {
                width: 330mm;
                height: 215mm;
                margin: 0;
                padding: 15mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            page {
                margin: 0;
                padding: 0;
            }

            /* Force page breaks */
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
    @foreach ($sptPegawaiRecords as $sptPegawai)
    <div style="width: 315mm; margin: 0 auto;">
        <table class="main-table ">
            <tr>
                <td class="section-left" style="border: none;">
                    <!-- Left section content -->
                    <div class="header">
                        <h1 style="position: absolute; top: 23.4mm; left: 10mm;">_______________________________________________</h1>
                        <img src="{{ public_path('images/logokaltara.png') }}" alt="Logo" class="logo">
                        <p><strong>PEMERINTAH PROVINSI KALIMANTAN UTARA<br>BADAN KESATUAN BANGSA DAN POLITIK</strong><br>Jalan Kolonel Soetaji Gedung Gubernur Lama Lt. 3 Kode Pos 77212<br>TANJUNG SELOR</p>
                        <table style="border: none;">
                            <tr>
                                <td width="60%" style="text-align: right; border: none;">Lampiran ke : <br>Kode Nomor :<br>Nomor :</td>
                                <td width="40%" style="text-align: left; border: none;">-<br>-<br>{{ $sptPegawai->nomor_sppd ?? '' }}</td>
                            </tr>
                        </table>
                        <h4>SURAT PERINTAH PERJALANAN DINAS</h4>
                    </div>

                    <table>
                        <tbody>
                            <tr>
                                <td width="5%">1.</td>
                                <td width="44%">Pejabat yang memberikan perintah</td>
                                <td>Kepala Dinas Komunikasi, Informatika, Statistik dan Persandian Prov. Kaltara</td>
                            </tr>
                            <tr>
                                <td>2.</td>
                                <td>Nama pegawai yang diperintah</td>
                                <td>{{ $sptPegawai->pegawai->nama_pegawai ?? '' }}<br>{{ $sptPegawai->pegawai->nip ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>3.</td>
                                <td>a. Pangkat dan Golongan<br>b. Jabatan/Instansi<br>c. Tingkat Perjalanan Dinas</td>
                                <td>a. {{ $sptPegawai->pegawai->pangkat_gologan ?? '' }}<br>b. {{ $sptPegawai->pegawai->jabatan ?? 'N/A' }}<br>c. -</td>
                            </tr>
                            <tr>
                                <td>4.</td>
                                <td>Maksud Perjalanan Dinas</td>
                                <td>{{ $spt->perihal_spt ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>5.</td>
                                <td>Alat angkut yang dipergunakan</td>
                                <td>Taxi Darat, Taxi Air, Taxi Udara</td>
                            </tr>
                            <tr>
                                <td>6.</td>
                                <td>a. Tempat berangkat<br>b. Tempat tujuan</td>
                                <td>a. {{ $spt->tempat_berangkat ?? '' }}<br>b. {{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? '') }}</td>
                            </tr>
                            <tr>
                                <td>7.</td>
                                <td>a. Lamanya Perjalanan Dinas<br>b. Tanggal berangkat<br>c. Tanggal harus kembali</td>
                                <td>a.
                                    {{ $spt->tanggal_berangkat && $spt->tanggal_kembali ? $spt->tanggal_berangkat->diffInDays($spt->tanggal_kembali) + 1 . ' Hari' : 'N/A' }}<br>
                                    b. {{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->isoFormat('D MMMM Y') : 'N/A' }}<br>
                                    c. {{ $spt->tanggal_kembali ? $spt->tanggal_kembali->isoFormat('D MMMM Y') : 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td>8.</td>
                                <td colspan="2">
                                    <table style="width: 100%;">
                                        <tr>
                                            <td class="no-border" width="10%">Pengikut</td>
                                            <td class="no-border" width="30%">Nama</td>
                                            <td class="no-border" width="30%">NIP./Umur</td>
                                            <td class="no-border" width="30%">Hubungan Keluarga</td>
                                        </tr>
                                        <tr>
                                            <td class="no-border" style="text-align:right;">1.<br>
                                                2. <br></td>
                                            <td class="no-border">_______________________<br>
                                                _______________________<br></td>
                                            <td class="no-border"><br></td>
                                            <td class="no-border"><br></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td>9.</td>
                                <td>Pembebanan Anggaran<br>a. Instansi<br>b. Mata Anggaran</td>
                                <td>Tahun Anggaran {{ now()->year }}<br>a. {{ $konfig->skpd }}<br>b. @if($spt->rekening)
        {{ $spt->rekening->nomor_rekening . ".5.1.02.04.01.0001" }}
    @else
        -
    @endif</td>
                            </tr>
                            <tr>
                                <td>10.</td>
                                <td>Keterangan lain-lain</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="no-border">
                                    Tembusan disampaikan kepada :<br>
                                    1. Bendahara Pengeluaran<br>
                                    2. Arsip<br><br><br>
                                    Catatan :<br>
                                    Paling lambat 2 minggu setelah kembali dari perjalanan dinas, SPPD harus dikembalikan pada bendahara.
                                </td>
                                <td class="no-border">
                                    <div class="sign-section">
                                        <p>Dikeluarkan di: {{ $konfig->lokasi_asal}}<br>Pada tanggal: {{ $spt->tanggal_spt ? $spt->tanggal_spt->isoFormat('D MMMM Y') : '' }}</p>
                                        <p><strong>Pengguna Anggaran/Kuasa Pengguna Anggaran,</strong><br>@if ($pa_sign_path)
                                        <div class="signature-image-wrapper">
                                            <img src="{{ $pa_sign_path }}" alt="Signature" style="width: auto; height: 70px;">
                                        </div>
                                        @else
                                        <p><br><br></p>
                                        @endif</p>
                                        <p><strong>{{ $spt->pengesah->nama_pegawai ?? '' }}</strong><br>NIP. {{ $spt->pengesah->nip ?? '' }}</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td class="section-right" style="border: none;">
                    <table class="travel-table" style="width: 100%;">
                        <tr>
                            <td style="width: 4%; border: none; "><strong>I</strong></td>
                            <td style="width: 14%; border: none;">Berangkat dari<br>(tempat asal)<br>Tanggal<br>Ke
                            </td>
                            <td style="width: 34%; border: none;">: {{ $konfig->lokasi_asal}}<br><br>: {{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->isoFormat('D MMMM Y') : 'N/A' }}<br>: {{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? '') }}</td>
                            <td colspan="2" style="width: 48%; border: none;">
                                <div class="sign-section">
                                    <p><strong>PPTK</strong></p>
                                    <br>@if ($pptk_sign_path)
                                    <div class="signature-image-wrapper">
                                        <img src="{{ $pptk_sign_path }}" alt="Signature" style="width: auto; height: 50px;">
                                    </div>
                                    @else
                                    <br><br>
                                    @endif
                                    <p><strong>{{ $spt->rekening->pegawai->nama_pegawai ?? 'N/A' }}</strong><br>NIP. {{ $spt->rekening->pegawai->nip ?? 'N/A' }}</p>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 4%; border-right: none; "><strong>II</strong></td>
                            <td style="width: 14%; border-right: none; border-left: none;">tiba di<br>Pada Tanggal<br>Kepala
                            </td>
                            <td style="width: 34%; border-left: none;">{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? '') }}<br>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->isoFormat('D MMMM Y') : 'N/A' }}</td>
                            <td style="width: 16%; border-right: none;">Berangkat dari<br>Ke<br>Pada
                                Tanggal<br>Kepala<br><br><br><br><br></td>
                            <td style="width: 32%; border-left: none; ">{{ $konfig->lokasi_asal}}<br>{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? '') }}<br>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->isoFormat('D MMMM Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 4%; border-right: none; "><strong>III</strong></td>
                            <td style="width: 14%; border-right: none; border-left: none;">tiba di<br>Pada Tanggal<br>Kepala
                            </td>
                            <td style="width: 34%; border-left: none;">{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? '') }}<br>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->isoFormat('D MMMM Y') : 'N/A' }}</td>
                            <td style="width: 16%; border-right: none;">Berangkat dari<br>Ke<br>Pada
                                Tanggal<br>Kepala<br><br><br><br><br></td>
                            <td style="width: 32%; border-left: none; ">{{ $konfig->lokasi_asal}}<br>{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? '') }}<br>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->isoFormat('D MMMM Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 4%; border-right: none; "><strong>IV</strong></td>
                            <td style="width: 14%; border-right: none; border-left: none;">tiba di<br>Pada Tanggal<br>Kepala
                            </td>
                            <td style="width: 34%; border-left: none;">{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? '') }}<br>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->isoFormat('D MMMM Y') : 'N/A' }}</td>
                            <td style="width: 16%; border-right: none;">Berangkat dari<br>Ke<br>Pada
                                Tanggal<br>Kepala<br><br><br><br><br></td>
                            <td style="width: 32%; border-left: none; ">{{ $konfig->lokasi_asal}}<br>{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? '') }}<br>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->isoFormat('D MMMM Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 4%; border-right: none; border-bottom: none;">V</td>
                            <td style="width: 16%; border-right: none; border-left: none; border-bottom: none;">dari<br>pada
                            </td>
                            <td colspan="3" style="width: 80%; border-left: none; border-bottom: none;">: {{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? '') }}<br>:
                            {{ $spt->tanggal_kembali ? $spt->tanggal_kembali->isoFormat('D MMMM Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td style="border-right: none; border-bottom: none; border-top: none;"></td>
                            <td colspan="4" style=" border-left: none; border-bottom: none; border-top: none;">Telah diperiksa,
                                dengan keterangan bahwa perjalanan tersebut atas perintahnya dan semata-mata untuk kepentingan
                                jabatan dalam waktu yang sesingkat-singkatnya.</td>
                        </tr>
                        <tr>
                            <td style="width: 4%; border-right: none; border-top: none;"></td>
                            <td colspan="4" style=" border-left: none; border-top: none;">
                            <div class="sign-section" style="padding-left: 37mm;">
                                        <p>Dikeluarkan di: {{ $konfig->lokasi_asal}}<br>Pada tanggal: {{ $spt->tanggal_spt ? $spt->tanggal_spt->isoFormat('D MMMM Y') : '' }}</p>
                                        <p><strong>Pengguna Anggaran/Kuasa Pengguna Anggaran,</strong><br>@if ($pa_sign_path)
                                        <div class="signature-image-wrapper">
                                            <img src="{{ $pa_sign_path }}" alt="Signature" style="width: auto; height: 70px;">
                                        </div>
                                        @else
                                        <p><br><br></p>
                                        @endif</p>
                                        <p><strong>{{ $spt->pengesah->nama_pegawai ?? '' }}</strong><br>NIP. {{ $spt->pengesah->nip ?? '' }}</p>
                                    </div>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 4%; border: none;"><strong>VI</strong></td>
                            <td colspan="4" style="margin-left:20px; border: none;">
                                CATATAN LAIN-LAIN:<br>
                                Pejabat yang berwenang menerbitkan SPPD, pegawai yang melakukan perjalanan dinas, para pejabat yang mengesahkan tanggal berangkat/tiba, serta bendaharawan bertanggung jawab berdasarkan peraturan-peraturan keuangan negara apabila negara menderita rugi akibat kesalahan, kelalaian dan kealpaannya.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </div>
    @if (!$loop->last)
    <div class="page-break"></div>
    @endif

    @endforeach
</body>

</html>