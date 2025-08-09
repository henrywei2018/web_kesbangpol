<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class FonteService
{
    private string $apiUrl;
    private string $token;

    public function __construct()
    {
        // Get settings from database instead of config
        $this->apiUrl = $this->getSetting('whatsapp.api_url', 'https://api.fonnte.com/send');
        $this->token = $this->getSetting('whatsapp.token');
    }

    /**
     * Get setting value from database
     */
    private function getSetting(string $key, string $default = ''): string
    {
        try {
            $setting = DB::table('settings')
                ->where('group', 'whatsapp')
                ->where('name', str_replace('whatsapp.', '', $key))
                ->first();
            
            return $setting ? $setting->payload : $default;
        } catch (Exception $e) {
            Log::warning('Failed to get setting', ['key' => $key, 'error' => $e->getMessage()]);
            return $default;
        }
    }

    /**
     * Check if WhatsApp notifications are enabled
     */
    private function isEnabled(): bool
    {
        $enabled = $this->getSetting('whatsapp.enabled', 'false');
        return $enabled === 'true' || $enabled === '1';
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
     * Send SKT creation notification
     */
    public function sendSKTNotification(string $target, array $data): array
    {
        $template = $this->getSetting('whatsapp.skt_template', 
            "🔔 *Notifikasi SKT*\n\nHalo! Permohonan SKT Anda telah diterima dan sedang diproses.\n\n📋 *Detail Permohonan:*\n• ID: {id}\n• Nama Ormas: {nama_ormas}\n• Jenis: {jenis_permohonan}\n\n✅ Tim kami akan segera memproses permohonan Anda.\n\nTerima kasih atas kepercayaan Anda! 🙏"
        );

        $message = str_replace(
            ['{id}', '{nama_ormas}', '{jenis_permohonan}'],
            [$data['id'], $data['nama_ormas'], $data['jenis_permohonan']],
            $template
        );
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send SKL creation notification
     */
    public function sendSKLNotification(string $target, array $data): array
    {
        $template = $this->getSetting('whatsapp.skl_template',
            "🔔 *Notifikasi SKL*\n\nHalo! Permohonan SKL Anda telah diterima dan sedang diproses.\n\n📋 *Detail Permohonan:*\n• ID: {id}\n• Nama Organisasi: {nama_organisasi}\n\n✅ Tim kami akan segera memproses permohonan Anda.\n\nTerima kasih atas kepercayaan Anda! 🙏"
        );

        $message = str_replace(
            ['{id}', '{nama_organisasi}'],
            [$data['id'], $data['nama_organisasi'] ?? 'N/A'],
            $template
        );
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send Information Request notification
     */
    public function sendInformationRequestNotification(string $target, array $data): array
    {
        $template = $this->getSetting('whatsapp.info_request_template',
            "🔔 *Notifikasi Permohonan Informasi Publik*\n\nHalo {nama_lengkap}!\n\nPermohonan informasi publik Anda telah diterima dan sedang diproses.\n\n📋 *Detail Permohonan:*\n• ID: {id}\n• Nama: {nama_lengkap}\n\n✅ Tim kami akan segera memproses permohonan Anda sesuai dengan ketentuan yang berlaku.\n\nTerima kasih atas kepercayaan Anda! 🙏"
        );

        $message = str_replace(
            ['{id}', '{nama_lengkap}'],
            [$data['id'], $data['nama_lengkap']],
            $template
        );
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send Information Objection notification
     */
    public function sendInformationObjectionNotification(string $target, array $data): array
    {
        $template = $this->getSetting('whatsapp.info_objection_template',
            "🔔 *Notifikasi Keberatan Informasi Publik*\n\nHalo {nama_lengkap}!\n\nKeberatan informasi publik Anda telah diterima dan sedang ditinjau.\n\n📋 *Detail Keberatan:*\n• ID: {id}\n• Nama: {nama_lengkap}\n\n✅ Tim kami akan segera meninjau keberatan Anda sesuai dengan prosedur yang berlaku.\n\nTerima kasih atas kepercayaan Anda! 🙏"
        );

        $message = str_replace(
            ['{id}', '{nama_lengkap}'],
            [$data['id'], $data['nama_lengkap']],
            $template
        );
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send status update notification
     */
    public function sendStatusUpdate(string $target, string $serviceType, array $data): array
    {
        $serviceNames = [
            'skt' => 'SKT',
            'skl' => 'SKL',
            'information_request' => 'Permohonan Informasi Publik',
            'information_objection' => 'Keberatan Informasi Publik'
        ];

        $serviceName = $serviceNames[$serviceType] ?? 'Layanan';

        $template = $this->getSetting('whatsapp.status_update_template',
            "📢 *Update Status {service_name}*\n\n🆔 ID: {id}\n📊 Status: *{status}*\n\n📝 Keterangan: {keterangan}\n\n⏰ Diperbarui: {timestamp}\n\nTerima kasih! 🙏"
        );

        $message = str_replace(
            ['{service_name}', '{id}', '{status}', '{keterangan}', '{timestamp}'],
            [
                $serviceName,
                $data['id'],
                $data['status'],
                $data['keterangan'] ?? '-',
                now()->format('d/m/Y H:i')
            ],
            $template
        );
        
        return $this->sendMessage($target, $message);
    }

    /**
     * Send admin notification for new submissions
     */
    public function sendAdminNotification(string $serviceType, array $data): array
    {
        // Get admin phone from settings
        $adminPhone = $this->getSetting('whatsapp.admin_main');
        
        if (empty($adminPhone)) {
            Log::info('Admin WhatsApp notification skipped - no admin phone configured');
            return ['success' => false, 'error' => 'No admin phone configured'];
        }

        $serviceNames = [
            'skt' => 'SKT',
            'skl' => 'SKL',
            'information_request' => 'Permohonan Informasi Publik',
            'information_objection' => 'Keberatan Informasi Publik'
        ];

        $serviceName = $serviceNames[$serviceType] ?? 'Layanan';

        $template = $this->getSetting('whatsapp.admin_notification_template',
            "🔔 *Notifikasi Admin - {service_name} Baru*\n\nAda pengajuan {service_name} baru yang perlu ditinjau:\n\n🆔 ID: {id}\n👤 Pemohon: {nama_pemohon}\n\n⏰ Waktu: {timestamp}\n\nSilakan cek panel admin untuk detail lengkap."
        );

        $message = str_replace(
            ['{service_name}', '{id}', '{nama_pemohon}', '{timestamp}'],
            [
                $serviceName,
                $data['id'],
                $data['nama_pemohon'] ?? 'N/A',
                now()->format('d/m/Y H:i')
            ],
            $template
        );
        
        return $this->sendMessage($adminPhone, $message);
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
            // Add 62 prefix for numbers starting with 8
            $phone = '62' . $phone;
        } elseif (str_starts_with($phone, '0')) {
            // Replace leading 0 with 62
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            // Add 62 prefix if not present
            $phone = '62' . $phone;
        }

        // Validate length (Indonesian numbers should be 10-15 digits with country code)
        if (strlen($phone) < 10 || strlen($phone) > 15) {
            return null;
        }

        return $phone;
    }

    /**
     * Test connection to Fonnte API
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
            ])->get('https://api.fonnte.com/validate');

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

    /**
     * Get all WhatsApp settings for admin panel
     */
    public function getSettings(): array
    {
        try {
            $settings = DB::table('settings')
                ->where('group', 'whatsapp')
                ->get()
                ->mapWithKeys(function ($setting) {
                    return [$setting->name => $setting->payload];
                });

            return $settings->toArray();
        } catch (Exception $e) {
            Log::error('Failed to get WhatsApp settings', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Update WhatsApp setting
     */
    public function updateSetting(string $key, string $value): bool
    {
        try {
            DB::table('settings')
                ->updateOrInsert(
                    [
                        'group' => 'whatsapp',
                        'name' => str_replace('whatsapp.', '', $key)
                    ],
                    [
                        'payload' => $value,
                        'locked' => 0,
                        'updated_at' => now()
                    ]
                );

            return true;
        } catch (Exception $e) {
            Log::error('Failed to update WhatsApp setting', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}