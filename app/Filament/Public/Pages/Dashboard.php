<?php
// app/Filament/Public/Pages/Dashboard.php

namespace App\Filament\Public\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Services\StatusService;
use App\Models\PermohonanInformasiPublik;
use App\Filament\Public\Widgets\StatsOverview;
use App\Filament\Public\Widgets\WelcomeCard;
use App\Filament\Public\Widgets\RecentActivity;
use App\Filament\Public\Widgets\QuickActions;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    // Use the custom view if you want, or let Filament handle it with widgets
    // protected static string $view = 'filament.public.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            WelcomeCard::class,
            StatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            QuickActions::class,
            RecentActivity::class,
        ];
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function getHeading(): string
    {
        return 'Dashboard Publik';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola permohonan dan keberatan informasi publik Anda.';
    }
    protected function getViewData(): array
    {
        $user = auth()->user();
        $userId = $user->id;

        // Gunakan StatusService
        $statistics = StatusService::getUserStatistics($userId);
        $recentActivities = StatusService::getRecentActivities($userId);

        // Get data untuk tabel
        $permohonanTerbaru = PermohonanInformasiPublik::where('id_pemohon', $userId)
            ->with(['statuses' => function($query) {
                $query->latest('created_at')->limit(1);
            }])
            ->latest('created_at')
            ->take(5)
            ->get();

        return [
            'user' => $user,
            'stats' => [
                'total_permohonan' => $statistics['permohonan']['total'],
                'permohonan_diproses' => $statistics['permohonan']['pending'] + $statistics['permohonan']['diproses'],
                'permohonan_disetujui' => $statistics['permohonan']['selesai'],
                // ... dst
            ],
            'recent_activities' => $recentActivities,
            'permohonan_terbaru' => $permohonanTerbaru,
        ];
    }
}