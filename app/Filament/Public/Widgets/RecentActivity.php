<?php

namespace App\Filament\Public\Widgets;

use Filament\Widgets\Widget;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
use Illuminate\Support\Str;

class RecentActivity extends Widget
{
    protected static string $view = 'filament.public.widgets.recent-activity';
    
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = auth()->user();
        
        $recentActivities = collect();
        
        // Get recent permohonan with their latest status
        $permohonanInformasi = PermohonanInformasiPublik::where('id_pemohon', $user->id)
            ->with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->latest()
            ->take(3)
            ->get();
            
        foreach ($permohonanInformasi as $permohonan) {
            $latestStatus = $permohonan->statuses->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            $statusDescription = $latestStatus ? $latestStatus->deskripsi_status : 'Belum ada deskripsi';
            
            $recentActivities->push([
                'type' => 'permohonan',
                'title' => 'Permohonan Informasi #' . ($permohonan->nomor_register ?? $permohonan->id),
                'description' => Str::limit($statusDescription, 50),
                'status' => $status,
                'date' => $permohonan->updated_at,
                'url' => route('filament.public.resources.permohonan-informasi-publiks.view', $permohonan->id)
            ]);
        }
        
        // Get recent keberatan with their latest status
        $keberatanInformasi = KeberatanInformasiPublik::where('id_pemohon', $user->id)
            ->with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->latest()
            ->take(2)
            ->get();
            
        foreach ($keberatanInformasi as $keberatan) {
            $latestStatus = $keberatan->statuses->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            $statusDescription = $latestStatus ? $latestStatus->deskripsi_status : 'Belum ada deskripsi';
            
            $recentActivities->push([
                'type' => 'keberatan',
                'title' => 'Keberatan Informasi #' . ($keberatan->nomor_registrasi ?? $keberatan->id),
                'description' => Str::limit($statusDescription, 50),
                'status' => $status,
                'date' => $keberatan->updated_at,
                'url' => route('filament.public.resources.keberatan-informasi-publiks.view', $keberatan->id)
            ]);
        }
        
        return [
            'activities' => $recentActivities->sortByDesc('date')->take(5)
        ];
    }
}