<?php

namespace App\Filament\Resources\KeberatanInformasiPublikResource\Pages;

use App\Filament\Resources\KeberatanInformasiPublikResource;
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
