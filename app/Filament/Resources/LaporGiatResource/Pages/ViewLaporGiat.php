<?php
// File: app/Filament/Resources/LaporGiatResource/Pages/ViewLaporGiat.php

namespace App\Filament\Resources\LaporGiatResource\Pages;

use App\Filament\Resources\LaporGiatResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLaporGiat extends ViewRecord
{
    protected static string $resource = LaporGiatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-m-document-arrow-down')
                ->url(fn () => $this->getRecord()->laporan_kegiatan_url)
                ->openUrlInNewTab()
                ->visible(fn () => !empty($this->getRecord()->laporan_kegiatan_path)),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Laporan Kegiatan';
    }
}