<?php

// app/Filament/Public/Pages/Dashboard.php

namespace App\Filament\Public\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.public.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            // Add public-specific widgets here
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add footer widgets here
        ];
    }

    public function getTitle(): string
    {
        return 'Dashboard';
    }

    public function getHeading(): string
    {
        $user = auth()->user();
        return "Selamat datang, {$user->firstname}!";
    }

    public function getSubheading(): ?string
    {
        return 'Panel untuk pengguna publik - akses layanan dan informasi yang tersedia.';
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        
        // Calculate stats based on user's actual data
        $totalSubmissions = 0;
        $pendingRequests = 0;
        $completedRequests = 0;

        // Count SKL submissions if SKL model exists
        if (class_exists('App\Models\SKL')) {
            $sklCount = $user->skls()->count();
            $totalSubmissions += $sklCount;
            $pendingRequests += $user->skls()->whereIn('status', ['pengajuan', 'proses'])->count();
            $completedRequests += $user->skls()->where('status', 'terbit')->count();
        }

        // Count SKT submissions if SKT model exists
        if (class_exists('App\Models\SKT')) {
            $sktCount = $user->skts()->count();
            $totalSubmissions += $sktCount;
            $pendingRequests += $user->skts()->whereIn('status', ['pengajuan', 'proses'])->count();
            $completedRequests += $user->skts()->where('status', 'terbit')->count();
        }
        
        return [
            'user' => $user,
            'stats' => [
                'total_submissions' => $totalSubmissions,
                'pending_requests' => $pendingRequests,
                'completed_requests' => $completedRequests,
            ]
        ];
    }
}