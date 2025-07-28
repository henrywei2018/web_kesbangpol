<?php

namespace App\Filament\Public\Widgets;

use Filament\Widgets\Widget;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;

class RecentActivity extends Widget
{
    protected static string $view = 'filament.public.widgets.recent-activity';
    
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = auth()->user();
        
        $recentActivities = collect();
        
        // Get recent permohonan
        $permohonanInformasi = PermohonanInformasiPublik::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();
            
        foreach ($permohonanInformasi as $permohonan) {
            $recentActivities->push([
                'type' => 'permohonan',
                'title' => 'Permohonan Informasi #' . $permohonan->id,
                'description' => Str::limit($permohonan->rincian_informasi, 50),
                'status' => $permohonan->latest_status ?? 'pengajuan',
                'date' => $permohonan->updated_at,
                'url' => route('filament.public.resources.permohonan-informasi-publiks.view', $permohonan->id)
            ]);
        }
        
        // Get recent keberatan
        $keberatanInformasi = KeberatanInformasiPublik::where('user_id', $user->id)
            ->latest()
            ->take(2)
            ->get();
            
        foreach ($keberatanInformasi as $keberatan) {
            $recentActivities->push([
                'type' => 'keberatan',
                'title' => 'Keberatan Informasi #' . $keberatan->id,
                'description' => Str::limit($keberatan->alasan_keberatan, 50),
                'status' => $keberatan->latest_status ?? 'pengajuan',
                'date' => $keberatan->updated_at,
                'url' => route('filament.public.resources.keberatan-informasi-publiks.view', $keberatan->id)
            ]);
        }
        
        return [
            'activities' => $recentActivities->sortByDesc('date')->take(5)
        ];
    }
}