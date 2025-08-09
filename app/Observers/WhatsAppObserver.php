<?php

namespace App\Observers;

use App\Models\SKT;
use App\Models\SKL;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
use App\Models\LaporATHG; // ✅ ADD this import
use App\Services\FonteService;
use Illuminate\Support\Facades\Log;

class WhatsAppObserver
{
    protected FonteService $fonteService;

    public function __construct(FonteService $fonteService)
    {
        $this->fonteService = $fonteService;
    }

    /**
     * Handle SKT created event
     */
    public function sktCreated(SKT $skt): void
    {
        Log::info('WhatsApp Observer: SKT created event triggered', [
            'skt_id' => $skt->id,
            'nama_ormas' => $skt->nama_ormas
        ]);

        // Send notifications to user and admin
        $this->sendUserNotification($skt, 'skt');
        $this->sendAdminNotification($skt, 'skt');
    }

    /**
     * Handle SKL created event
     */
    public function sklCreated(SKL $skl): void
    {
        Log::info('WhatsApp Observer: SKL created event triggered', [
            'skl_id' => $skl->id,
            'nama_organisasi' => $skl->nama_organisasi ?? 'N/A'
        ]);

        // Send notifications to user and admin
        $this->sendUserNotification($skl, 'skl');
        $this->sendAdminNotification($skl, 'skl');
    }

    /**
     * Handle PermohonanInformasiPublik created event
     */
    public function permohonanInformasiPublikCreated(PermohonanInformasiPublik $request): void
    {
        Log::info('WhatsApp Observer: PermohonanInformasiPublik created event triggered', [
            'request_id' => $request->id,
            'user_id' => $request->user_id
        ]);

        // Send notifications to user and admin
        $this->sendUserNotification($request, 'information_request');
        $this->sendAdminNotification($request, 'information_request');
    }

    /**
     * Handle KeberatanInformasiPublik created event
     */
    public function keberatanInformasiPublikCreated(KeberatanInformasiPublik $keberatan): void
    {
        Log::info('WhatsApp Observer: KeberatanInformasiPublik created event triggered', [
            'keberatan_id' => $keberatan->id,
            'user_id' => $keberatan->user_id
        ]);

        // Send notifications to user and admin
        $this->sendUserNotification($keberatan, 'information_objection');
        $this->sendAdminNotification($keberatan, 'information_objection');
    }

    /**
     * Handle LaporATHG created event
     */
    public function laporATHGCreated(LaporATHG $laporATHG): void
    {
        Log::info('WhatsApp Observer: LaporATHG created event triggered', [
            'lapor_athg_id' => $laporATHG->id,
            'lapathg_id' => $laporATHG->lapathg_id,
            'bidang' => $laporATHG->bidang,
            'tingkat_urgensi' => $laporATHG->tingkat_urgensi
        ]);

        // Send notifications to user and admin
        $this->sendUserNotification($laporATHG, 'athg_report');
        $this->sendAdminNotification($laporATHG, 'athg_report');
    }

    /**
     * Send WhatsApp notification to user who created the record
     */
    private function sendUserNotification($model, string $serviceType): void
    {
        Log::info('WhatsApp Observer: Starting user notification', [
            'model' => get_class($model),
            'id' => $model->id,
            'service_type' => $serviceType
        ]);

        try {
            // Get the user who created the record
            $user = $this->getModelUser($model);
            
            if (!$user) {
                Log::warning('WhatsApp user notification skipped - no user found', [
                    'model' => get_class($model),
                    'id' => $model->id
                ]);
                return;
            }

            if (!$user->no_telepon) {
                Log::warning('WhatsApp user notification skipped - no phone number', [
                    'model' => get_class($model),
                    'id' => $model->id,
                    'user_id' => $user->id,
                    'user_name' => $user->firstname . ' ' . $user->lastname
                ]);
                return;
            }

            // Format phone number
            $phoneNumber = $this->formatPhoneNumber($user->no_telepon);
            if (!$phoneNumber) {
                Log::warning('WhatsApp user notification skipped - invalid phone format', [
                    'model' => get_class($model),
                    'id' => $model->id,
                    'original_phone' => $user->no_telepon
                ]);
                return;
            }

            // Send appropriate notification based on service type
            $result = $this->sendUserNotificationByType($model, $user, $serviceType, $phoneNumber);

            Log::info('WhatsApp user notification completed', [
                'model' => get_class($model),
                'id' => $model->id,
                'service_type' => $serviceType,
                'phone' => $phoneNumber,
                'success' => $result['success'] ?? false,
                'message_id' => $result['message_id'] ?? null,
                'error' => $result['error'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp user notification failed with exception', [
                'model' => get_class($model),
                'id' => $model->id,
                'service_type' => $serviceType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send appropriate WhatsApp notification based on service type
     */
    private function sendUserNotificationByType($model, $user, string $serviceType, string $phoneNumber): array
    {
        return match ($serviceType) {
            'skt' => $this->fonteService->sendSKTNotification($phoneNumber, [
                'id' => $model->id,
                'nama_ormas' => $model->nama_ormas,
                'jenis_permohonan' => $model->jenis_permohonan,
                'nama_pemohon' => $user->firstname . ' ' . $user->lastname,
                'tanggal_pengajuan' => $model->created_at->format('d/m/Y H:i'),
            ]),
            
            'skl' => $this->fonteService->sendSKLNotification($phoneNumber, [
                'id' => $model->id,
                'nama_organisasi' => $model->nama_organisasi ?? $model->nama_ormas ?? 'N/A',
                'email_organisasi' => $model->email_organisasi ?? $model->email ?? 'N/A',
                'nama_pemohon' => $user->firstname . ' ' . $user->lastname,
                'tanggal_pengajuan' => $model->created_at->format('d/m/Y H:i'),
            ]),
            
            'information_request' => $this->fonteService->sendInformationRequestNotification($phoneNumber, [
                'id' => $model->id,
                'nama_lengkap' => $this->getFullName($model, $user),
                'rincian_informasi' => $model->rincian_informasi ?? 'N/A',
                'tanggal_pengajuan' => $model->created_at->format('d/m/Y H:i'),
            ]),
            
            'information_objection' => $this->fonteService->sendInformationObjectionNotification($phoneNumber, [
                'id' => $model->id,
                'nama_lengkap' => $this->getFullName($model, $user),
                'alasan_keberatan' => $model->alasan_keberatan ?? $model->tujuan_keberatan ?? 'N/A',
                'tanggal_pengajuan' => $model->created_at->format('d/m/Y H:i'),
            ]),
            
            'athg_report' => $this->fonteService->sendATHGReportNotification($phoneNumber, [
                'id' => $model->id,
                'lapathg_id' => $model->lapathg_id,
                'nama_pelapor' => $this->getFullName($model, $user),
                'bidang' => $model->bidang ?? 'N/A',
                'jenis_athg' => $model->jenis_athg ?? 'N/A',
                'perihal' => $model->perihal ?? 'N/A',
                'tingkat_urgensi' => $model->tingkat_urgensi ?? 'N/A',
                'tanggal_pengajuan' => $model->created_at->format('d/m/Y H:i'),
            ]),
            
            default => [
                'success' => false, 
                'error' => 'Unknown service type: ' . $serviceType
            ]
        };
    }

    /**
     * Send WhatsApp notification to admin
     */
    private function sendAdminNotification($model, string $serviceType): void
    {
        Log::info('WhatsApp Observer: Starting admin notification', [
            'model' => get_class($model),
            'id' => $model->id,
            'service_type' => $serviceType
        ]);

        try {
            $user = $this->getModelUser($model);
            
            // Prepare admin notification data
            $adminData = [
                'id' => $model->id,
                'nama_pemohon' => $user ? $user->firstname . ' ' . $user->lastname : 'N/A',
                'email_pemohon' => $user ? $user->email : 'N/A',
                'tanggal_pengajuan' => $model->created_at->format('d/m/Y H:i'),
                'service_type_name' => $this->getServiceTypeName($serviceType),
            ];

            // Add specific data based on model type
            $adminData = array_merge($adminData, $this->getModelSpecificAdminData($model, $serviceType));

            // Send admin notification via FonteService
            $result = $this->fonteService->sendAdminNotification($serviceType, $adminData);

            Log::info('WhatsApp admin notification completed', [
                'model' => get_class($model),
                'id' => $model->id,
                'service_type' => $serviceType,
                'success' => $result['success'] ?? false,
                'message_id' => $result['message_id'] ?? null,
                'error' => $result['error'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp admin notification failed with exception', [
                'model' => get_class($model),
                'id' => $model->id,
                'service_type' => $serviceType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Get model-specific data for admin notifications
     */
    private function getModelSpecificAdminData($model, string $serviceType): array
    {
        return match ($serviceType) {
            'skt' => [
                'nama_ormas' => $model->nama_ormas,
                'jenis_permohonan' => $model->jenis_permohonan,
                'tempat_pendirian' => $model->tempat_pendirian ?? 'N/A',
                'bidang_kegiatan' => $model->bidang_kegiatan ?? 'N/A',
            ],
            
            'skl' => [
                'nama_organisasi' => $model->nama_organisasi ?? $model->nama_ormas ?? 'N/A',
                'email_organisasi' => $model->email_organisasi ?? $model->email ?? 'N/A',
                'alamat_organisasi' => $model->alamat_organisasi ?? $model->alamat_sekretariat ?? 'N/A',
            ],
            
            'information_request' => [
                'rincian_informasi' => $model->rincian_informasi ?? 'N/A',
                'tujuan_penggunaan' => $model->tujuan_penggunaan_informasi ?? 'N/A',
                'cara_memperoleh' => $model->cara_memperoleh_informasi ?? 'N/A',
            ],
            
            'information_objection' => [
                'permohonan_terkait' => $model->permohonan_id ?? 'N/A',
                'alasan_keberatan' => $model->alasan_keberatan ?? $model->tujuan_keberatan ?? 'N/A',
                'rincian_informasi' => $model->rincian_informasi ?? 'N/A',
            ],
            
            'athg_report' => [
                'lapathg_id' => $model->lapathg_id,
                'bidang' => $model->bidang ?? 'N/A',
                'jenis_athg' => $model->jenis_athg ?? 'N/A',
                'perihal' => $model->perihal ?? 'N/A',
                'tingkat_urgensi' => $model->tingkat_urgensi ?? 'N/A',
                'lokasi' => $model->lokasi ?? 'N/A',
                'kontak_pelapor' => $model->kontak_pelapor ?? 'N/A',
            ],
            
            default => []
        };
    }

    /**
     * Get service type display name
     */
    private function getServiceTypeName(string $serviceType): string
    {
        return match ($serviceType) {
            'skt' => 'SKT (Surat Keterangan Terdaftar)',
            'skl' => 'SKL (Surat Keterangan Lunas)',
            'information_request' => 'Permohonan Informasi Publik',
            'information_objection' => 'Keberatan Informasi Publik',
            'athg_report' => 'Laporan ATHG',
            default => 'Layanan'
        };
    }

    /**
     * Handle status updates for all models
     */
    public function handleStatusUpdate($model, string $newStatus, string $keterangan = ''): void
    {
        Log::info('WhatsApp Observer: Status update triggered', [
            'model' => get_class($model),
            'id' => $model->id,
            'new_status' => $newStatus,
            'keterangan' => $keterangan
        ]);

        try {
            $user = $this->getModelUser($model);
            
            if (!$user || !$user->no_telepon) {
                Log::warning('WhatsApp status update skipped - no phone number', [
                    'model' => get_class($model),
                    'id' => $model->id,
                    'status' => $newStatus,
                    'user_found' => $user !== null,
                    'phone_exists' => $user ? ($user->no_telepon !== null) : false
                ]);
                return;
            }

            // Format phone number
            $phoneNumber = $this->formatPhoneNumber($user->no_telepon);
            if (!$phoneNumber) {
                Log::warning('WhatsApp status update skipped - invalid phone format', [
                    'model' => get_class($model),
                    'id' => $model->id,
                    'original_phone' => $user->no_telepon
                ]);
                return;
            }

            // Determine service type
            $serviceType = $this->determineServiceType($model);

            // Send status update notification
            $result = $this->fonteService->sendStatusUpdate($phoneNumber, $serviceType, [
                'id' => $model->id,
                'status' => $newStatus,
                'status_display' => $this->getStatusDisplayName($newStatus),
                'keterangan' => $keterangan,
                'service_name' => $this->getServiceTypeName($serviceType),
                'timestamp' => now()->format('d/m/Y H:i'),
            ]);

            Log::info('WhatsApp status update completed', [
                'model' => get_class($model),
                'id' => $model->id,
                'status' => $newStatus,
                'service_type' => $serviceType,
                'phone' => $phoneNumber,
                'success' => $result['success'] ?? false,
                'message_id' => $result['message_id'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp status update failed with exception', [
                'model' => get_class($model),
                'id' => $model->id,
                'status' => $newStatus,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Determine service type from model class
     */
    private function determineServiceType($model): string
    {
        return match (get_class($model)) {
            'App\Models\SKT' => 'skt',
            'App\Models\SKL' => 'skl',
            'App\Models\PermohonanInformasiPublik' => 'information_request',
            'App\Models\KeberatanInformasiPublik' => 'information_objection',
            'App\Models\LaporATHG' => 'athg_report',
            default => 'unknown'
        };
    }

    /**
     * Get display name for status
     */
    private function getStatusDisplayName(string $status): string
    {
        return match ($status) {
            'pengajuan' => 'Pengajuan Baru',
            'diproses' => 'Sedang Diproses',
            'perbaikan' => 'Perlu Perbaikan',
            'terbit' => 'Telah Diterbitkan',
            'ditolak' => 'Ditolak',
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'completed' => 'Selesai',
            default => ucfirst($status)
        };
    }

    /**
     * Get user from model with various relationship patterns
     */
    private function getModelUser($model)
    {
        // Try different user relationship patterns
        if (isset($model->user) && $model->user) {
            return $model->user;
        }

        if (method_exists($model, 'user') && $model->user()) {
            return $model->user;
        }

        // For models with id_pemohon field (SKT, SKL)
        if (isset($model->id_pemohon)) {
            return \App\Models\User::find($model->id_pemohon);
        }

        // For models with user_id field (PermohonanInfo, KeberatanInfo, LaporATHG)
        if (isset($model->user_id)) {
            return \App\Models\User::find($model->user_id);
        }

        // Fallback to authenticated user
        return auth()->user();
    }

    /**
     * Get full name from model or user
     */
    private function getFullName($model, $user): string
    {
        // Try to get from model first (some models have nama_lengkap or nama_pelapor field)
        if (isset($model->nama_lengkap) && !empty($model->nama_lengkap)) {
            return $model->nama_lengkap;
        }

        if (isset($model->nama_pelapor) && !empty($model->nama_pelapor)) {
            return $model->nama_pelapor;
        }

        // Fallback to user name
        if ($user) {
            return trim($user->firstname . ' ' . $user->lastname);
        }

        return 'N/A';
    }

    /**
     * Format phone number to international format for WhatsApp
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
}