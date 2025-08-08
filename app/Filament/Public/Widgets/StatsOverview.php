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
        
        // Get total counts
        $totalPermohonan = PermohonanInformasiPublik::where('id_pemohon', $user->id)->count();
        $totalKeberatan = KeberatanInformasiPublik::where('id_pemohon', $user->id)->count();
        
        // Get permohonan with their latest status using the morph relationship
        $permohonanData = PermohonanInformasiPublik::where('id_pemohon', $user->id)
            ->with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->get();
            
        // Calculate status counts for permohonan
        $permohonanSelesai = $permohonanData->filter(function($item) {
            $latestStatus = $item->statuses->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            return $status === 'Selesai';
        })->count();
        
        $permohonanDiproses = $permohonanData->filter(function($item) {
            $latestStatus = $item->statuses->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            return in_array($status, ['Pending', 'Diproses']);
        })->count();
        
        // Get keberatan with their latest status
        $keberatanData = KeberatanInformasiPublik::where('id_pemohon', $user->id)
            ->with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->get();
            
        $keberatanAktif = $keberatanData->filter(function($item) {
            $latestStatus = $item->statuses->first();
            $status = $latestStatus ? $latestStatus->status : 'Pending';
            return in_array($status, ['Pending', 'Diproses']);
        })->count();

        return [
            Stat::make('Total Permohonan', $totalPermohonan)
                ->description($permohonanSelesai . ' selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary')
                ->chart($this->getPermohonanChart()),
                
            Stat::make('Dalam Proses', $permohonanDiproses)
                ->description('Menunggu review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Keberatan Aktif', $keberatanAktif)
                ->description('Total keberatan: ' . $totalKeberatan)
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
                
            Stat::make('Tingkat Penyelesaian', $totalPermohonan > 0 ? round(($permohonanSelesai / $totalPermohonan) * 100) . '%' : '0%')
                ->description('Dari total permohonan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }
    
    private function getPermohonanChart(): array
    {
        // Get last 7 days of permohonan data
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = PermohonanInformasiPublik::where('id_pemohon', auth()->id())
                ->whereDate('created_at', $date)
                ->count();
            $data[] = $count;
        }
        
        return $data;
    }
}