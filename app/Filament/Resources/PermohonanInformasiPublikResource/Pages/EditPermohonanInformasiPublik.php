<?php

namespace App\Filament\Resources\PermohonanInformasiPublikResource\Pages;

use App\Filament\Resources\PermohonanInformasiPublikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermohonanInformasiPublik extends EditRecord
{
    protected static string $resource = PermohonanInformasiPublikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
