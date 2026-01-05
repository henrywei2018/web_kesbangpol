<?php

namespace App\Filament\Public\Resources\LaporGiatResource\Pages;

use App\Filament\Public\Resources\LaporGiatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporGiats extends ListRecords
{
    protected static string $resource = LaporGiatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Laporan Baru')
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getTitle(): string
    {
        return 'Laporan Kegiatan';
    }
}