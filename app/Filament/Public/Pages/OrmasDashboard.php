<?php


namespace App\Filament\Public\Pages;

use Filament\Pages\Page;

class OrmasDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static string $view = 'filament.public.pages.ormas-dashboard';

    protected static ?string $navigationLabel = 'Statistik POKUS';

    protected static ?string $title = 'Data Organisasi Kemasyarakatan';

    protected static ?string $navigationGroup = 'POKUS KALTARA';

    protected static ?int $navigationSort = 1;

    public function getHeading(): string
    {
        return 'Data ORMAS Kalimantan Utara';
    }

    public function getSubheading(): ?string
    {
        return 'Informasi statistik dan data Organisasi Kemasyarakatan yang terdaftar di Provinsi Kalimantan Utara';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Public\Widgets\PublicOrmasStatsOverview::class,
            \App\Filament\Public\Widgets\PublicOrmasDetailedStats::class,
            \App\Filament\Public\Widgets\PublicOrmasRegionalDistribution::class,
            \App\Filament\Public\Widgets\PublicOrmasCategoryDistribution::class,
        ];
    }
}

