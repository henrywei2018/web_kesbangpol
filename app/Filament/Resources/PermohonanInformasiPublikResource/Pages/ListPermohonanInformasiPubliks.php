<?php

namespace App\Filament\Resources\PermohonanInformasiPublikResource\Pages;

use App\Filament\Resources\PermohonanInformasiPublikResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermohonanInformasiPubliks extends ListRecords
{
    protected static string $resource = PermohonanInformasiPublikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
