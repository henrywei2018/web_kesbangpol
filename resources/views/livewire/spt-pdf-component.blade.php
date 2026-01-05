@extends('components.layouts.spt-pdf')

@section('content')
<div>
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold">SURAT PERINTAH TUGAS</h1>
        <p class="text-xl">Nomor: {{ $spt->nomor_spt ?? 'N/A' }}</p>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2">Detail SPT:</h2>
        <table class="w-full">
            <tr>
                <td class="font-semibold pr-4">Kategori Perjalanan:</td>
                <td>{{ $spt->kategori_perjalanan ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="font-semibold pr-4">Tanggal SPT:</td>
                <td>{{ $spt->tanggal_spt ? $spt->tanggal_spt->format('d F Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="font-semibold pr-4">Perihal SPT:</td>
                <td>{{ $spt->perihal_spt ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="font-semibold pr-4">Tempat Berangkat:</td>
                <td>{{ $spt->tempat_berangkat ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="font-semibold pr-4">Tempat Tujuan:</td>
                <td>{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}</td>
            </tr>
            <tr>
                <td class="font-semibold pr-4">Tanggal Berangkat:</td>
                <td>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="font-semibold pr-4">Tanggal Kembali:</td>
                <td>{{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Rest of the content remains the same -->

    <div class="no-print mt-8">
    <button wire:click="generatePdf({{ $spt->id }})" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
        Generate PDF
    </button>
</div>

</div>
@endsection