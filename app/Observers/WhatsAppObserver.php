<?php

namespace App\Observers;

use App\Models\SKT;
use App\Models\SKL;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
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
        $this->sendUserNotification($skt, 'skt');
        $this->sendAdminNotification($skt, 'skt');
    }

    /**
     * Handle SKL created event
     */
    public function sklCreated(SKL $skl): void
    {
        $this->sendUserNotification($skl, 'skl');
        $this->sendAdminNotification($skl, 'skl');
    }

    /**
     * Handle PermohonanInformasiPublik created event
     */
    public function permohonanInformasiPublikCreated(PermohonanInformasiPublik $request): void
    {
        $serviceType = $request->kategori_permohonan === 'keberatan' 
            ? 'information_objection' 
            : 'information_request';
            
        $this->sendUserNotification($request, $serviceType);
        $this->sendAdminNotification($request, $serviceType);
    }

    /**
     * Handle KeberatanInformasiPublik created event
     */
    public function keberatanInformasiPublikCreated(KeberatanInformasiPublik $keberatan): void
    {
        $this->sendUserNotification($keberatan, 'information_objection');
        $this->sendAdminNotification($keberatan, 'information_objection');
    }

    /**
     * Send WhatsApp notification to user
     */
    private function sendUserNotification($model, string $serviceType): void
    {
        // The FonteService will check if enabled internally
        // No need to check here since it uses database settings

        try {
            // Get user phone number
            $user = $this->getModelUser($model);
            
            if (!$user || !$user->no_telepon) {
                Log::warning('WhatsApp notification skipped - no phone number', [
                    'model' => get_class($model),
                    'id' => $model->id,
                    'user_id' => $user?->id
                ]);
                return;
            }

            // Send appropriate notification based on service type
            $result = match ($serviceType) {
                'skt' => $this->fonteService->sendSKTNotification($user->no_telepon, [
                    'id' => $model->id,
                    'nama_ormas' => $model->nama_ormas,
                    'jenis_permohonan' => $model->jenis_permohonan,
                ]),
                'skl' => $this->fonteService->sendSKLNotification($user->no_telepon, [
                    'id' => $model->id,
                    'nama_organisasi' => $model->nama_organisasi ?? null,
                    'email_organisasi' => $model->email_organisasi ?? null,
                ]),
                'information_request' => $this->fonteService->sendInformationRequestNotification($user->no_telepon, [
                    'id' => $model->id,
                    'nama_lengkap' => $this->getFullName($model, $user),
                ]),
                'information_objection' => $this->fonteService->sendInformationObjectionNotification($user->no_telepon, [
                    'id' => $model->id,
                    'nama_lengkap' => $this->getFullName($model, $user),
                ]),
                default => ['success' => false, 'error' => 'Unknown service type']
            };

            Log::info('WhatsApp user notification sent', [
                'model' => get_class($model),
                'id' => $model->id,
                'service_type' => $serviceType,
                'phone' => $user->no_telepon,
                'success' => $result['success'] ?? false,
                'message_id' => $result['message_id'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp user notification failed', [
                'model' => get_class($model),
                'id' => $model->id,
                'service_type' => $serviceType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send WhatsApp notification to admin
     */
    private function sendAdminNotification($model, string $serviceType): void
    {
        // The FonteService will handle admin phone lookup and enabled check

        try {
            $user = $this->getModelUser($model);
            
            // Prepare admin notification data
            $adminData = [
                'id' => $model->id,
                'nama_pemohon' => $user ? "{$user->firstname} {$user->lastname}" : 'N/A',
            ];

            // Add specific data based on model type
            if ($model instanceof SKT) {
                $adminData['nama_ormas'] = $model->nama_ormas;
            } elseif ($model instanceof SKL) {
                $adminData['nama_organisasi'] = $model->nama_organisasi ?? 'N/A';
            }

            $result = $this->fonteService->sendAdminNotification($serviceType, $adminData);

            Log::info('WhatsApp admin notification sent', [
                'model' => get_class($model),
                'id' => $model->id,
                'service_type' => $serviceType,
                'success' => $result['success'] ?? false
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp admin notification failed', [
                'model' => get_class($model),
                'id' => $model->id,
                'service_type' => $serviceType,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get user from model
     */
    private function getModelUser($model)
    {
        if (method_exists($model, 'user')) {
            return $model->user ?? auth()->user();
        }

        // Fallback to authenticated user
        return auth()->user();
    }

    /**
     * Get full name from model or user
     */
    private function getFullName($model, $user): string
    {
        // Try to get from model first
        if (isset($model->nama_lengkap) && !empty($model->nama_lengkap)) {
            return $model->nama_lengkap;
        }

        // Fallback to user name
        if ($user) {
            return "{$user->firstname} {$user->lastname}";
        }

        return 'N/A';
    }

    /**
     * Handle status updates for all models
     */
    public function handleStatusUpdate($model, string $newStatus, string $keterangan = ''): void
    {
        // FonteService will check if enabled internally

        try {
            $user = $this->getModelUser($model);
            
            if (!$user || !$user->no_telepon) {
                return;
            }

            // Determine service type
            $serviceType = match (get_class($model)) {
                'App\Models\SKT' => 'skt',
                'App\Models\SKL' => 'skl',
                'App\Models\PermohonanInformasiPublik' => $model->kategori_permohonan === 'keberatan' 
                    ? 'information_objection' 
                    : 'information_request',
                'App\Models\KeberatanInformasiPublik' => 'information_objection',
                default => 'unknown'
            };

            $result = $this->fonteService->sendStatusUpdate($user->no_telepon, $serviceType, [
                'id' => $model->id,
                'status' => $newStatus,
                'keterangan' => $keterangan,
            ]);

            Log::info('WhatsApp status update sent', [
                'model' => get_class($model),
                'id' => $model->id,
                'status' => $newStatus,
                'service_type' => $serviceType,
                'success' => $result['success'] ?? false
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp status update failed', [
                'model' => get_class($model),
                'id' => $model->id,
                'status' => $newStatus,
                'error' => $e->getMessage()
            ]);
        }
    }
}