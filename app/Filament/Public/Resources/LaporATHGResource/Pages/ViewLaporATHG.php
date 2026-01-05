<?php

namespace App\Filament\Public\Resources\LaporATHGResource\Pages;

use App\Filament\Public\Resources\LaporATHGResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLaporATHG extends ViewRecord
{
    protected static string $resource = LaporATHGResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn ($record) => $record->status_athg === 'pending')
                ->label('Edit Laporan'),
        ];
    }

    // Add custom view layout for better UX
    public function getTitle(): string
    {
        return 'Laporan ATHG #' . $this->getRecord()->lapathg_id;
    }

    public function getSubheading(): ?string
    {
        $record = $this->getRecord();
        $statusInfo = $record->getStatusInfo();
        return 'Status: ' . $statusInfo['label'] . ' • ' . $record->created_at->format('d F Y, H:i');
    }
}