<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
use App\Models\SKT;
use App\Models\LaporATHG;

class AdminOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    protected function getStats(): array
    {
        $totalUsers = User::count();
        $totalPermohonan = PermohonanInformasiPublik::count();
        $totalSKT = SKT::count();
        $totalATHG = LaporATHG::count();

        // Calculate trends
        $lastMonthUsers = User::where('created_at', '>=', now()->subMonth())->count();
        $userGrowth = $totalUsers > 0 ? (($lastMonthUsers / $totalUsers) * 100) : 0;

        return [
            Stat::make('Total Pengguna', number_format($totalUsers))
                ->description(round($userGrowth, 1) . '% pertumbuhan bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($this->getUserChart()),

            Stat::make('Permohonan Informasi', number_format($totalPermohonan))
                ->description($this->getPermohonanPendingCount() . ' menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('SKT Terdaftar', number_format($totalSKT))
                ->description('Surat Keterangan Terdaftar')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('primary'),

            Stat::make('Laporan ATHG', number_format($totalATHG))
                ->description('Ancaman, Tantangan, Hambatan, Gangguan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }

    private function getUserChart(): array
    {
        // Generate chart data for the last 7 days
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::whereDate('created_at', $date)->count();
            $data[] = $count;
        }
        return $data;
    }

    private function getPermohonanPendingCount(): int
    {
        return PermohonanInformasiPublik::whereHas('statuses', function($query) {
            $query->where('status', 'pending')
                  ->orWhere('status', 'diproses');
        })->count();
    }
}