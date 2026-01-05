<?php
// app/Services/StatusService.php

namespace App\Services;

use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;

class StatusService
{
    /**
     * Get status configuration for different models
     */
    public static function getStatusConfig(): array
    {
        return [
            'Pending' => [
                'color' => 'info',
                'badge_class' => 'badge-theme-info',
                'bg_class' => 'bg-theme-info-light',
                'text_class' => 'text-theme-info',
                'dot_class' => 'bg-theme-info',
                'description' => 'Menunggu verifikasi dan pemrosesan'
            ],
            'Diproses' => [
                'color' => 'warning',
                'badge_class' => 'badge-theme-warning',
                'bg_class' => 'bg-theme-warning-light',
                'text_class' => 'text-theme-warning',
                'dot_class' => 'bg-theme-warning',
                'description' => 'Sedang dalam proses review'
            ],
            'Selesai' => [
                'color' => 'success',
                'badge_class' => 'badge-theme-success',
                'bg_class' => 'bg-theme-success-light',
                'text_class' => 'text-theme-success',
                'dot_class' => 'bg-theme-success',
                'description' => 'Permohonan telah selesai diproses'
            ],
            'Ditolak' => [
                'color' => 'danger',
                'badge_class' => 'badge-theme-danger',
                'bg_class' => 'bg-theme-danger-light',
                'text_class' => 'text-theme-danger',
                'dot_class' => 'bg-theme-danger',
                'description' => 'Permohonan ditolak'
            ]
        ];
    }

    /**
     * Get status information for a given status
     */
    public static function getStatusInfo(string $status): array
    {
        $config = self::getStatusConfig();
        return $config[$status] ?? $config['Pending'];
    }

    /**
     * Get user statistics for dashboard
     */
    public static function getUserStatistics($userId): array
    {
        // Get permohonan with latest status
        $permohonanData = PermohonanInformasiPublik::where('id_pemohon', $userId)
            ->with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->get();

        // Get keberatan with latest status
        $keberatanData = KeberatanInformasiPublik::where('id_pemohon', $userId)
            ->with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->get();

        // Calculate permohonan statistics
        $permohonanStats = [
            'total' => $permohonanData->count(),
            'pending' => 0,
            'diproses' => 0,
            'selesai' => 0,
            'ditolak' => 0,
        ];

        foreach ($permohonanData as $item) {
            $latestStatus = $item->statuses->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            
            switch ($status) {
                case 'Pending':
                    $permohonanStats['pending']++;
                    break;
                case 'Diproses':
                    $permohonanStats['diproses']++;
                    break;
                case 'Selesai':
                    $permohonanStats['selesai']++;
                    break;
                case 'Ditolak':
                    $permohonanStats['ditolak']++;
                    break;
            }
        }

        // Calculate keberatan statistics
        $keberatanStats = [
            'total' => $keberatanData->count(),
            'pending' => 0,
            'diproses' => 0,
            'selesai' => 0,
            'ditolak' => 0,
        ];

        foreach ($keberatanData as $item) {
            $latestStatus = $item->statuses->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            
            switch ($status) {
                case 'Pending':
                    $keberatanStats['pending']++;
                    break;
                case 'Diproses':
                    $keberatanStats['diproses']++;
                    break;
                case 'Selesai':
                    $keberatanStats['selesai']++;
                    break;
                case 'Ditolak':
                    $keberatanStats['ditolak']++;
                    break;
            }
        }

        return [
            'permohonan' => $permohonanStats,
            'keberatan' => $keberatanStats,
            'combined' => [
                'total_submissions' => $permohonanStats['total'] + $keberatanStats['total'],
                'total_pending' => $permohonanStats['pending'] + $keberatanStats['pending'],
                'total_diproses' => $permohonanStats['diproses'] + $keberatanStats['diproses'],
                'total_selesai' => $permohonanStats['selesai'] + $keberatanStats['selesai'],
                'total_ditolak' => $permohonanStats['ditolak'] + $keberatanStats['ditolak'],
                'completion_rate' => $permohonanStats['total'] > 0 
                    ? round(($permohonanStats['selesai'] / $permohonanStats['total']) * 100)
                    : 0
            ]
        ];
    }

    /**
     * Get recent activities for a user
     */
    public static function getRecentActivities($userId, $limit = 5): \Illuminate\Support\Collection
    {
        $activities = collect();

        // Get recent permohonan
        $permohonanData = PermohonanInformasiPublik::where('id_pemohon', $userId)
            ->with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->latest('updated_at')
            ->take($limit)
            ->get();

        foreach ($permohonanData as $item) {
            $latestStatus = $item->statuses->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            $statusDescription = $latestStatus ? $latestStatus->deskripsi_status : 'Belum ada deskripsi';

            $activities->push([
                'type' => 'permohonan',
                'title' => 'Permohonan Informasi #' . ($item->nomor_register ?? $item->id),
                'description' => $statusDescription,
                'status' => $status,
                'status_info' => self::getStatusInfo($status),
                'date' => $item->updated_at,
                'model' => $item,
                'url' => route('filament.public.resources.permohonan-informasi-publiks.view', $item->id)
            ]);
        }

        // Get recent keberatan
        $keberatanData = KeberatanInformasiPublik::where('id_pemohon', $userId)
            ->with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->latest('updated_at')
            ->take($limit)
            ->get();

        foreach ($keberatanData as $item) {
            $latestStatus = $item->statuses->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            $statusDescription = $latestStatus ? $latestStatus->deskripsi_status : 'Belum ada deskripsi';

            $activities->push([
                'type' => 'keberatan',
                'title' => 'Keberatan Informasi #' . ($item->nomor_registrasi ?? $item->id),
                'description' => $statusDescription,
                'status' => $status,
                'status_info' => self::getStatusInfo($status),
                'date' => $item->updated_at,
                'model' => $item,
                'url' => route('filament.public.resources.keberatan-informasi-publiks.view', $item->id)
            ]);
        }

        return $activities->sortByDesc('date')->take($limit);
    }

    /**
     * Check if a status allows document download
     */
    public static function canDownloadDocument(string $status): bool
    {
        return $status === 'Selesai';
    }

    /**
     * Get status timeline for a model
     */
    public static function getStatusTimeline($model): \Illuminate\Support\Collection
    {
        return $model->statuses()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($status) {
                return [
                    'status' => $status->status,
                    'description' => $status->deskripsi_status,
                    'date' => $status->created_at,
                    'status_info' => self::getStatusInfo($status->status)
                ];
            });
    }
}