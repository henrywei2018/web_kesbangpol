<?php

namespace App\Filament\Resources\OrmasMasterResource\Widgets;

use App\Models\OrmasMaster;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OrmasDetailedStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Get basic counts
        $totalOrmas = OrmasMaster::count();
        $selesaiCount = OrmasMaster::where('status_administrasi', 'selesai')->count();
        $belumSelesaiCount = OrmasMaster::where('status_administrasi', 'belum_selesai')->count();
        $fromSKT = OrmasMaster::where('sumber_registrasi', 'skt')->count();
        $fromSKL = OrmasMaster::where('sumber_registrasi', 'skl')->count();

        // Calculate percentages
        $completionRate = $totalOrmas > 0 ? round(($selesaiCount / $totalOrmas) * 100, 1) : 0;

        // Get this month's registrations
        $thisMonthCount = OrmasMaster::whereMonth('first_registered_at', now()->month)
            ->whereYear('first_registered_at', now()->year)
            ->count();

        // Get this month's completions
        $thisMonthCompletions = OrmasMaster::where('status_administrasi', 'selesai')
            ->whereMonth('tanggal_selesai_administrasi', now()->month)
            ->whereYear('tanggal_selesai_administrasi', now()->year)
            ->count();

        return [
            Stat::make('Total ORMAS', $totalOrmas)
                ->description('Total organisasi terdaftar')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Selesai Administrasi', $selesaiCount)
                ->description("{$completionRate}% dari total ORMAS")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Belum Selesai', $belumSelesaiCount)
                ->description('Masih dalam proses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Dari SKT', $fromSKT)
                ->description('Registrasi melalui SKT')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Dari SKL', $fromSKL)
                ->description('Registrasi melalui SKL')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('gray'),

            Stat::make('Registrasi Bulan Ini', $thisMonthCount)
                ->description('ORMAS baru bulan ' . now()->format('F'))
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('success'),

            Stat::make('Selesai Bulan Ini', $thisMonthCompletions)
                ->description('Completed this month')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary'),
        ];
    }
}