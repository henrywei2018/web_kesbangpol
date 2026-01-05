<?php

namespace App\Filament\Public\Resources\LaporATHGResource\Pages;

use App\Filament\Public\Resources\LaporATHGResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditLaporATHG extends EditRecord
{
    protected static string $resource = LaporATHGResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()->hasRole('super_admin')),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Laporan berhasil diperbarui')
            ->body('Perubahan pada laporan Anda telah disimpan.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Reset status to pending when edited by public user
        if (auth()->user()->hasRole('public') && $this->getRecord()->status_athg !== 'pending') {
            $data['status_athg'] = 'pending';
            
            // Show notification about status reset
            Notification::make()
                ->info()
                ->title('Status Reset')
                ->body('Status laporan direset ke "Pending" karena ada perubahan.')
                ->send();
        }
        
        return $data;
    }
}