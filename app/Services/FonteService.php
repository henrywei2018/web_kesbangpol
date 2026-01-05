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
        
        // Get API URL and token with proper type-specific cleaning
        $this->apiUrl = $this->cleanSettingValue($this->settings->api_url ?? 'https://api.fonnte.com/send', 'url');
        $this->token = $this->cleanSettingValue($this->settings->token ?? '', 'general');
    }

    /**
     * Clean setting value - handle different types of settings safely
     */
    private function cleanSettingValue(?string $value, string $type = 'general'): string
    {
        if (empty($value)) {
            return '';
        }

        // Handle based on setting type
        switch ($type) {
            case 'template':
                // For templates, only remove outer quotes but preserve all formatting
                return $this->cleanTemplate($value);
                
            case 'phone':
                // For phone numbers, remove quotes and trim all whitespace
                return trim(trim($value, '"\''));
                
            case 'group_id':
                // For group IDs, remove quotes and trim but preserve @ symbols
                return trim(trim($value, '"\''));
                
            case 'url':
                // For URLs, remove quotes and trim
                return trim(trim($value, '"\''));
                
            default:
                // General cleaning
                return trim(trim($value, '"\''));
        }
    }

    /**
     * Clean template value while preserving formatting
     */
    private function cleanTemplate(string $template): string
    {
        // Only trim the very beginning and end, preserve all internal formatting
        $cleaned = trim($template);
        
        // Remove outer quotes if they wrap the entire string
        if ((str_starts_with($cleaned, '"') && str_ends_with($cleaned, '"')) ||
            (str_starts_with($cleaned, "'") && str_ends_with($cleaned, "'"))) {
            $cleaned = substr($cleaned, 1, -1);
        }
        
        // Handle JSON escaped strings
        if (str_contains($cleaned, '\\n')) {
            $cleaned = str_replace('\\n', "\n", $cleaned);
        }
        if (str_contains($cleaned, '\\r')) {
            $cleaned = str_replace('\\r', "\r", $cleaned);
        }
        if (str_contains($cleaned, '\\"')) {
            $cleaned = str_replace('\\"', '"', $cleaned);
        }
        
        return $cleaned;
    }

    /**
     * Check if WhatsApp notifications are enabled
     */
    private function isEnabled(): bool
    {
        return $this->settings->enabled ?? false;
    }

    /**
     * Send WhatsApp message via Fonnte
     */
    public function sendMessage(string $target, string $message, array $options = []): array
    {
        // Check if WhatsApp is enabled
        if (!$this->isEnabled()) {
            Log::info('WhatsApp notifications disabled');
            return [
                'success' => false,
                'error' => 'WhatsApp notifications are disabled'
            ];
        }

        // Check if token is configured
        if (empty($this->token)) {
            Log::error('WhatsApp token not configured');
            return [
                'success' => false,
                'error' => 'WhatsApp token not configured'
            ];
        }

        // Check if target is a group ID
        if ($this->isValidGroupId($target)) {
            return $this->sendGroupMessage($target, $message, $options);
        }

        try {
            // Validate phone number format untuk nomor biasa
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
     * Send WhatsApp message to group
     */
    public function sendGroupMessage(string $groupId, string $message, array $options = []): array
    {
        // Check if WhatsApp is enabled
        if (!$this->isEnabled()) {
            Log::info('WhatsApp notifications disabled');
            return [
                'success' => false,
                'error' => 'WhatsApp notifications are disabled'
            ];
        }

        try {
            // Clean group ID value with proper type
            $groupId = $this->cleanSettingValue($groupId, 'group_id');
            
            // Validasi format grup ID
            if (!$this->isValidGroupId($groupId)) {
                throw new Exception('Invalid group ID format');
            }

            $payload = [
                'target' => $groupId,
                'message' => $message,
            ];

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

            Log::info('Fontte WhatsApp Group Response', [
                'target' => $groupId,
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
            Log::error('Fontte WhatsApp Group Error', [
                'target' => $groupId ?? 'unknown',
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
     * Send SKT notification using template from settings
     */
    public function sendSKTNotification(string $target, array $data): array
    {
        // Ambil template dari settings dengan proper template cleaning
        $userTemplate = $this->cleanSettingValue($this->settings->user_template_skt ?? '', 'template') 
                       ?: $this->getDefaultSKTUserTemplate();
        
        $message = $this->buildMessageFromTemplate($userTemplate, [
            'id' => $data['id'] ?? 'N/A',
            'nama_ormas' => $data['nama_ormas'] ?? 'N/A',
            'jenis_permohonan' => $data['jenis_permohonan'] ?? 'N/A',
            'nama_pemohon' => $data['nama_pemohon'] ?? 'N/A',
            'tanggal_pengajuan' => $data['tanggal_pengajuan'] ?? now()->format('d/m/Y H:i'),
        ]);
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send SKL notification using template from settings
     */
    public function sendSKLNotification(string $target, array $data): array
    {
        $userTemplate = $this->cleanSettingValue($this->settings->user_template_skl ?? '', 'template') 
                       ?: $this->getDefaultSKLUserTemplate();
        
        $message = $this->buildMessageFromTemplate($userTemplate, [
            'id' => $data['id'] ?? 'N/A',
            'nama_organisasi' => $data['nama_organisasi'] ?? 'N/A',
            'email_organisasi' => $data['email_organisasi'] ?? 'N/A',
            'nama_pemohon' => $data['nama_pemohon'] ?? 'N/A',
            'tanggal_pengajuan' => $data['tanggal_pengajuan'] ?? now()->format('d/m/Y H:i'),
        ]);
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send Information Request notification using template from settings
     */
    public function sendInformationRequestNotification(string $target, array $data): array
    {
        $userTemplate = $this->cleanSettingValue($this->settings->user_template_ppid ?? '', 'template') 
                       ?: $this->getDefaultPPIDUserTemplate();
        
        $message = $this->buildMessageFromTemplate($userTemplate, [
            'id' => $data['id'] ?? 'N/A',
            'nama_lengkap' => $data['nama_lengkap'] ?? 'N/A',
            'rincian_informasi' => $data['rincian_informasi'] ?? 'N/A',
            'tanggal_pengajuan' => $data['tanggal_pengajuan'] ?? now()->format('d/m/Y H:i'),
        ]);
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send Information Objection notification using template from settings
     */
    public function sendInformationObjectionNotification(string $target, array $data): array
    {
        $userTemplate = $this->cleanSettingValue($this->settings->user_template_ppid ?? '', 'template') 
                       ?: $this->getDefaultPPIDObjectionTemplate();
        
        $message = $this->buildMessageFromTemplate($userTemplate, [
            'id' => $data['id'] ?? 'N/A',
            'nama_lengkap' => $data['nama_lengkap'] ?? 'N/A',
            'alasan_keberatan' => $data['alasan_keberatan'] ?? 'N/A',
            'tanggal_pengajuan' => $data['tanggal_pengajuan'] ?? now()->format('d/m/Y H:i'),
        ]);
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send ATHG Report notification using template from settings
     */
    public function sendATHGReportNotification(string $target, array $data): array
    {
        $userTemplate = $this->cleanSettingValue($this->settings->user_template_athg ?? '', 'template') 
                       ?: $this->getDefaultATHGUserTemplate();
        
        $message = $this->buildMessageFromTemplate($userTemplate, [
            'lapathg_id' => $data['lapathg_id'] ?? $data['id'] ?? 'N/A',
            'nama_pelapor' => $data['nama_pelapor'] ?? 'N/A',
            'bidang' => $data['bidang'] ?? 'N/A',
            'jenis_athg' => $data['jenis_athg'] ?? 'N/A',
            'perihal' => $data['perihal'] ?? 'N/A',
            'tanggal_pengajuan' => $data['tanggal_pengajuan'] ?? now()->format('d/m/Y H:i'),
            'lokasi' => $data['lokasi'] ?? 'N/A',
            'detail_kejadian' => $data['detail_kejadian'] ?? 'N/A',
            'sumber_informasi' => $data['sumber_informasi'] ?? 'N/A',
        ]);

        return $this->sendMessage($target, $message);
    }

    /**
     * Send ATHG Admin notification using template from settings
     */
    public function sendATHGAdminNotification(array $data): array
    {
        // Get admin phone with proper phone cleaning
        $adminPhone = $this->cleanSettingValue($this->settings->admin_athg ?? '', 'phone') 
                     ?: $this->cleanSettingValue($this->settings->admin_main ?? '', 'phone');
        
        if (empty($adminPhone)) {
            Log::info('ATHG Admin WhatsApp notification skipped - no admin phone configured');
            return ['success' => false, 'error' => 'No ATHG admin phone configured'];
        }

        $adminTemplate = $this->cleanSettingValue($this->settings->admin_template_athg ?? '', 'template') 
                        ?: $this->getDefaultATHGAdminTemplate();

        $message = $this->buildMessageFromTemplate($adminTemplate, [
            'id' => $data['id'] ?? 'N/A',
            'lapathg_id' => $data['lapathg_id'] ?? $data['id'] ?? 'N/A',
            'bidang' => $data['bidang'] ?? 'N/A',
            'jenis_athg' => $data['jenis_athg'] ?? 'N/A',
            'perihal' => $data['perihal'] ?? 'N/A',
            'status' => $data['status'] ?? 'pending',
            'tanggal' => $data['tanggal'] ?? now()->format('d/m/Y H:i'),
            'nama_pemohon' => $data['nama_pelapor'] ?? $data['nama_pemohon'] ?? 'N/A',
            'lokasi' => $data['lokasi'] ?? 'N/A',
            'detail_kejadian' => $data['detail_kejadian'] ?? 'N/A',
            'sumber_informasi' => $data['sumber_informasi'] ?? 'N/A',
        ]);

        return $this->sendMessage($adminPhone, $message);
    }

    /**
     * Send ATHG Group notification using template from settings
     */
    public function sendATHGGroupNotification(array $data): array
    {
        // Get group ID with proper group cleaning
        $groupId = $this->cleanSettingValue($this->settings->group_athg ?? '', 'group_id');
        
        if (empty($groupId)) {
            Log::info('ATHG Group WhatsApp notification skipped - no group ID configured');
            return ['success' => false, 'error' => 'No ATHG group ID configured'];
        }

        $adminTemplate = $this->cleanSettingValue($this->settings->admin_template_athg ?? '', 'template') 
                        ?: $this->getDefaultATHGAdminTemplate();

        $message = $this->buildMessageFromTemplate($adminTemplate, [
            'id' => $data['id'] ?? 'N/A',
            'lapathg_id' => $data['lapathg_id'] ?? $data['id'] ?? 'N/A',
            'bidang' => $data['bidang'] ?? 'N/A',
            'jenis_athg' => $data['jenis_athg'] ?? 'N/A',
            'perihal' => $data['perihal'] ?? 'N/A',
            'status' => $data['status'] ?? 'pending',
            'tanggal' => $data['tanggal'] ?? now()->format('d/m/Y H:i'),
            'nama_pemohon' => $data['nama_pelapor'] ?? $data['nama_pemohon'] ?? 'N/A',
            'lokasi' => $data['lokasi'] ?? 'N/A',
            'detail_kejadian' => $data['detail_kejadian'] ?? 'N/A',
            'sumber_informasi' => $data['sumber_informasi'] ?? 'N/A',
        ]);

        return $this->sendGroupMessage($groupId, $message);
    }

    /**
     * Send Lapor Giat notification using template from settings
     */
    public function sendLaporGiatNotification(string $target, array $data): array
    {
        // Untuk sementara gunakan template sederhana, bisa ditambahkan ke settings nanti
        $template = '📋 *Notifikasi Laporan Kegiatan*

Halo {nama_pemohon}!

Laporan kegiatan Anda telah diterima dan sedang ditinjau.

🏢 *Detail Laporan:*
• ID: {id}
• Nama Ormas: {nama_ormas}
• Ketua: {ketua_nama_lengkap}
• Tanggal Kegiatan: {tanggal_kegiatan}
• Tanggal Pengajuan: {tanggal_pengajuan}

📝 Laporan Anda akan segera ditinjau oleh tim admin.

✅ Anda akan mendapat notifikasi setelah proses review selesai.

Terima kasih atas partisipasi Anda! 🙏';

        $message = $this->buildMessageFromTemplate($template, [
            'id' => $data['id'] ?? 'N/A',
            'nama_ormas' => $data['nama_ormas'] ?? 'N/A',
            'ketua_nama_lengkap' => $data['ketua_nama_lengkap'] ?? 'N/A',
            'tanggal_kegiatan' => $data['tanggal_kegiatan'] ?? 'N/A',
            'nama_pemohon' => $data['nama_pemohon'] ?? 'N/A',
            'tanggal_pengajuan' => $data['tanggal_pengajuan'] ?? now()->format('d/m/Y H:i'),
        ]);
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send admin notification for new submissions (generic method)
     */
    public function sendAdminNotification(string $serviceType, array $data): array
    {
        // Determine which specific admin method to call based on service type
        return match ($serviceType) {
            'skt' => $this->sendSKTAdminNotification($data),
            'skl' => $this->sendSKLAdminNotification($data),
            'information_request' => $this->sendPPIDAdminNotification($data),
            'information_objection' => $this->sendPPIDAdminNotification($data),
            'athg_report' => $this->sendATHGAdminNotification($data),
            'lapor_giat' => $this->sendLaporGiatAdminNotification($data),
            default => [
                'success' => false,
                'error' => 'Unknown service type: ' . $serviceType
            ]
        };
    }

    /**
     * Send SKT Admin notification
     */
    public function sendSKTAdminNotification(array $data): array
    {
        $adminPhone = $this->cleanSettingValue($this->settings->admin_skt ?? '', 'phone') 
                     ?: $this->cleanSettingValue($this->settings->admin_main ?? '', 'phone');
        
        if (empty($adminPhone)) {
            Log::info('SKT Admin WhatsApp notification skipped - no admin phone configured');
            return ['success' => false, 'error' => 'No SKT admin phone configured'];
        }

        $adminTemplate = $this->cleanSettingValue($this->settings->admin_template_skt ?? '', 'template') 
                        ?: $this->getDefaultSKTAdminTemplate();

        $message = $this->buildMessageFromTemplate($adminTemplate, [
            'id' => $data['id'] ?? 'N/A',
            'nama_ormas' => $data['nama_ormas'] ?? 'N/A',
            'jenis_permohonan' => $data['jenis_permohonan'] ?? 'N/A',
            'nama_pemohon' => $data['nama_pemohon'] ?? 'N/A',
            'status' => $data['status'] ?? 'pending',
            'tanggal' => $data['tanggal'] ?? now()->format('d/m/Y H:i'),
        ]);

        return $this->sendMessage($adminPhone, $message);
    }

    /**
     * Send SKL Admin notification
     */
    public function sendSKLAdminNotification(array $data): array
    {
        $adminPhone = $this->cleanSettingValue($this->settings->admin_skl ?? '', 'phone') 
                     ?: $this->cleanSettingValue($this->settings->admin_main ?? '', 'phone');
        
        if (empty($adminPhone)) {
            Log::info('SKL Admin WhatsApp notification skipped - no admin phone configured');
            return ['success' => false, 'error' => 'No SKL admin phone configured'];
        }

        $adminTemplate = $this->cleanSettingValue($this->settings->admin_template_skl ?? '', 'template') 
                        ?: $this->getDefaultSKLAdminTemplate();

        $message = $this->buildMessageFromTemplate($adminTemplate, [
            'id' => $data['id'] ?? 'N/A',
            'nama_organisasi' => $data['nama_organisasi'] ?? 'N/A',
            'email_organisasi' => $data['email_organisasi'] ?? 'N/A',
            'nama_pemohon' => $data['nama_pemohon'] ?? 'N/A',
            'status' => $data['status'] ?? 'pending',
            'tanggal' => $data['tanggal'] ?? now()->format('d/m/Y H:i'),
        ]);

        return $this->sendMessage($adminPhone, $message);
    }

    /**
     * Send PPID Admin notification
     */
    public function sendPPIDAdminNotification(array $data): array
    {
        $adminPhone = $this->cleanSettingValue($this->settings->admin_ppid ?? '', 'phone') 
                     ?: $this->cleanSettingValue($this->settings->admin_main ?? '', 'phone');
        
        if (empty($adminPhone)) {
            Log::info('PPID Admin WhatsApp notification skipped - no admin phone configured');
            return ['success' => false, 'error' => 'No PPID admin phone configured'];
        }

        $adminTemplate = $this->cleanSettingValue($this->settings->admin_template_ppid ?? '', 'template') 
                        ?: $this->getDefaultPPIDAdminTemplate();

        $message = $this->buildMessageFromTemplate($adminTemplate, [
            'id' => $data['id'] ?? 'N/A',
            'nama_lengkap' => $data['nama_lengkap'] ?? $data['nama_pemohon'] ?? 'N/A',
            'rincian_informasi' => $data['rincian_informasi'] ?? 'N/A',
            'status' => $data['status'] ?? 'pending',
            'tanggal' => $data['tanggal'] ?? now()->format('d/m/Y H:i'),
        ]);

        return $this->sendMessage($adminPhone, $message);
    }

    /**
     * Send Lapor Giat Admin notification
     */
    public function sendLaporGiatAdminNotification(array $data): array
    {
        $adminPhone = $this->cleanSettingValue($this->settings->admin_main ?? '', 'phone');
        
        if (empty($adminPhone)) {
            Log::info('Lapor Giat Admin WhatsApp notification skipped - no admin phone configured');
            return ['success' => false, 'error' => 'No admin phone configured'];
        }

        $template = '🔔 *NOTIFIKASI ADMIN - LAPORAN KEGIATAN*

Ada laporan kegiatan baru yang perlu ditinjau:

📋 *Detail:*
• ID: {id}
• Nama Ormas: {nama_ormas}
• Ketua: {ketua_nama_lengkap}
• Tanggal Kegiatan: {tanggal_kegiatan}
• Pemohon: {nama_pemohon}
• Status: {status}
• Tanggal: {tanggal}

Silakan cek panel admin untuk detail lengkap.';

        $message = $this->buildMessageFromTemplate($template, [
            'id' => $data['id'] ?? 'N/A',
            'nama_ormas' => $data['nama_ormas'] ?? 'N/A',
            'ketua_nama_lengkap' => $data['ketua_nama_lengkap'] ?? 'N/A',
            'tanggal_kegiatan' => $data['tanggal_kegiatan'] ?? 'N/A',
            'nama_pemohon' => $data['nama_pemohon'] ?? 'N/A',
            'status' => $data['status'] ?? 'pending',
            'tanggal' => $data['tanggal'] ?? now()->format('d/m/Y H:i'),
        ]);

        return $this->sendMessage($adminPhone, $message);
    }

    /**
     * Send status update notification
     */
    public function sendStatusUpdate(string $target, string $serviceType, array $data): array
    {
        // Default template untuk status update
        $template = '📢 *Update Status {service_name}*

🆔 ID: {id}
📊 Status: *{status_display}*

📝 Keterangan: {keterangan}

⏰ Diperbarui: {timestamp}

Terima kasih! 🙏';

        $message = $this->buildMessageFromTemplate($template, [
            'service_name' => $data['service_name'] ?? $this->getServiceName($serviceType),
            'id' => $data['id'] ?? 'N/A',
            'status' => $data['status'] ?? 'N/A',
            'status_display' => $data['status_display'] ?? ucfirst($data['status'] ?? 'N/A'),
            'keterangan' => $data['keterangan'] ?? 'Tidak ada keterangan',
            'timestamp' => $data['timestamp'] ?? now()->format('d/m/Y H:i')
        ]);
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Get service name by type
     */
    private function getServiceName(string $serviceType): string
    {
        return match ($serviceType) {
            'skt' => 'SKT',
            'skl' => 'SKL', 
            'information_request' => 'Permohonan Informasi Publik',
            'information_objection' => 'Keberatan Informasi Publik',
            'athg_report' => 'Laporan ATHG',
            'lapor_giat' => 'Laporan Kegiatan',
            default => 'Layanan'
        };
    }

    /**
     * Build message from template dengan replace variables
     */
    private function buildMessageFromTemplate(string $template, array $data): string
    {
        $message = $template;
        
        foreach ($data as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
        
        return $message;
    }

    /**
     * Default templates
     */
    private function getDefaultSKTUserTemplate(): string
    {
        return '🔔 *Notifikasi SKT*

Halo {nama_pemohon}!

Permohonan SKT Anda telah diterima dan sedang diproses.

📋 *Detail Permohonan:*
• ID: {id}
• Nama Ormas: {nama_ormas}
• Jenis: {jenis_permohonan}
• Tanggal: {tanggal_pengajuan}

✅ Tim kami akan segera memproses permohonan Anda.

Terima kasih atas kepercayaan Anda! 🙏';
    }

    private function getDefaultSKLUserTemplate(): string
    {
        return '🔔 *Notifikasi SKL*

Halo {nama_pemohon}!

Permohonan SKL Anda telah diterima dan sedang diproses.

📋 *Detail Permohonan:*
• ID: {id}
• Nama Organisasi: {nama_organisasi}
• Email: {email_organisasi}
• Tanggal: {tanggal_pengajuan}

✅ Tim kami akan segera memproses permohonan Anda.

Terima kasih atas kepercayaan Anda! 🙏';
    }

    private function getDefaultPPIDUserTemplate(): string
    {
        return '🔔 *Notifikasi Permohonan Informasi Publik*

Halo {nama_lengkap}!

Permohonan informasi publik Anda telah diterima dan sedang diproses.

📋 *Detail Permohonan:*
• ID: {id}
• Nama: {nama_lengkap}
• Rincian: {rincian_informasi}
• Tanggal: {tanggal_pengajuan}

✅ Tim kami akan segera memproses permohonan Anda sesuai dengan ketentuan yang berlaku.

Terima kasih atas kepercayaan Anda! 🙏';
    }

    private function getDefaultPPIDObjectionTemplate(): string
    {
        return '🔔 *Notifikasi Keberatan Informasi Publik*

Halo {nama_lengkap}!

Keberatan informasi publik Anda telah diterima dan sedang ditinjau.

📋 *Detail Keberatan:*
• ID: {id}
• Nama: {nama_lengkap}
• Alasan: {alasan_keberatan}
• Tanggal: {tanggal_pengajuan}

✅ Tim kami akan segera meninjau keberatan Anda sesuai dengan prosedur yang berlaku.

Terima kasih atas kepercayaan Anda! 🙏';
    }

    private function getDefaultATHGUserTemplate(): string
    {
        return '🚨 *Notifikasi Laporan ATHG*

Halo {nama_pelapor}!

Laporan ATHG Anda telah diterima dan sedang ditinjau.

📋 *Detail Laporan:*
• ID: {lapathg_id}
• Bidang: {bidang}
• Jenis ATHG: {jenis_athg}
• Perihal: {perihal}
• Lokasi: {lokasi}
• Tanggal: {tanggal_pengajuan}

🔒 Laporan Anda akan ditangani dengan kerahasiaan tinggi.

✅ Tim kami akan segera menindaklanjuti sesuai prosedur.

Terima kasih atas partisipasi Anda! 🙏';
    }

    private function getDefaultATHGAdminTemplate(): string
    {
        return '🚨 *LAPORAN ATHG - POKUS KALTARA*

Ada laporan ATHG yang perlu perhatian:

📋 *Detail:*
• ID Laporan: {id}
• ID ATHG: {lapathg_id}
• Bidang: {bidang}
• Jenis ATHG: {jenis_athg}
• Perihal: {perihal}
• Lokasi: {lokasi}
• Status: {status}
• Tanggal: {tanggal}

🚨 *PERHATIAN: Informasi sensitif - Tangani sesuai prosedur*

Silakan cek panel admin untuk detail lengkap.';
    }

    private function getDefaultSKTAdminTemplate(): string
    {
        return '🔔 *NOTIFIKASI SKT - KESBANGPOL KALTARA*

Ada pengajuan SKT yang perlu perhatian:

📋 *Detail:*
• ID: {id}
• Nama Ormas: {nama_ormas}
• Jenis: {jenis_permohonan}
• Pemohon: {nama_pemohon}
• Status: {status}
• Tanggal: {tanggal}

Silakan cek panel admin untuk detail lengkap.';
    }

    private function getDefaultSKLAdminTemplate(): string
    {
        return '🔔 *NOTIFIKASI SKL - KESBANGPOL KALTARA*

Ada pengajuan SKL yang perlu perhatian:

📋 *Detail:*
• ID: {id}
• Nama Organisasi: {nama_organisasi}
• Email: {email_organisasi}
• Pemohon: {nama_pemohon}
• Status: {status}
• Tanggal: {tanggal}

Silakan cek panel admin untuk detail lengkap.';
    }

    private function getDefaultPPIDAdminTemplate(): string
    {
        return '🔔 *NOTIFIKASI PPID - KESBANGPOL KALTARA*

Ada permohonan informasi publik yang perlu perhatian:

📋 *Detail:*
• ID: {id}
• Nama Pemohon: {nama_lengkap}
• Rincian: {rincian_informasi}
• Status: {status}
• Tanggal: {tanggal}

Silakan cek panel admin untuk detail lengkap.';
    }

    /**
     * Validate if target is a group ID
     */
    private function isValidGroupId(string $target): bool
    {
        return str_contains($target, '@g.us') && strlen($target) > 15;
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
            $phone = '628' . substr($phone, 2);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        } elseif (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        if (strlen($phone) < 10 || strlen($phone) > 15) {
            return null;
        }

        return $phone;
    }

    /**
     * Test connection to Fontte API
     */
    public function testConnection(): array
    {
        if (empty($this->token)) {
            return [
                'success' => false,
                'error' => 'WhatsApp token not configured'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->get('https://api.fontte.com/validate');

            return [
                'success' => $response->successful(),
                'response' => $response->json(),
                'status' => $response->status()
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}