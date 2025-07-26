@extends('components.layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <!-- Signature Pad Section -->
        <div class="col-lg-10 col-md-12 py-4">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-body p-4 p-md-5">
                    <!-- Header -->
                    <div class="row mb-4 text-center">
                        <div class="col">
                            <img src="{{ asset('images/logokaltara.png') }}" alt="Logo" class="logo mb-3"
                                style="width: 60px;">
                            <h2 class="font-weight-bold text-uppercase" style="font-size: 1.5rem;">Surat Perintah Tugas
                            </h2>
                        </div>
                    </div>

                    <!-- Menugaskan Section -->
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <p class="font-weight-bold" style="margin-left: 1mm; font-size: 1.2rem;">Rincian Surat Tugas
                            </p>

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

                            <p style="margin-left: 1mm; font-size: 1rem;"><strong>Perihal:</strong>
                                {{ $spt->perihal_spt ?? 'N/A' }}</p>
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
                                        <td>{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lamanya Perjalanan Dinas</strong></td>
                                        <td>{{ $spt->tanggal_berangkat && $spt->tanggal_kembali ? $spt->tanggal_berangkat->diffInDays($spt->tanggal_kembali) + 1 . ' Hari' : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Berangkat</strong></td>
                                        <td>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Kembali</strong></td>
                                        <td>{{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Beban Biaya</strong></td>
                                        <td>{{ $spt->rekening->nomor_rekening ? $spt->rekening->nomor_rekening . ".5.1.02.04.01.0001" : 'N/A' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Signature Pad Area -->
                    <div x-data="signaturePadComponent()" x-init="initSignaturePad" class="p-4">
                        <h2 class="text-lg font-bold">Signature Pad</h2>
                        <div class="border border-primary-300 rounded">
                            <canvas id="signature-pad" width="300" height="200" class="border"></canvas>
                        </div>
                        <div class="flex space-x-2 mt-2">
                            <button @click="clearPad"
                                class="bg-secondary text-white px-4 py-2 rounded">Bersihkan</button>
                            <button @click="saveSignature" class="bg-primary text-white px-4 py-2 rounded"
                                x-bind:disabled="isSaving">
                                <span x-show="!isSaving">Simpan</span>
                                <span x-show="isSaving">Menyimpan...</span>
                            </button>
                        </div>
                        <div x-show="errorMessage" class="mt-4 text-red-600">
                            <p x-text="errorMessage"></p>
                        </div>
                        @if (session()->has('message'))
                        <div class="mt-4 text-green-600">
                            {{ session('message') }}
                        </div>
                        @endif
                        @if (session()->has('error'))
                        <div class="mt-4 text-red-600">
                            {{ session('error') }}
                        </div>
                        @endif
                    </div>
                    @push('scripts')
                    <script>
                    function signaturePadComponent() {
                        return {
                            signaturePad: null,
                            isSaving: false,
                            errorMessage: null,
                            initSignaturePad() {
                                const canvas = document.getElementById('signature-pad');
                                this.signaturePad = new SignaturePad(canvas);
                                this.resizeCanvas(canvas);

                                window.addEventListener('resize', () => {
                                    this.resizeCanvas(canvas);
                                });

                                // Handle event when signature is saved successfully
                                Livewire.on('signatureSaved', () => {
                                    this.isSaving = false;
                                    this.clearPad();
                                });
                            },
                            resizeCanvas(canvas) {
                                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                                canvas.width = canvas.offsetWidth * ratio;
                                canvas.height = canvas.offsetHeight * ratio;
                                canvas.getContext('2d').scale(ratio, ratio);
                            },
                            clearPad() {
                                this.signaturePad.clear();
                                this.errorMessage = null;
                            },
                            saveSignature() {
                                if (this.signaturePad.isEmpty()) {
                                    this.errorMessage = "Please provide a signature.";
                                    return;
                                }
                                this.isSaving = true;
                                const signatureData = this.signaturePad.toDataURL('image/png');

                                // Dispatch event to Livewire
                                Livewire.dispatch('saveSignature', signatureData);
                            }
                            
                        };
                    }
                    </script>
                    @endpush
                </div>
            </div>
        </div>
    </div>
</div>
@endsection