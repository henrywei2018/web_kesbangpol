<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FonteService
{
    private string $apiUrl;
    private string $token;

    public function __construct()
    {
        $this->apiUrl = config('services.fonnte.api_url', 'https://api.fonnte.com/send');
        $this->token = config('services.fonnte.token');
    }

    /**
     * Send WhatsApp message via Fonnte
     */
    public function sendMessage(string $target, string $message, array $options = []): array
    {
        try {
            // Validate phone number format
            $target = $this->formatPhoneNumber($target);
            
            if (!$target) {
                throw new Exception('Invalid phone number format');
            }

            $payload = [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Indonesia
            ];

            // Add optional parameters
            if (isset($options['delay'])) {
                $payload['delay'] = $options['delay'];
            }

            if (isset($options['schedule'])) {
                $payload['schedule'] = $options['schedule'];
            }

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, $payload);

            $result = $response->json();

            // Log the response for debugging
            Log::info('Fonnte WhatsApp Response', [
                'target' => $target,
                'status' => $response->status(),
                'response' => $result
            ]);

            return [
                'success' => $response->successful() && ($result['status'] ?? false),
                'response' => $result,
                'message_id' => $result['id'] ?? null,
                'status' => $result['status'] ?? false,
                'reason' => $result['reason'] ?? null
            ];

        } catch (Exception $e) {
            Log::error('Fonnte WhatsApp Error', [
                'target' => $target ?? 'unknown',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => null
            ];
        }
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber(string $phoneNumber): ?string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        if (empty($phone)) {
            return null;
        }

        // Handle Indonesian phone numbers
        if (str_starts_with($phone, '08')) {
            // Convert 08xxx to 628xxx
            $phone = '628' . substr($phone, 2);
        } elseif (str_starts_with($phone, '8')) {
            // Convert 8xxx to 628xxx
            $phone = '62' . $phone;
        } elseif (str_starts_with($phone, '0')) {
            // Convert 0xxx to 62xxx
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            // Add 62 prefix if not present
            $phone = '62' . $phone;
        }

        // Validate minimum length (Indonesian mobile numbers)
        if (strlen($phone) < 10 || strlen($phone) > 15) {
            return null;
        }

        return $phone;
    }

    /**
     * Send notification for SKT submission
     */
    public function sendSKTNotification(string $phoneNumber, array $data): array
    {
        $message = $this->buildSKTMessage($data);
        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Send notification for SKL submission
     */
    public function sendSKLNotification(string $phoneNumber, array $data): array
    {
        $message = $this->buildSKLMessage($data);
        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Send notification for Information Request submission
     */
    public function sendInformationRequestNotification(string $phoneNumber, array $data): array
    {
        $message = $this->buildInformationRequestMessage($data);
        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Send notification for Information Objection submission
     */
    public function sendInformationObjectionNotification(string $phoneNumber, array $data): array
    {
        $message = $this->buildInformationObjectionMessage($data);
        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Send notification for ATHG Report submission
     */
    public function sendATHGReportNotification(string $phoneNumber, array $data): array
    {
        $message = $this->buildATHGMessage($data);
        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Build SKT submission message
     */
    private function buildSKTMessage(array $data): string
    {
        $namaOrmas = $data['nama_ormas'] ?? 'Tidak diketahui';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "🏢 *POKUSKALTARA - Konfirmasi Pengajuan SKT*\n\n" .
               "Halo,\n\n" .
               "Pengajuan Surat Keterangan Terdaftar (SKT) Anda telah berhasil diterima:\n\n" .
               "📋 *Detail Pengajuan:*\n" .
               "• Nama ORMAS: {$namaOrmas}\n" .
               "• Tanggal Submit: {$tanggalSubmit}\n" .
               "• ID Pengajuan: {$nomorRegistrasi}\n" .
               "• Jenis: " . ($data['jenis_permohonan'] ?? 'Pendaftaran') . "\n\n" .
               "📌 *Status:* Sedang dalam proses review\n\n" .
               "Kami akan memproses pengajuan Anda dalam 1-3 hari kerja. " .
               "Silakan pantau status melalui dashboard panel atau hubungi kami jika ada pertanyaan.\n\n" .
               "Hormat kami,\n" .
               "*Tim POKUSKALTARA*\n" .
               "Pemerintah Provinsi Kalimantan Utara";
    }

    /**
     * Build SKL submission message
     */
    private function buildSKLMessage(array $data): string
    {
        $namaOrganisasi = $data['nama_organisasi'] ?? $data['email_organisasi'] ?? 'Tidak diketahui';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "📋 *POKUSKALTARA - Konfirmasi Laporan ORMAS*\n\n" .
               "Halo,\n\n" .
               "Laporan Organisasi Kemasyarakatan (SKL) Anda telah berhasil diterima:\n\n" .
               "📋 *Detail Laporan:*\n" .
               "• Nama Organisasi: {$namaOrganisasi}\n" .
               "• Tanggal Submit: {$tanggalSubmit}\n" .
               "• ID Laporan: {$nomorRegistrasi}\n\n" .
               "📌 *Status:* Sedang dalam proses review\n\n" .
               "Terima kasih atas laporan kegiatan organisasi Anda. " .
               "Tim kami akan mereview dan memberikan feedback dalam 2-5 hari kerja.\n\n" .
               "Hormat kami,\n" .
               "*Tim POKUSKALTARA*\n" .
               "Pemerintah Provinsi Kalimantan Utara";
    }

    /**
     * Build Information Request message
     */
    private function buildInformationRequestMessage(array $data): string
    {
        $namaLengkap = $data['nama_lengkap'] ?? 'Tidak diketahui';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "📄 *PPID KALTARA - Konfirmasi Permohonan Informasi*\n\n" .
               "Halo {$namaLengkap},\n\n" .
               "Permohonan informasi publik Anda telah berhasil diterima:\n\n" .
               "📋 *Detail Permohonan:*\n" .
               "• Nama Pemohon: {$namaLengkap}\n" .
               "• Tanggal Submit: {$tanggalSubmit}\n" .
               "• Nomor Registrasi: {$nomorRegistrasi}\n\n" .
               "📌 *Status:* Sedang dalam proses review\n\n" .
               "Sesuai dengan UU No. 14 Tahun 2008, kami akan merespons permohonan Anda " .
               "dalam waktu maksimal 10 (sepuluh) hari kerja.\n\n" .
               "Silakan pantau status melalui dashboard atau hubungi PPID untuk informasi lebih lanjut.\n\n" .
               "Hormat kami,\n" .
               "*PPID Kalimantan Utara*\n" .
               "Pemerintah Provinsi Kalimantan Utara";
    }

    /**
     * Build Information Objection message
     */
    private function buildInformationObjectionMessage(array $data): string
    {
        $namaLengkap = $data['nama_lengkap'] ?? 'Tidak diketahui';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "⚖️ *PPID KALTARA - Konfirmasi Keberatan Informasi*\n\n" .
               "Halo {$namaLengkap},\n\n" .
               "Keberatan informasi publik Anda telah berhasil diterima:\n\n" .
               "📋 *Detail Keberatan:*\n" .
               "• Nama Pengaju: {$namaLengkap}\n" .
               "• Tanggal Submit: {$tanggalSubmit}\n" .
               "• Nomor Registrasi: {$nomorRegistrasi}\n\n" .
               "📌 *Status:* Sedang dalam proses review\n\n" .
               "Tim kami akan meninjau keberatan Anda dengan seksama. " .
               "Proses penanganan keberatan akan dilakukan sesuai dengan ketentuan peraturan perundang-undangan.\n\n" .
               "Kami akan menghubungi Anda untuk update status dalam 7 hari kerja.\n\n" .
               "Hormat kami,\n" .
               "*PPID Kalimantan Utara*\n" .
               "Pemerintah Provinsi Kalimantan Utara";
    }

    /**
     * Build ATHG Report message
     */
    private function buildATHGMessage(array $data): string
    {
        $namaLengkap = $data['nama_lengkap'] ?? 'Pelapor';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "🚨 *KALTARA - Konfirmasi Laporan ATHG*\n\n" .
               "Halo {$namaLengkap},\n\n" .
               "Laporan dugaan Aparatur Tidak Harmonis dan Gratifikasi (ATHG) Anda telah berhasil diterima:\n\n" .
               "📋 *Detail Laporan:*\n" .
               "• Nama Pelapor: {$namaLengkap}\n" .
               "• Tanggal Submit: {$tanggalSubmit}\n" .
               "• Nomor Registrasi: {$nomorRegistrasi}\n\n" .
               "📌 *Status:* Sedang dalam proses review\n\n" .
               "⚠️ *PENTING:*\n" .
               "• Laporan Anda akan ditangani dengan kerahasiaan tinggi\n" .
               "• Tim investigasi akan menindaklanjuti sesuai prosedur\n" .
               "• Identitas pelapor akan dijaga kerahasiaannya\n\n" .
               "Terima kasih atas partisipasi Anda dalam menciptakan pemerintahan yang bersih.\n\n" .
               "Hormat kami,\n" .
               "*Tim Anti Korupsi*\n" .
               "Pemerintah Provinsi Kalimantan Utara";
    }

    /**
     * Send status update notification
     */
    public function sendStatusUpdate(string $phoneNumber, string $serviceType, array $data): array
    {
        $message = $this->buildStatusUpdateMessage($serviceType, $data);
        return $this->sendMessage($phoneNumber, $message);
    }

    /**
     * Build status update message
     */
    private function buildStatusUpdateMessage(string $serviceType, array $data): string
    {
        $status = $data['status'] ?? 'Unknown';
        $nomorRegistrasi = $data['id'] ?? 'Pending';
        $keterangan = $data['keterangan'] ?? '';
        
        $serviceNames = [
            'skt' => 'SKT (Surat Keterangan Terdaftar)',
            'skl' => 'SKL (Laporan ORMAS)',
            'information_request' => 'Permohonan Informasi Publik',
            'information_objection' => 'Keberatan Informasi Publik',
            'athg_report' => 'Laporan ATHG'
        ];

        $serviceName = $serviceNames[$serviceType] ?? 'Layanan';

        return "🔔 *Update Status Layanan*\n\n" .
               "Halo,\n\n" .
               "Ada update status untuk pengajuan {$serviceName} Anda:\n\n" .
               "📋 *Detail Update:*\n" .
               "• Nomor Registrasi: {$nomorRegistrasi}\n" .
               "• Status Terbaru: *{$status}*\n" .
               "• Tanggal Update: " . now()->format('d/m/Y H:i') . "\n" .
               ($keterangan ? "• Keterangan: {$keterangan}\n" : '') . "\n" .
               "Silakan login ke dashboard untuk informasi lebih detail.\n\n" .
               "Hormat kami,\n" .
               "*Tim Layanan Publik*\n" .
               "Pemerintah Provinsi Kalimantan Utara";
    }
}