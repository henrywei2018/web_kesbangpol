<?php

namespace App\Filament\Public\Resources\KeberatanInformasiPublikResource\Pages;

use App\Filament\Public\Resources\KeberatanInformasiPublikResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKeberatanInformasiPubliks extends ListRecords
{
    protected static string $resource = KeberatanInformasiPublikResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
