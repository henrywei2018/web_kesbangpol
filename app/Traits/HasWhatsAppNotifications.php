<?php
namespace App\Traits;

use App\Services\FonteService;
use Illuminate\Support\Facades\Log;

trait HasWhatsAppNotifications
{
    /**
     * Send WhatsApp notification after model creation
     */
    public function sendCreationNotification(): void
    {
        if (!config('services.fonnte.enabled')) {
            return;
        }

        try {
            $fonteService = app(FonteService::class);
            $user = $this->user ?? auth()->user();
            
            if (!$user || !$user->no_telepon) {
                Log::warning('WhatsApp notification skipped - no phone number', [
                    'model' => get_class($this),
                    'id' => $this->id
                ]);
                return;
            }

            // Determine notification type based on model
            $result = match (get_class($this)) {
                'App\Models\SKT' => $fonteService->sendSKTNotification($user->no_telepon, [
                    'id' => $this->id,
                    'nama_ormas' => $this->nama_ormas,
                    'jenis_permohonan' => $this->jenis_permohonan,
                ]),
                'App\Models\SKL' => $fonteService->sendSKLNotification($user->no_telepon, [
                    'id' => $this->id,
                    'nama_organisasi' => $this->nama_organisasi ?? null,
                    'email_organisasi' => $this->email_organisasi ?? null,
                ]),
                'App\Models\PermohonanInformasiPublik' => $fonteService->sendInformationRequestNotification($user->no_telepon, [
                    'id' => $this->id,
                    'nama_lengkap' => $this->nama_lengkap ?? $user->firstname . ' ' . $user->lastname,
                ]),
                'App\Models\KeberatanInformasiPublik' => $fonteService->sendInformationObjectionNotification($user->no_telepon, [
                    'id' => $this->id,
                    'nama_lengkap' => $this->nama_lengkap ?? $user->firstname . ' ' . $user->lastname,
                ]),
                'App\Models\LaporATHG' => $fonteService->sendATHGReportNotification($user->no_telepon, [
                    'id' => $this->id,
                    'nama_lengkap' => $this->nama_pelapor ?? $user->firstname . ' ' . $user->lastname,
                    'bidang' => $this->bidang ?? 'Tidak ditentukan',
                    'tingkat_urgensi' => $this->tingkat_urgensi ?? 'normal',
                ]),
                default => ['success' => false, 'error' => 'Unknown model type: ' . get_class($this)]
            };

            Log::info('WhatsApp notification sent', [
                'model' => get_class($this),
                'id' => $this->id,
                'phone' => $user->no_telepon,
                'success' => $result['success']
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp notification failed', [
                'model' => get_class($this),
                'id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send status update notification
     */
    public function sendStatusUpdateNotification(string $newStatus, string $keterangan = ''): void
    {
        if (!config('services.fonnte.enabled')) {
            return;
        }

        try {
            $fonteService = app(FonteService::class);
            $user = $this->user ?? auth()->user();
            
            if (!$user || !$user->no_telepon) {
                return;
            }

            // Determine service type based on model
            $serviceType = match (get_class($this)) {
                'App\Models\SKT' => 'skt',
                'App\Models\SKL' => 'skl',
                'App\Models\PermohonanInformasiPublik' => 'information_request',
                'App\Models\KeberatanInformasiPublik' => 'information_objection',
                'App\Models\LaporATHG' => 'athg_report',
                default => 'unknown'
            };

            $result = $fonteService->sendStatusUpdate($user->no_telepon, $serviceType, [
                'id' => $this->id,
                'status' => $newStatus,
                'keterangan' => $keterangan,
            ]);

            Log::info('WhatsApp status update sent', [
                'model' => get_class($this),
                'id' => $this->id,
                'status' => $newStatus,
                'success' => $result['success']
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp status update failed', [
                'model' => get_class($this),
                'id' => $this->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}