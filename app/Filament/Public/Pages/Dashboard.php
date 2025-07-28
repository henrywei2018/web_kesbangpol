<?php

namespace App\Filament\Public\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
use Illuminate\Support\Facades\DB;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.public.pages.dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            // Add public-specific widgets here if needed
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // Add footer widgets here if needed
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
        return 'Portal untuk pengguna publik - akses layanan dan informasi yang tersedia.';
    }

    protected function getViewData(): array
    {
        $user = auth()->user();
        
        // Get user's information requests with eager loading of statuses
        $permohonanInformasi = PermohonanInformasiPublik::where('user_id', $user->id)
            ->with(['statuses' => function($query) {
                $query->latest('created_at');
            }])
            ->get();
            
        $keberatanInformasi = KeberatanInformasiPublik::where('user_id', $user->id)
            ->with(['statuses' => function($query) {
                $query->latest('created_at');
            }])
            ->get();
        
        // Calculate statistics
        $totalPermohonan = $permohonanInformasi->count();
        $totalKeberatan = $keberatanInformasi->count();
        
        // Status breakdown for Permohonan Informasi using the morphed relationship
        $permohonanDiproses = $permohonanInformasi->filter(function($item) {
            $status = $item->latest_status;
            return in_array($status, ['Pending', 'Diproses']);
        })->count();
        
        $permohonanDisetujui = $permohonanInformasi->filter(function($item) {
            $status = $item->latest_status;
            return $status === 'Selesai';
        })->count();
        
        $permohonanDitolak = $permohonanInformasi->filter(function($item) {
            $status = $item->latest_status;
            return $status === 'Ditolak';
        })->count();
        
        // Status breakdown for Keberatan using the morphed relationship
        $keberatanAktif = $keberatanInformasi->filter(function($item) {
            $status = $item->latest_status;
            return in_array($status, ['Pending', 'Diproses']);
        })->count();
        
        $keberatanSelesai = $keberatanInformasi->filter(function($item) {
            $status = $item->latest_status;
            return $status === 'Selesai';
        })->count();
        
        // Recent activities
        $recentActivities = collect();
        
        // Add recent permohonan activities
        foreach ($permohonanInformasi->sortByDesc('created_at')->take(5) as $permohonan) {
            $status = $permohonan->latest_status ?? 'Pending';
            $statusDescription = $permohonan->latest_deskripsi_status ?? 'Belum ada deskripsi';
            
            $recentActivities->push([
                'type' => 'permohonan',
                'title' => 'Permohonan Informasi #' . ($permohonan->nomor_register ?? $permohonan->id),
                'description' => $statusDescription,
                'date' => $permohonan->updated_at,
                'status' => $status,
                'url' => route('filament.public.resources.permohonan-informasi-publiks.view', $permohonan->id)
            ]);
        }
        
        // Add recent keberatan activities
        foreach ($keberatanInformasi->sortByDesc('created_at')->take(5) as $keberatan) {
            $status = $keberatan->latest_status ?? 'Pending';
            $statusDescription = $keberatan->latest_deskripsi_status ?? 'Belum ada deskripsi';
            
            $recentActivities->push([
                'type' => 'keberatan',
                'title' => 'Keberatan Informasi #' . ($keberatan->nomor_registrasi ?? $keberatan->id),
                'description' => $statusDescription,
                'date' => $keberatan->updated_at,
                'status' => $status,
                'url' => route('filament.public.resources.keberatan-informasi-publiks.view', $keberatan->id)
            ]);
        }
        
        // Sort by date and take latest 5
        $recentActivities = $recentActivities->sortByDesc('date')->take(5);
        
        return [
            'user' => $user,
            'stats' => [
                'total_permohonan' => $totalPermohonan,
                'total_keberatan' => $totalKeberatan,
                'permohonan_diproses' => $permohonanDiproses,
                'permohonan_disetujui' => $permohonanDisetujui,
                'permohonan_ditolak' => $permohonanDitolak,
                'keberatan_aktif' => $keberatanAktif,
                'keberatan_selesai' => $keberatanSelesai,
            ],
            'recent_activities' => $recentActivities,
            'permohonan_terbaru' => $permohonanInformasi->sortByDesc('created_at')->take(5),
            'keberatan_terbaru' => $keberatanInformasi->sortByDesc('created_at')->take(3),
        ];
    }
}