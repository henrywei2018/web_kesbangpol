<?php

namespace App\Filament\Public\Resources\LaporATHGResource\Pages;

use App\Filament\Public\Resources\LaporATHGResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporATHGS extends ListRecords
{
    protected static string $resource = LaporATHGResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Laporan ATHG')
                ->icon('heroicon-o-plus'), // Use danger color to emphasize urgency
        ];
    }

    public function getTitle(): string
    {
        return 'Laporan ATHG Saya';
    }

    public function getSubheading(): ?string
    {
        $total = $this->getTableQuery()->count();
        $urgent = $this->getTableQuery()->whereIn('tingkat_urgensi', ['tinggi', 'kritis'])->count();
        
        if ($urgent > 0) {
            return "Total {$total} laporan • {$urgent} laporan urgent memerlukan perhatian";
        }
        
        return "Total {$total} laporan ATHG";
    }

    // Add custom empty state
    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Belum Ada Laporan ATHG';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Laporkan situasi Ancaman, Tantangan, Hambatan, atau Gangguan yang memerlukan perhatian khusus.';
    }

    protected function getTableEmptyStateActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Laporan ATHG')
                ->icon('heroicon-o-plus'),
        ];
    }
}