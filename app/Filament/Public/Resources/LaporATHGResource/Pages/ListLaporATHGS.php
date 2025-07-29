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
                ->label('Buat Laporan Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add widgets here if needed
        ];
    }
}
