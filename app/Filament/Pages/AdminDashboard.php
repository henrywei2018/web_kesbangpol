<?php
// app/Filament/Pages/AdminDashboard.php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
use App\Models\SKT;
use App\Models\LaporATHG;
use App\Services\StatusService;
use Illuminate\Support\Facades\DB;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard Admin';
    protected static ?int $navigationSort = -10;
    protected static string $view = 'filament.pages.admin-dashboard';
    protected static ?string $title = 'Dashboard Admin';
    protected static ?string $slug = 'dashboard-admin';
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public function mount(): void
    {
        // Check if user has permission to access admin dashboard
        if (!auth()->user()->hasAnyRole(['super_admin', 'admin', 'editor'])) {
            redirect()->to('/panel')->send();
        }
    }

    protected function getViewData(): array
    {
        return [
            'overviewStats' => $this->getOverviewStats(),
            'permohonanStats' => $this->getPermohonanStats(),
            'keberatanStats' => $this->getKeberatanStats(),
            'sktStats' => $this->getSKTStats(),
            'athgStats' => $this->getATHGStats(),
            'userRoleStats' => $this->getUserRoleStats(),
            'recentActivities' => $this->getRecentActivities(),
            'performanceMetrics' => $this->getPerformanceMetrics(),
        ];
    }

    private function getOverviewStats(): array
    {
        $totalUsers = User::count();
        $totalPermohonan = PermohonanInformasiPublik::count();
        $totalKeberatan = KeberatanInformasiPublik::count();
        $totalSKT = SKT::count();
        $totalATHG = LaporATHG::count();

        // Calculate monthly growth
        $lastMonthUsers = User::where('created_at', '>=', now()->subMonth())->count();
        $monthlyGrowth = $totalUsers > 0 ? ($lastMonthUsers / $totalUsers) * 100 : 0;

        return [
            'totalUsers' => $totalUsers,
            'totalPermohonan' => $totalPermohonan,
            'totalKeberatan' => $totalKeberatan,
            'totalSKT' => $totalSKT,
            'totalATHG' => $totalATHG,
            'monthlyGrowth' => round($monthlyGrowth, 1),
            'pendingApprovals' => $this->getPendingApprovals(),
        ];
    }

    private function getPermohonanStats(): array
    {
        $permohonanWithStatus = PermohonanInformasiPublik::with(['statuses' => function($query) {
            $query->latest('created_at')->limit(1);
        }])->get();

        $stats = [
            'pending' => 0,
            'diproses' => 0,
            'selesai' => 0,
            'ditolak' => 0,
        ];

        foreach ($permohonanWithStatus as $permohonan) {
            $latestStatus = $permohonan->statuses->first();
            $status = $latestStatus ? strtolower($latestStatus->status) : 'pending';
            
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        return $stats;
    }

    private function getKeberatanStats(): array
    {
        $keberatanWithStatus = KeberatanInformasiPublik::with(['statuses' => function($query) {
            $query->latest('created_at')->limit(1);
        }])->get();

        $stats = [
            'pending' => 0,
            'diproses' => 0,
            'selesai' => 0,
            'ditolak' => 0,
        ];

        foreach ($keberatanWithStatus as $keberatan) {
            $latestStatus = $keberatan->statuses->first();
            $status = $latestStatus ? strtolower($latestStatus->status) : 'pending';
            
            if (isset($stats[$status])) {
                $stats[$status]++;
            }
        }

        return $stats;
    }

    private function getSKTStats(): array
    {
        // Assuming SKT has similar status tracking
        return [
            'pengajuan' => SKT::where('status', 'pengajuan')->count(),
            'verifikasi' => SKT::where('status', 'verifikasi')->count(),
            'selesai' => SKT::where('status', 'selesai')->count(),
            'ditolak' => SKT::where('status', 'ditolak')->count(),
        ];
    }

    private function getATHGStats(): array
    {
        return [
            'ideologi' => LaporATHG::where('bidang', 'ideologi')->count(),
            'politik' => LaporATHG::where('bidang', 'politik')->count(),
            'keamanan' => LaporATHG::where('bidang', 'keamanan')->count(),
            'budaya' => LaporATHG::where('bidang', 'budaya')->count(),
        ];
    }

    private function getUserRoleStats(): array
    {
        return [
            'super_admin' => User::role('super_admin')->count(),
            'admin' => User::role('admin')->count(),
            'public' => User::role('public')->count(),
        ];
    }

    private function getRecentActivities(): array
    {
        $activities = [];

        // Get recent permohonan
        $recentPermohonan = PermohonanInformasiPublik::with('user')
            ->latest('created_at')
            ->limit(5)
            ->get();

        foreach ($recentPermohonan as $item) {
            $activities[] = [
                'id' => 'permohonan_' . $item->id,
                'type' => 'permohonan',
                'user' => $item->user->name ?? 'Unknown',
                'action' => 'Submitted new request',
                'time' => $item->created_at->diffForHumans(),
                'status' => $item->latest_status ?? 'pending',
                'url' => route('filament.admin.resources.permohonan-informasi-publiks.view', $item),
            ];
        }

        // Get recent keberatan
        $recentKeberatan = KeberatanInformasiPublik::with('user')
            ->latest('created_at')
            ->limit(3)
            ->get();

        foreach ($recentKeberatan as $item) {
            $activities[] = [
                'id' => 'keberatan_' . $item->id,
                'type' => 'keberatan',
                'user' => $item->user->name ?? 'Unknown',
                'action' => 'Filed objection',
                'time' => $item->created_at->diffForHumans(),
                'status' => $item->latest_status ?? 'pending',
                'url' => route('filament.admin.resources.keberatan-informasi-publiks.view', $item),
            ];
        }

        // Sort by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }

    private function getPerformanceMetrics(): array
    {
        // Calculate average processing time
        $avgProcessingTime = DB::table('mt_status')
            ->where('status', 'selesai')
            ->whereNotNull('updated_at')
            ->avg(DB::raw('DATEDIFF(updated_at, created_at)'));

        // Calculate approval rate
        $totalProcessed = DB::table('mt_status')
            ->whereIn('status', ['selesai', 'ditolak'])
            ->count();
        
        $totalApproved = DB::table('mt_status')
            ->where('status', 'selesai')
            ->count();

        $approvalRate = $totalProcessed > 0 ? ($totalApproved / $totalProcessed) * 100 : 0;

        return [
            'avgProcessingTime' => round($avgProcessingTime ?? 0, 1),
            'approvalRate' => round($approvalRate, 1),
            'userSatisfaction' => 4.7, // This would come from a ratings table
        ];
    }

    private function getPendingApprovals(): int
    {
        return DB::table('mt_status')
            ->whereIn('status', ['pending', 'diproses'])
            ->count();
    }
}