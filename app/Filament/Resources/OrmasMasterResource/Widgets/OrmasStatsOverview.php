<?php

namespace App\Filament\Resources\OrmasMasterResource\Widgets;

use App\Models\OrmasMaster;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrmasStatsOverview extends ChartWidget
{
    protected static ?string $heading = 'Status Administrasi ORMAS';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    public function getDescription(): ?string
    {
        $total = OrmasMaster::count();
        return "Total ORMAS terdaftar: {$total} organisasi";
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
                    'label' => 'Status Administrasi',
                    'data' => [$selesai, $belumSelesai],
                    'backgroundColor' => [
                        '#10b981', // Green for completed
                        '#ef4444', // Red for incomplete
                    ],
                    'borderColor' => [
                        '#059669',
                        '#dc2626',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => [
                "Selesai ({$persentaseSelesai}%)",
                "Belum Selesai ({$persentaseBelumSelesai}%)",
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
                            'size' => 12,
                        ],
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const label = context.label || "";
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
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