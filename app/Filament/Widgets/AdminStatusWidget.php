<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\PermohonanInformasiPublik;

class AdminStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Status Permohonan Informasi';
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    protected function getData(): array
    {
        $permohonanWithStatus = PermohonanInformasiPublik::with(['statuses' => function($query) {
            $query->latest('created_at')->limit(1);
        }])->get();

        $statusCounts = [
            'pending' => 0,
            'diproses' => 0,
            'selesai' => 0,
            'ditolak' => 0,
        ];

        foreach ($permohonanWithStatus as $permohonan) {
            $latestStatus = $permohonan->statuses->first();
            $status = $latestStatus ? strtolower($latestStatus->status) : 'pending';
            
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Status Distribution',
                    'data' => array_values($statusCounts),
                    'backgroundColor' => [
                        '#f59e0b', // pending - yellow
                        '#3b82f6', // diproses - blue
                        '#10b981', // selesai - green
                        '#ef4444', // ditolak - red
                    ],
                ],
            ],
            'labels' => ['Pending', 'Diproses', 'Selesai', 'Ditolak'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
