<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $role === 'PA' ? 'PA' : 'PPTK' }}</title>
    
    <!-- Tambahkan CSS Bootstrap untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Styling utama untuk card dan border */
        .card-wrapper {
            border: 2px solid #f51302; /* Border primary */
            border-radius: 10px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Shadow untuk elegan */
        }

        /* Border untuk signature pad */
        .signature-pad-wrapper {
            border: 2px solid #f51302;
            border-radius: 0.25rem;
            width: 100%;
            max-width: 600px;
            height: 200px;
            background-color: #f8f9fa;
        }

        /* Pastikan canvas mengikuti ukuran parent */
        canvas {
            width: 100%;
            height: 100%;
        }

        /* Styling tombol */
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0b5ed7;
        }

        /* Full width button on mobile */
        .btn-block {
            width: 100%;
        }

        /* Adjust padding and margins for mobile view */
        @media (max-width: 768px) {
            .card-body {
                padding: 1.5rem;
            }

            .signature-pad-wrapper {
                height: 180px;
            }

            .btn {
                margin-top: 15px;
            }
        }

        /* Notification styling */
        #notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body style="background-color: #f8f9fa;">

<div class="container">
    <div class="row justify-content-center">
        <!-- Signature Pad Section -->
        <div class="col-lg-8 col-md-12 py-4">
            <div class="card-wrapper">
                <div class="card-body p-4 p-md-5">
                    <!-- Header -->
                    <div class="row mb-4 text-center">
                        <div class="col">
                            <img src="{{ asset('images/logokaltara.png') }}" alt="Logo" class="logo mb-3" style="width: 60px;">
                            <h2 class="font-weight-bold text-uppercase" style="font-size: 1.5rem;">Surat Perintah Tugas - Tanda Tangan sebagai {{ $role === 'PA' ? 'PA' : 'PPTK' }}</h2>
                        </div>
                    </div>

                    <!-- Menugaskan Section -->
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <p class="font-weight-bold" style="font-size: 1.0rem;">Rincian Surat Tugas</p>

                            <!-- Pegawai Table -->
                            <table class="table table-sm" style="font-size: 1.0rem;">
                                @if($sptPegawaiRecords && $sptPegawaiRecords->count() > 0)
                                @foreach($sptPegawaiRecords as $index => $pegawai)
                                <tr>
                                    <td>{{ $index + 1 }}.</td>
                                    <td>
                                        <strong>Nama:</strong> {{ $pegawai->pegawai->nama_pegawai ?? 'N/A' }}<br>
                                        <strong>NIP:</strong> {{ $pegawai->pegawai->nip ?? 'N/A' }}<br>
                                        <strong>Jabatan:</strong> {{ $pegawai->pegawai->jabatan ?? 'N/A' }}
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Tidak ada data pegawai tersedia.</td>
                                </tr>
                                @endif
                            </table>

                            <p><strong>Perihal:</strong> {{ $spt->perihal_spt ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- SPT Details Section -->
                    <div class="row mb-4">
                        <div class="col-lg-12">
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td style="width: 30%;"><strong>Tempat Berangkat</strong></td>
                                        <td>{{ $spt->tempat_berangkat ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tempat Tujuan</strong></td>
                                        <td>{{ is_array($spt->tempat_tujuan) ? implode(', ', $spt->tempat_tujuan) : ($spt->tempat_tujuan ?? 'N/A') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Lamanya Perjalanan Dinas</strong></td>
                                        <td>{{ $spt->tanggal_berangkat && $spt->tanggal_kembali ? $spt->tanggal_berangkat->diffInDays($spt->tanggal_kembali) + 1 . ' Hari' : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Berangkat</strong></td>
                                        <td>{{ $spt->tanggal_berangkat ? $spt->tanggal_berangkat->format('d F Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Kembali</strong></td>
                                        <td>{{ $spt->tanggal_kembali ? $spt->tanggal_kembali->format('d F Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Beban Biaya</strong></td>
                                        <td>{{ $spt->rekening->nomor_rekening ? $spt->rekening->nomor_rekening . ".5.1.02.04.01.0001" : 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Signature Pad Section -->
                    <div class="row mb-4">
                        <div class="col-lg-12 text-center">
                            <p class="font-weight-bold" style="font-size: 1.0rem;">Tanda Tangan</p>
                            <div class="signature-pad-wrapper mx-auto">
                                <canvas id="signatureCanvas"></canvas>
                            </div>
                            <button type="button" class="btn btn-secondary mt-3 btn-block" onclick="clearSignature()">Clear Signature</button>
                        </div>
                    </div>

                    <!-- Checkbox Persetujuan -->
                    <div class="row mb-4">
                        <div class="col-lg-12 text-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="approvalCheckbox" required>
                                <label class="form-check-label" for="approvalCheckbox">
                                    Saya menyetujui isi Surat Perintah Tugas ini dan menandatanganinya secara elektronik.
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Signature -->
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <form id="signatureForm">
                                @csrf
                                <input type="hidden" name="signature" id="signatureInput">
                                <input type="hidden" name="role" value="{{ $role }}">
                                <button type="button" class="btn btn-primary btn-block" id="submitSignature" disabled onclick="saveSignature()">Submit Signature</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification -->
<div id="notification">Tanda tangan berhasil disimpan!</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Signature Pad JS -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const canvas = document.getElementById('signatureCanvas');
    const submitSignatureBtn = document.getElementById('submitSignature');
    const approvalCheckbox = document.getElementById('approvalCheckbox');
    const role = "{{ strtolower($role) }}";
    const sptId = "{{ $spt->id }}";
    const penColor = (role === 'pa') ? '#2C4C9C' : '#000000';
    const signaturePad = new SignaturePad(canvas, {
        penColor: penColor,
        backgroundColor: 'rgba(0, 0, 0, 0)',
        minWidth: 2,
        maxWidth: 4
    });
   
    function clearSignature() {
        signaturePad.clear();
    }

    // Fungsi untuk enable/disable tombol submit berdasarkan checkbox
    approvalCheckbox.addEventListener('change', function () {
        submitSignatureBtn.disabled = !approvalCheckbox.checked;
    });

    // Fungsi untuk menyimpan signature dan mengirim form via AJAX
    function saveSignature() {
    if (signaturePad.isEmpty()) {
        alert('Harap tanda tangani terlebih dahulu.');
        return;
    }

    const dataUrl = signaturePad.toDataURL('image/png');
    document.getElementById('signatureInput').value = dataUrl;

    $.ajax({
        url: "/spt/signature/" + role + "/" + sptId,
        method: 'POST',
        data: $('#signatureForm').serialize(),
        success: function(response) {
            showNotification(response.message || 'Tanda tangan berhasil disimpan!');
        },
        error: function(xhr) {
            alert('Gagal menyimpan tanda tangan. Coba lagi.');
        }
    });
}


    // Show notification
    function showNotification(message) {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.style.display = 'block';

        // Hide the notification after 3 seconds
        setTimeout(function() {
            notification.style.display = 'none';
        }, 3000);
    }

    // Menyesuaikan ukuran canvas jika ada perubahan ukuran jendela
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); // Kosongkan canvas setelah ukuran berubah
    }
</script>
</body>
</html>
