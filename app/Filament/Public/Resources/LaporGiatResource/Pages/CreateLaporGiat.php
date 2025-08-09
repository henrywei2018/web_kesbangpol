<?php
// File: app/Filament/Public/Resources/LaporGiatResource/Pages/CreateLaporGiat.php

namespace App\Filament\Public\Resources\LaporGiatResource\Pages;

use App\Filament\Public\Resources\LaporGiatResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateLaporGiat extends CreateRecord
{
    protected static string $resource = LaporGiatResource::class;

    public function getTitle(): string
    {
        return 'Buat Laporan Kegiatan';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Laporan Kegiatan Berhasil Dibuat')
            ->body('Laporan kegiatan Anda telah berhasil diajukan dan sedang menunggu review.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure user_id is set
        $data['user_id'] = auth()->id();
        $data['status'] = 'pending';

        return $data;
    }
}