<?php

namespace App\Filament\Public\Resources\KeberatanInformasiPublikResource\Pages;

use App\Filament\Public\Resources\KeberatanInformasiPublikResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKeberatanInformasiPublik extends EditRecord
{
    protected static string $resource = KeberatanInformasiPublikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
