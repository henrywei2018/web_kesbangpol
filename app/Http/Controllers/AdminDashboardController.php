<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
use App\Models\SKT;
use App\Models\LaporATHG;
use App\Services\StatusService;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Check if user has admin access
        if (!auth()->user()->hasAnyRole(['super_admin', 'admin', 'editor'])) {
            abort(403, 'Unauthorized access to admin dashboard.');
        }

        $data = [
            'overviewStats' => $this->getOverviewStats(),
            'permohonanStats' => $this->getPermohonanStats(),
            'keberatanStats' => $this->getKeberatanStats(),
            'sktStats' => $this->getSKTStats(),
            'athgStats' => $this->getATHGStats(),
            'userRoleStats' => $this->getUserRoleStats(),
            'recentActivities' => $this->getRecentActivities(),
            'performanceMetrics' => $this->getPerformanceMetrics(),
        ];

        return view('admin.dashboard', $data);
    }

    private function getOverviewStats(): array
    {
        // Same logic as in AdminDashboard page
        $totalUsers = User::count();
        $totalPermohonan = PermohonanInformasiPublik::count();
        $totalKeberatan = KeberatanInformasiPublik::count();
        $totalSKT = SKT::count();
        $totalATHG = LaporATHG::count();

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

    // Add other private methods here...
    // (Same as in AdminDashboard page)
}