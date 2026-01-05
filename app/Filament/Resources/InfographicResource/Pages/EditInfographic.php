<?php

namespace App\Filament\Resources\InfographicResource\Pages;

use App\Filament\Resources\InfographicResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInfographic extends EditRecord
{
    protected static string $resource = InfographicResource::class;
    
    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     return $data;
    // }

    // protected function afterSave(): void
    // {
    //     // Periksa apakah ada gambar baru yang diunggah, lalu tambahkan ke Spatie Media Library
    //     if ($this->data['infographic_images']) {
    //         $this->record->clearMediaCollection('infographic_images'); // Hapus gambar lama
    //         $this->record->addMediaFromRequest('infographic_images') // Menggunakan request file langsung
    //                      ->toMediaCollection('infographic_images');
    //     }
    // }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
