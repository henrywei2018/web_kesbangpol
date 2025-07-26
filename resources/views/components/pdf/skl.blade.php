<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Lapor</title>
    <style>
    @page {
        size: A4;
        margin: 0;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 9pt;
        line-height: 1.2;
        margin: 0;
        padding: 8mm;
        position: relative;
        height: 277mm;
        width: 190mm;
    }

    .header {
        text-align: center;
        margin-bottom: 5mm;
        line-height: 0.70;
    }

    .logo {
        position: absolute;
        top: 8mm;
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
        margin-left: 10mm;
    }

    td {
        padding: 2mm 0;
    }

    .indent {
        padding-left: 10mm;
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
    <div class="header" style=" margin-left: 20mm; ">
        <h1 style="position: absolute;
            top: 32mm;
            left: 12mm;">________________________________________________________________________________</h1>
        <img src="{{ public_path('images/logokaltara.png') }}" alt="Logo" class="logo">
        <h1>PEMERINTAH PROVINSI KALIMANTAN UTARA</h1>
        <h1>BADAN KESATUAN BANGSA DAN POLITIK</h1>
        <p>Jalan Rambutan Gedung Gabungan Dinas Lt. 5 Kode Pos 77212</p>
        <p>E-mail : kesbangpol@kaltaraprov.go.id Website : kesbangpol.kaltaraprov.go.id</p>
        <p><strong>TANJUNG SELOR</strong></p>
    </div>

    <div class="content">
        <h1 style="text-align: center; text-decoration: underline; margin-left: 20mm;">SURAT KETERANGAN LAPOR</h1>
        <p style="text-align: center; margin-left: 20mm;">Nomor : 094/{{ $skl->id ?? 'N/A' }}/KESBANGPOL-set/SKL/{{ now()->format('Y') }}

        </p>

        <table width="100%" cellspacing="5" cellpadding="0" id="detail" style="margin-top: 7mm;">
            <tbody>
                <tr>
                    <td width="20%" valign="top">NAMA ORGANISASI</td>
                    <td width="3%" valign="top">:</td>
                    <td width="77%" style="text-transform: uppercase">{{ $skl->nama_organisasi ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td valign="top">NOMOR DAN PENGESAHAN BADAN HUKUM</td>
                    <td valign="top">:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td valign="top">BIDANG KEGIATAN</td>
                    <td valign="top">:</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td valign="top">SUSUNAN KEPENGURUSAN</td>
                    <td valign="top">:</td>
                    <td>Ketua : {{ $skl->nama_ketua ?? 'N/A' }}<br>
                        Sekretaris : -<br>
                        Bendahara : -<br>
                    </td>
                </tr>

                <tr>
                    <td valign="top">ALAMAT SEKRETARIAT</td>
                    <td valign="top">:</td>
                    <td>Jl.adityawarman rt 1 karang balik kota tarakan</td>
                </tr>

                <tr>
                    <td valign="top">NOMOR NPWP</td>
                    <td valign="top">:</td>
                    <td>-</td>
                </tr>

                <tr>
                    <td valign="top">NOMOR dan TANGGAL SURAT</td>
                    <td valign="top">:</td>
                    <td>-<br>-</td>
                </tr>

                <tr>
                    <td valign="top">NOMOR REGISTER BERKAS</td>
                    <td valign="top">:</td>
                    <td>{{ $skl->id ?? 'N/A' }}/{{ $skl->created_at->format('dmY') ?? 'N/A' }}</td>
                </tr>

                <tr>
                    <td valign="top">MASA BERLAKU</td>
                    <td valign="top">:</td>
                    <td>{{ $validityDate }}</td>
                </tr>
            </tbody>
        </table>

        <div class="signature" style="margin-left: 10mm;">
            <p>Dikeluarkan di : Tanjung Selor</p>
            <p>Pada Tanggal : {{ now()->format('d F Y')  }}</p>
            <p><strong>Kepala Badan</strong></p>
            <br><br><br>
            <p><strong>{{ $pengesah->nama_pegawai ?? 'N/A' }}</strong></p>
            <p>{{ $pengesah->pangkat_gologan ?? 'N/A' }}</p>
            <p>NIP. {{ $pengesah->nip ?? 'N/A' }}</p>
        </div>
    </div>
</body>

</html>