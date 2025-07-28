<?php
// app/Filament/Public/Widgets/StatsOverview.php

namespace App\Filament\Public\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        
        $totalPermohonan = PermohonanInformasiPublik::where('user_id', $user->id)->count();
        $totalKeberatan = KeberatanInformasiPublik::where('user_id', $user->id)->count();
        
        $permohonanDisetujui = PermohonanInformasiPublik::where('user_id', $user->id)
            ->where('latest_status', 'disetujui')
            ->count();
            
        $permohonanDiproses = PermohonanInformasiPublik::where('user_id', $user->id)
            ->whereIn('latest_status', ['pengajuan', 'proses', 'review'])
            ->count();

        return [
            Stat::make('Total Permohonan', $totalPermohonan)
                ->description($permohonanDisetujui . ' disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
                
            Stat::make('Dalam Proses', $permohonanDiproses)
                ->description('Menunggu review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Keberatan Aktif', $totalKeberatan)
                ->description('Total keberatan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
                
            Stat::make('Tingkat Persetujuan', $totalPermohonan > 0 ? round(($permohonanDisetujui / $totalPermohonan) * 100) . '%' : '0%')
                ->description('Dari total permohonan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
}