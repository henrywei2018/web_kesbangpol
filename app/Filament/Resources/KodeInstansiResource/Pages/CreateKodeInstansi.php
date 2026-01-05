<?php

namespace App\Filament\Resources\KodeInstansiResource\Pages;

use App\Filament\Resources\KodeInstansiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKodeInstansi extends CreateRecord
{
    protected static string $resource = KodeInstansiResource::class;
    
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     return $data;
    // }
    // protected function afterCreate(): void
    // {
    //     // Daftarkan gambar ke Spatie Media Library
    //     if ($this->data['infographic_images']) {
    //         $this->record->addMediaFromRequest('infographic_images') // Menggunakan request file langsung
    //                      ->toMediaCollection('infographic_images');
    //     }
    // }    

}
