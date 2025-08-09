<?php
// File: app/Filament/Public/Resources/LaporGiatResource/Pages/ViewLaporGiat.php

namespace App\Filament\Public\Resources\LaporGiatResource\Pages;

use App\Filament\Public\Resources\LaporGiatResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLaporGiat extends ViewRecord
{
    protected static string $resource = LaporGiatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => $this->getRecord()->status === 'pending'),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Laporan Kegiatan';
    }
}