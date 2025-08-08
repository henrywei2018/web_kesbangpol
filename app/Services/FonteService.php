<?php

namespace App\Services;

use App\Settings\WhatsAppSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FonteService
{
    private string $apiUrl;
    private string $token;
    private WhatsAppSettings $settings;

    public function __construct()
    {
        $this->settings = app(WhatsAppSettings::class);
        $this->apiUrl = $this->settings->api_url ?? 'https://api.fonnte.com/send';
        $this->token = $this->settings->token ?? '';
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
     * Send notification to both user and admin
     */
    private function sendDualNotification(string $userPhone, string $userMessage, string $adminMessage, string $serviceType = 'main'): array
    {
        $results = [
            'user' => ['success' => false],
            'admin' => ['success' => false],
            'overall_success' => false
        ];

        // Check if WhatsApp is enabled
        if (!$this->settings->enabled) {
            Log::info('WhatsApp notifications disabled in settings');
            return $results;
        }

        // Send to user
        if ($userPhone) {
            $results['user'] = $this->sendMessage($userPhone, $userMessage);
            Log::info('User notification sent', [
                'phone' => $userPhone,
                'success' => $results['user']['success']
            ]);
        }

        // Send to admin(s)
        $adminPhones = $this->getAdminPhones($serviceType);
        $adminResults = [];
        
        foreach ($adminPhones as $adminPhone) {
            if ($adminPhone && $adminPhone !== $userPhone) { // Don't send duplicate if user is admin
                $adminResult = $this->sendMessage($adminPhone, $adminMessage);
                $adminResults[] = $adminResult;
                
                Log::info('Admin notification sent', [
                    'phone' => $adminPhone,
                    'service_type' => $serviceType,
                    'success' => $adminResult['success']
                ]);
            }
        }

        $results['admin'] = [
            'success' => !empty($adminResults) && collect($adminResults)->where('success', true)->isNotEmpty(),
            'results' => $adminResults
        ];

        // Overall success if either user or admin notification succeeded
        $results['overall_success'] = $results['user']['success'] || $results['admin']['success'];

        return $results;
    }

    /**
     * Get admin phone numbers for specific service type
     */
    private function getAdminPhones(string $serviceType = 'main'): array
    {
        $phones = [];

        // Add main admin (always gets notifications)
        if ($this->settings->admin_main) {
            $phones[] = $this->settings->admin_main;
        }

        // Add service-specific admin
        $serviceAdminPhone = match($serviceType) {
            'skt' => $this->settings->admin_skt,
            'skl' => $this->settings->admin_skl,
            'ppid' => $this->settings->admin_ppid,
            'athg' => $this->settings->admin_athg,
            default => null
        };

        if ($serviceAdminPhone) {
            $phones[] = $serviceAdminPhone;
        }

        // Add backup admin if no specific admin found and no main admin
        if (empty($phones) && $this->settings->admin_backup) {
            $phones[] = $this->settings->admin_backup;
        }

        return array_unique(array_filter($phones));
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

    public function sendSKTNotification(string $phoneNumber, array $data): array
    {
        $userMessage = $this->buildSKTMessage($data);
        $adminMessage = $this->buildSKTAdminMessage($data);
        
        return $this->sendDualNotification($phoneNumber, $userMessage, $adminMessage, 'skt');
    }

    /**
     * Send notification for SKL submission - DUAL VERSION
     */
    public function sendSKLNotification(string $phoneNumber, array $data): array
    {
        $userMessage = $this->buildSKLMessage($data);
        $adminMessage = $this->buildSKLAdminMessage($data);
        
        return $this->sendDualNotification($phoneNumber, $userMessage, $adminMessage, 'skl');
    }

    /**
     * Send notification for Information Request submission - DUAL VERSION
     */
    public function sendInformationRequestNotification(string $phoneNumber, array $data): array
    {
        $userMessage = $this->buildInformationRequestMessage($data);
        $adminMessage = $this->buildInformationRequestAdminMessage($data);
        
        return $this->sendDualNotification($phoneNumber, $userMessage, $adminMessage, 'ppid');
    }

    /**
     * Send notification for Information Objection submission - DUAL VERSION
     */
    public function sendInformationObjectionNotification(string $phoneNumber, array $data): array
    {
        $userMessage = $this->buildInformationObjectionMessage($data);
        $adminMessage = $this->buildInformationObjectionAdminMessage($data);
        
        return $this->sendDualNotification($phoneNumber, $userMessage, $adminMessage, 'ppid');
    }

    /**
     * Send notification for ATHG Report submission - DUAL VERSION
     */
    public function sendATHGReportNotification(string $phoneNumber, array $data): array
    {
        $userMessage = $this->buildATHGMessage($data);
        $adminMessage = $this->buildATHGAdminMessage($data);
        
        return $this->sendDualNotification($phoneNumber, $userMessage, $adminMessage, 'athg');
    }

    /**
     * Send status update notification (for both user and admin)
     */
    public function sendStatusUpdateNotification(string $phoneNumber, string $serviceType, array $data): array
    {
        $userMessage = $this->buildStatusUpdateMessage($serviceType, $data);
        $adminMessage = $this->buildStatusUpdateAdminMessage($serviceType, $data);
        
        return $this->sendDualNotification($phoneNumber, $userMessage, $adminMessage, $serviceType);
    }

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
               "Silakan pantau status melalui dashboard atau hubungi kami untuk informasi lebih lanjut.\n\n" .
               "Terima kasih atas kepercayaan Anda.\n\n" .
               "Hormat kami,\n" .
               "*Tim POKUSKALTARA*\n" .
               "Pemerintah Provinsi Kalimantan Utara";
    }

    /**
     * Build SKL submission message for user
     */
    private function buildSKLMessage(array $data): string
    {
        $namaOrganisasi = $data['nama_organisasi'] ?? 'Tidak diketahui';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "📜 *POKUSKALTARA - Konfirmasi Pengajuan SKL*\n\n" .
               "Halo,\n\n" .
               "Pengajuan Surat Keterangan Lunas (SKL) Anda telah berhasil diterima:\n\n" .
               "📋 *Detail Pengajuan:*\n" .
               "• Nama Organisasi: {$namaOrganisasi}\n" .
               "• Tanggal Submit: {$tanggalSubmit}\n" .
               "• ID Pengajuan: {$nomorRegistrasi}\n\n" .
               "📌 *Status:* Sedang dalam proses review\n\n" .
               "Kami akan memproses pengajuan Anda dalam 1-3 hari kerja. " .
               "Silakan pantau status melalui dashboard atau hubungi kami untuk informasi lebih lanjut.\n\n" .
               "Terima kasih atas kepercayaan Anda.\n\n" .
               "Hormat kami,\n" .
               "*Tim POKUSKALTARA*\n" .
               "Pemerintah Provinsi Kalimantan Utara";
    }

    /**
     * Build Information Request message for user
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
     * Build Information Objection message for user
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
     * Build ATHG Report message for user
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
               "Kami akan menghubungi Anda dalam 24 jam untuk proses lebih lanjut.\n\n" .
               "Hormat kami,\n" .
               "*Tim Investigasi ATHG*\n" .
               "Pemerintah Provinsi Kalimantan Utara";
    }

    /**
     * Build status update message for user
     */
    private function buildStatusUpdateMessage(string $serviceType, array $data): string
    {
        $status = $data['status'] ?? 'Unknown';
        $keterangan = $data['keterangan'] ?? '';
        $id = $data['id'] ?? 'N/A';
        $tanggal = now()->format('d/m/Y H:i');

        $serviceLabel = match($serviceType) {
            'skt' => 'SKT',
            'skl' => 'SKL', 
            'ppid', 'information_request' => 'Permohonan Informasi',
            'information_objection' => 'Keberatan Informasi',
            'athg', 'athg_report' => 'Laporan ATHG',
            default => 'Layanan'
        };

        $statusIcon = match($status) {
            'approved', 'disetujui', 'selesai' => '✅',
            'rejected', 'ditolak' => '❌',
            'review', 'dalam_review' => '⏳',
            default => '📌'
        };

        $message = "{$statusIcon} *UPDATE STATUS {$serviceLabel}*\n\n" .
                   "Halo,\n\n" .
                   "Ada update status untuk pengajuan Anda:\n\n" .
                   "📋 *Detail:*\n" .
                   "• ID: {$id}\n" .
                   "• Status: " . ucwords(str_replace('_', ' ', $status)) . "\n" .
                   "• Waktu Update: {$tanggal}\n";

        if ($keterangan) {
            $message .= "• Keterangan: {$keterangan}\n";
        }

        $message .= "\nSilakan cek dashboard untuk detail lengkap.\n\n" .
                    "Terima kasih,\n" .
                    "*Tim Layanan Kaltara*";

        return $message;
    }

    private function buildSKTAdminMessage(array $data): string
    {
        $namaOrmas = $data['nama_ormas'] ?? 'Tidak diketahui';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "🔔 *[ADMIN] Pengajuan SKT Baru*\n\n" .
               "Ada pengajuan SKT baru yang perlu direview:\n\n" .
               "📋 *Detail:*\n" .
               "• ORMAS: {$namaOrmas}\n" .
               "• ID: {$nomorRegistrasi}\n" .
               "• Waktu: {$tanggalSubmit}\n" .
               "• Jenis: " . ($data['jenis_permohonan'] ?? 'Pendaftaran') . "\n\n" .
               "⏰ *Action Required:* Review dalam 1x24 jam\n\n" .
               "Silakan akses dashboard admin untuk memproses.";
    }

    /**
     * Build SKL admin notification message
     */
    private function buildSKLAdminMessage(array $data): string
    {
        $namaOrganisasi = $data['nama_organisasi'] ?? 'Tidak diketahui';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "🔔 *[ADMIN] Pengajuan SKL Baru*\n\n" .
               "Ada pengajuan SKL baru yang perlu direview:\n\n" .
               "📋 *Detail:*\n" .
               "• Organisasi: {$namaOrganisasi}\n" .
               "• ID: {$nomorRegistrasi}\n" .
               "• Waktu: {$tanggalSubmit}\n\n" .
               "⏰ *Action Required:* Review dalam 1x24 jam\n\n" .
               "Silakan akses dashboard admin untuk memproses.";
    }

    /**
     * Build Information Request admin notification message
     */
    private function buildInformationRequestAdminMessage(array $data): string
    {
        $namaLengkap = $data['nama_lengkap'] ?? 'Tidak diketahui';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "🔔 *[ADMIN PPID] Permohonan Informasi Baru*\n\n" .
               "Ada permohonan informasi publik baru:\n\n" .
               "📋 *Detail:*\n" .
               "• Pemohon: {$namaLengkap}\n" .
               "• ID: {$nomorRegistrasi}\n" .
               "• Waktu: {$tanggalSubmit}\n\n" .
               "⏰ *Deadline:* Maksimal 10 hari kerja\n\n" .
               "Silakan akses dashboard PPID untuk memproses.";
    }

    /**
     * Build Information Objection admin notification message
     */
    private function buildInformationObjectionAdminMessage(array $data): string
    {
        $namaLengkap = $data['nama_lengkap'] ?? 'Tidak diketahui';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';

        return "🔔 *[ADMIN PPID] Keberatan Informasi Baru*\n\n" .
               "Ada keberatan informasi publik yang perlu ditangani:\n\n" .
               "📋 *Detail:*\n" .
               "• Pengaju: {$namaLengkap}\n" .
               "• ID: {$nomorRegistrasi}\n" .
               "• Waktu: {$tanggalSubmit}\n\n" .
               "🚨 *PRIORITY:* Keberatan perlu penanganan khusus\n\n" .
               "Silakan akses dashboard PPID segera.";
    }

    /**
     * Build ATHG admin notification message
     */
    private function buildATHGAdminMessage(array $data): string
    {
        $namaLengkap = $data['nama_lengkap'] ?? 'Pelapor';
        $tanggalSubmit = now()->format('d/m/Y H:i');
        $nomorRegistrasi = $data['id'] ?? 'Pending';
        $bidang = $data['bidang'] ?? 'Tidak ditentukan';
        $urgensi = $data['tingkat_urgensi'] ?? 'normal';

        return "🚨 *[ADMIN] Laporan ATHG Baru*\n\n" .
               "Ada laporan ATHG yang memerlukan perhatian:\n\n" .
               "📋 *Detail:*\n" .
               "• Pelapor: {$namaLengkap}\n" .
               "• ID: {$nomorRegistrasi}\n" .
               "• Bidang: " . ucfirst($bidang) . "\n" .
               "• Urgensi: " . ucfirst($urgensi) . "\n" .
               "• Waktu: {$tanggalSubmit}\n\n" .
               ($urgensi === 'tinggi' ? "🔥 *URGENT:* Perlu penanganan segera!\n\n" : "") .
               "Silakan akses dashboard untuk investigasi lebih lanjut.";
    }

    /**
     * Build status update admin notification message
     */
    private function buildStatusUpdateAdminMessage(string $serviceType, array $data): string
    {
        $status = $data['status'] ?? 'Unknown';
        $keterangan = $data['keterangan'] ?? '';
        $id = $data['id'] ?? 'N/A';
        $tanggal = now()->format('d/m/Y H:i');

        $serviceLabel = match($serviceType) {
            'skt' => 'SKT',
            'skl' => 'SKL',
            'ppid', 'information_request' => 'Permohonan Informasi',
            'information_objection' => 'Keberatan Informasi',
            'athg', 'athg_report' => 'Laporan ATHG',
            default => 'Layanan'
        };

        $message = "🔔 *[ADMIN] Update Status {$serviceLabel}*\n\n" .
                   "Status telah diupdate:\n\n" .
                   "📋 *Detail:*\n" .
                   "• ID: {$id}\n" .
                   "• Status Baru: " . ucwords(str_replace('_', ' ', $status)) . "\n" .
                   "• Waktu: {$tanggal}\n";

        if ($keterangan) {
            $message .= "• Keterangan: {$keterangan}\n";
        }

        $message .= "\nNotifikasi telah dikirim ke user.";

        return $message;
    }
}