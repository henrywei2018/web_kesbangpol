<x-filament::page>
    <div class="container">
        <div class="row justify-content-center">
            <!-- Signature Pad Section -->
            <div class="col-lg-10 col-md-12 py-4">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-body p-4 p-md-5">
                        <!-- Header -->
                        <div class="row mb-4 text-center">
                            <div class="col">
                                <img src="{{ asset('images/logokaltara.png') }}" alt="Logo" class="logo mb-3" style="width: 60px;">
                                <h2 class="font-weight-bold text-uppercase" style="font-size: 1.5rem;">
                                    Surat Perintah Tugas
                                </h2>
                            </div>
                        </div>

                        <!-- Menugaskan Section -->
                        <div class="row mb-4">
                            <div class="col-lg-12">
                                <p class="font-weight-bold" style="margin-left: 1mm; font-size: 1.2rem;">Rincian Surat Tugas</p>

                                <!-- Pegawai Table -->
                                <table class="table table-sm" style="margin-left: 1mm;">
                                    @if($sptPegawaiRecords && $sptPegawaiRecords->count() > 0)
                                        @foreach($sptPegawaiRecords as $index => $pegawai)
                                            <tr>
                                                <td>{{ $index + 1 }}.</td>
                                                <td>
                                                    <strong>Nama:</strong><br>
                                                    <strong>NIP:</strong><br>
                                                    <strong>Jabatan:</strong>
                                                </td>
                                                <td>
                                                    {{ $pegawai->pegawai->nama_pegawai ?? 'N/A' }}<br>
                                                    {{ $pegawai->pegawai->nip ?? 'N/A' }}<br>
                                                    {{ $pegawai->pegawai->jabatan ?? 'N/A' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Tidak ada data pegawai tersedia.</td>
                                        </tr>
                                    @endif
                                </table>

                                <p style="margin-left: 1mm; font-size: 1rem;">
                                    <strong>Perihal:</strong> {{ $spt->perihal_spt ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        <!-- SPT Details Section -->
                        <div class="row mb-4">
                            <div class="col-lg-12">
                                <table class="table table-responsive-sm" style="margin-left: 1mm;">
                                    <tbody>
                                        <tr>
                                            <td style="width: 40%;"><strong>Tempat Berangkat</strong></td>
                                            <td>{{ $spt->tempat_berangkat ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tempat Tujuan</strong></td>
                                            <td><tr>
    <td><strong>Tempat Tujuan</strong></td>
    <td>{{ $spt && is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}</td>
</tr>
</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Lamanya Perjalanan Dinas</strong></td>
                                            <td>{{ $spt && $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Berangkat</strong></td>
                                            <td>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Kembali</strong></td>
                                            <td>{{ $spt && $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Beban Biaya</strong></td>
                                            <td>{{ $spt->rekening->nomor_rekening ? $spt->rekening->nomor_rekening . ".5.1.02.04.01.0001" : 'N/A' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Signature Form -->
                        <div class="row">
                            <div class="col-lg-12">
                                <h3 class="font-weight-bold text-center mb-4">Tanda Tangan</h3>
                                <form wire:submit.prevent="submit">
                                    {{ $this->form }}
                                    <button type="submit" class="btn btn-primary mt-4">Submit Tanda Tangan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
