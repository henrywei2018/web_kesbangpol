<?php

namespace App\Filament\Public\Widgets;

use App\Models\OrmasMaster;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PublicOrmasDetailedStats extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        // Get basic counts
        $totalOrmas = OrmasMaster::count();
        $selesaiCount = OrmasMaster::where('status_administrasi', 'selesai')->count();
        $fromSKT = OrmasMaster::where('sumber_registrasi', 'skt')->count();
        $fromSKL = OrmasMaster::where('sumber_registrasi', 'skl')->count();

        // Calculate percentages
        $completionRate = $totalOrmas > 0 ? round(($selesaiCount / $totalOrmas) * 100, 1) : 0;

        // Get this year's registrations
        $thisYearCount = OrmasMaster::whereYear('first_registered_at', now()->year)->count();

        return [
            Stat::make('Total ORMAS Terdaftar', $totalOrmas)
                ->description('Organisasi Kemasyarakatan di Kalimantan Utara')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Registrasi Lengkap', $selesaiCount)
                ->description("Tingkat kelengkapan: {$completionRate}%")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Melalui SKT', $fromSKT)
                ->description('Surat Keterangan Terdaftar')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('Melalui SKL', $fromSKL)
                ->description('Surat Keterangan Laporan')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('gray'),

            Stat::make('Registrasi Tahun Ini', $thisYearCount)
                ->description('ORMAS yang terdaftar di tahun ' . now()->year)
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
        ];
    }
}