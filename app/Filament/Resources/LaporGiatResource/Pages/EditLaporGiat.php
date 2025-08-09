<?php
// File: app/Filament/Resources/LaporGiatResource/Pages/EditLaporGiat.php

namespace App\Filament\Resources\LaporGiatResource\Pages;

use App\Filament\Resources\LaporGiatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditLaporGiat extends EditRecord
{
    protected static string $resource = LaporGiatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Review Laporan Kegiatan';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Status Berhasil Diperbarui')
            ->body('Status laporan kegiatan telah berhasil diperbarui.');
    }
}