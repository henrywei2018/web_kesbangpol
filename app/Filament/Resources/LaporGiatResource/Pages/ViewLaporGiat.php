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
                ->url(fn () => $this->getRecord()->laporan_kegiatan_download_url) // Fix: Use download_url
                ->openUrlInNewTab()
                ->visible(fn () => $this->getRecord()->hasLaporanFile()),
            
            Actions\Action::make('view_pdf')
                ->label('Lihat PDF')
                ->icon('heroicon-m-eye')
                ->url(fn () => $this->getRecord()->laporan_kegiatan_url) // For viewing inline
                ->openUrlInNewTab()
                ->visible(fn () => $this->getRecord()->hasLaporanFile()),
            
            Actions\Action::make('download_images_zip')
                ->label('Download Foto (ZIP)')
                ->icon('heroicon-m-archive-box-arrow-down')
                ->url(fn () => $this->getRecord()->all_images_zip_url)
                ->visible(fn () => $this->getRecord()->hasImages()),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Laporan Kegiatan';
    }
}