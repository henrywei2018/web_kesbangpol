<?php
// File: app/Filament/Public/Resources/LaporGiatResource/Pages/EditLaporGiat.php

namespace App\Filament\Public\Resources\LaporGiatResource\Pages;

use App\Filament\Public\Resources\LaporGiatResource;
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
            Actions\DeleteAction::make()
                ->visible(fn () => $this->getRecord()->status === 'pending'),
        ];
    }

    public function getTitle(): string
    {
        return 'Edit Laporan Kegiatan';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Laporan Kegiatan Berhasil Diperbarui')
            ->body('Perubahan pada laporan kegiatan Anda telah berhasil disimpan.');
    }
}