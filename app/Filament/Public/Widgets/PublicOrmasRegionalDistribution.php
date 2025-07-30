<?php

namespace App\Filament\Public\Widgets;

use App\Models\OrmasMaster;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PublicOrmasRegionalDistribution extends ChartWidget
{
    protected static ?string $heading = 'Sebaran ORMAS per Wilayah';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '400px';

    public function getDescription(): ?string
    {
        return 'Distribusi Organisasi Kemasyarakatan berdasarkan Kabupaten/Kota di Kalimantan Utara';
    }

    protected function getData(): array
    {
        // Get regional distribution
        $regionalData = OrmasMaster::select('kab_kota', DB::raw('count(*) as total'))
            ->whereNotNull('kab_kota')
            ->groupBy('kab_kota')
            ->orderBy('total', 'desc')
            ->pluck('total', 'kab_kota')
            ->toArray();

        $labels = array_keys($regionalData);
        $data = array_values($regionalData);

        // Use consistent colors for North Kalimantan regions
        $colors = [
            '#0ea5e9', // Sky blue
            '#10b981', // Emerald
            '#f59e0b', // Amber
            '#ef4444', // Red
            '#8b5cf6', // Violet
        ];

        // Generate colors for regions
        $backgroundColors = [];
        $borderColors = [];
        
        for ($i = 0; $i < count($data); $i++) {
            $colorIndex = $i % count($colors);
            $backgroundColors[] = $colors[$colorIndex];
            $borderColors[] = $colors[$colorIndex];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah ORMAS',
                    'data' => $data,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
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
                            return label + ": " + value + " ORMAS (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'cutout' => '50%',
            'layout' => [
                'padding' => 10,
            ],
        ];
    }
}