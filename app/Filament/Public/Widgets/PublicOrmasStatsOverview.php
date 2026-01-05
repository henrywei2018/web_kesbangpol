<?php

// app/Filament/Public/Widgets/PublicOrmasStatsOverview.php

namespace App\Filament\Public\Widgets;

use App\Models\OrmasMaster;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PublicOrmasStatsOverview extends ChartWidget
{
    protected static ?string $heading = 'Status Registrasi ORMAS Kalimantan Utara';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '350px';

    public function getDescription(): ?string
    {
        $total = OrmasMaster::count();
        return "Total ORMAS terdaftar di Kalimantan Utara: {$total} organisasi";
    }

    protected function getData(): array
    {
        // Get status counts
        $statusCounts = OrmasMaster::select('status_administrasi', DB::raw('count(*) as total'))
            ->groupBy('status_administrasi')
            ->pluck('total', 'status_administrasi')
            ->toArray();

        // Ensure we have data for both statuses
        $selesai = $statusCounts['selesai'] ?? 0;
        $belumSelesai = $statusCounts['belum_selesai'] ?? 0;
        $total = $selesai + $belumSelesai;

        // Calculate percentages
        $persentaseSelesai = $total > 0 ? round(($selesai / $total) * 100, 1) : 0;
        $persentaseBelumSelesai = $total > 0 ? round(($belumSelesai / $total) * 100, 1) : 0;

        return [
            'datasets' => [
                [
                    'label' => 'Status Registrasi',
                    'data' => [$selesai, $belumSelesai],
                    'backgroundColor' => [
                        '#10b981', // Green for completed
                        '#f59e0b', // Amber for in progress (more neutral for public)
                    ],
                    'borderColor' => [
                        '#059669',
                        '#d97706',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                "Terdaftar Lengkap ({$persentaseSelesai}%)",
                "Dalam Proses ({$persentaseBelumSelesai}%)",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 13,
                        ],
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.parsed;
                            return label + ": " + value + " ORMAS";
                        }',
                    ],
                ],
            ],
            'layout' => [
                'padding' => 20,
            ],
        ];
    }
}