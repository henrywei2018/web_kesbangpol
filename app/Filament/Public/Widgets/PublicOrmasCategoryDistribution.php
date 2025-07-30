<?php

namespace App\Filament\Public\Widgets;

use App\Models\OrmasMaster;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PublicOrmasCategoryDistribution extends ChartWidget
{
    protected static ?string $heading = 'ORMAS Berdasarkan Kategori';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '350px';

    public function getDescription(): ?string
    {
        return 'Kategorisasi Organisasi Kemasyarakatan berdasarkan bidang dan karakteristik';
    }

    protected function getData(): array
    {
        // Get category distribution
        $categoryData = OrmasMaster::select('ciri_khusus', DB::raw('count(*) as total'))
            ->whereNotNull('ciri_khusus')
            ->groupBy('ciri_khusus')
            ->orderBy('total', 'desc')
            ->pluck('total', 'ciri_khusus')
            ->toArray();

        $labels = [];
        $data = [];
        $total = array_sum($categoryData);

        foreach ($categoryData as $category => $count) {
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $labels[] = "{$category} ({$percentage}%)";
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah ORMAS',
                    'data' => $data,
                    'backgroundColor' => [
                        '#059669', // Green - Keagamaan
                        '#dc2626', // Red - Kewanitaan
                        '#2563eb', // Blue - Kepemudaan
                        '#7c3aed', // Purple - Kesamaan Profesi
                        '#ea580c', // Orange - Kesamaan Kegiatan
                        '#0891b2', // Cyan - Kesamaan Bidang
                        '#65a30d', // Lime - Mitra K/L
                    ],
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const value = context.parsed.y;
                            return "Jumlah ORMAS: " + value;
                        }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Jumlah Organisasi',
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Kategori ORMAS',
                    ],
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 0,
                    ],
                ],
            ],
            'layout' => [
                'padding' => 20,
            ],
        ];
    }
}