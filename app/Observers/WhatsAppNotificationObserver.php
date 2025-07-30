<?php

namespace App\Observers;

use App\Models\SKT;
use App\Models\SKL;
use App\Models\PermohonanInformasiPublik;
use App\Services\FonteService;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationObserver
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
        try {
            // Get user phone number
            $user = $skt->user ?? auth()->user();
            if (!$user || !$user->no_telepon) {
                Log::warning('SKT WhatsApp notification skipped - no phone number', ['skt_id' => $skt->id]);
                return;
            }

            // Send WhatsApp notification
            $result = $this->fonteService->sendSKTNotification($user->no_telepon, [
                'id' => $skt->id,
                'nama_ormas' => $skt->nama_ormas,
                'jenis_permohonan' => $skt->jenis_permohonan,
            ]);

            Log::info('SKT WhatsApp notification sent', [
                'skt_id' => $skt->id,
                'phone' => $user->no_telepon,
                'success' => $result['success']
            ]);

        } catch (\Exception $e) {
            Log::error('SKT WhatsApp notification failed', [
                'skt_id' => $skt->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle SKL created event
     */
    public function sklCreated(SKL $skl): void
    {
        try {
            // Get user phone number
            $user = $skl->user ?? auth()->user();
            if (!$user || !$user->no_telepon) {
                Log::warning('SKL WhatsApp notification skipped - no phone number', ['skl_id' => $skl->id]);
                return;
            }

            // Send WhatsApp notification
            $result = $this->fonteService->sendSKLNotification($user->no_telepon, [
                'id' => $skl->id,
                'nama_organisasi' => $skl->nama_organisasi ?? null,
                'email_organisasi' => $skl->email_organisasi ?? null,
            ]);

            Log::info('SKL WhatsApp notification sent', [
                'skl_id' => $skl->id,
                'phone' => $user->no_telepon,
                'success' => $result['success']
            ]);

        } catch (\Exception $e) {
            Log::error('SKL WhatsApp notification failed', [
                'skl_id' => $skl->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle Information Request created event
     */
    public function informationRequestCreated(PermohonanInformasiPublik $request): void
    {
        try {
            // Get user phone number
            $user = $request->user ?? auth()->user();
            if (!$user || !$user->no_telepon) {
                Log::warning('Information Request WhatsApp notification skipped - no phone number', ['request_id' => $request->id]);
                return;
            }

            // Determine notification type based on category
            if ($request->kategori_permohonan === 'keberatan') {
                $result = $this->fonteService->sendInformationObjectionNotification($user->no_telepon, [
                    'id' => $request->id,
                    'nama_lengkap' => $request->nama_lengkap ?? $user->firstname . ' ' . $user->lastname,
                ]);
            } else {
                $result = $this->fonteService->sendInformationRequestNotification($user->no_telepon, [
                    'id' => $request->id,
                    'nama_lengkap' => $request->nama_lengkap ?? $user->firstname . ' ' . $user->lastname,
                ]);
            }

            Log::info('Information Request WhatsApp notification sent', [
                'request_id' => $request->id,
                'phone' => $user->no_telepon,
                'type' => $request->kategori_permohonan,
                'success' => $result['success']
            ]);

        } catch (\Exception $e) {
            Log::error('Information Request WhatsApp notification failed', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

// Separate observers for each model

class SKTObserver
{
    protected FonteService $fonteService;

    public function __construct(FonteService $fonteService)
    {
        $this->fonteService = $fonteService;
    }

    public function created(SKT $skt): void
    {
        // Send WhatsApp notification
        $this->sendWhatsAppNotification($skt);
        
        // Your existing ORMAS master creation logic
        \App\Models\OrmasMaster::createOrUpdateFromSKT($skt, 'belum_selesai');
    }

    private function sendWhatsAppNotification(SKT $skt): void
    {
        if (!config('services.fonnte.enabled')) {
            return;
        }

        try {
            $user = $skt->user ?? auth()->user();
            if (!$user || !$user->no_telepon) {
                return;
            }

            $this->fonteService->sendSKTNotification($user->no_telepon, [
                'id' => $skt->id,
                'nama_ormas' => $skt->nama_ormas,
                'jenis_permohonan' => $skt->jenis_permohonan,
            ]);

        } catch (\Exception $e) {
            Log::error('SKT WhatsApp notification failed', [
                'skt_id' => $skt->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

class SKLObserver
{
    protected FonteService $fonteService;

    public function __construct(FonteService $fonteService)
    {
        $this->fonteService = $fonteService;
    }

    public function created(SKL $skl): void
    {
        // Send WhatsApp notification
        $this->sendWhatsAppNotification($skl);
        
        // Your existing ORMAS master creation logic
        \App\Models\OrmasMaster::createOrUpdateFromSKL($skl, 'belum_selesai');
    }

    private function sendWhatsAppNotification(SKL $skl): void
    {
        if (!config('services.fonnte.enabled')) {
            return;
        }

        try {
            $user = $skl->user ?? auth()->user();
            if (!$user || !$user->no_telepon) {
                return;
            }

            $this->fonteService->sendSKLNotification($user->no_telepon, [
                'id' => $skl->id,
                'nama_organisasi' => $skl->nama_organisasi ?? null,
                'email_organisasi' => $skl->email_organisasi ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('SKL WhatsApp notification failed', [
                'skl_id' => $skl->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

class PermohonanInformasiPublikObserver
{
    protected FonteService $fonteService;

    public function __construct(FonteService $fonteService)
    {
        $this->fonteService = $fonteService;
    }

    public function created(PermohonanInformasiPublik $request): void
    {
        $this->sendWhatsAppNotification($request);
    }

    private function sendWhatsAppNotification(PermohonanInformasiPublik $request): void
    {
        if (!config('services.fonnte.enabled')) {
            return;
        }

        try {
            $user = $request->user ?? auth()->user();
            if (!$user || !$user->no_telepon) {
                return;
            }

            // Determine notification type
            if ($request->kategori_permohonan === 'keberatan') {
                $result = $this->fonteService->sendInformationObjectionNotification($user->no_telepon, [
                    'id' => $request->id,
                    'nama_lengkap' => $request->nama_lengkap ?? $user->firstname . ' ' . $user->lastname,
                ]);
            } else {
                $result = $this->fonteService->sendInformationRequestNotification($user->no_telepon, [
                    'id' => $request->id,
                    'nama_lengkap' => $request->nama_lengkap ?? $user->firstname . ' ' . $user->lastname,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Information Request WhatsApp notification failed', [
                'request_id' => $request->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}