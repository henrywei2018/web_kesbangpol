<?php

namespace App\Filament\Resources\KonfigurasiAplikasiResource\Pages;

use App\Filament\Resources\KonfigurasiAplikasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKonfigurasiAplikasis extends ListRecords
{
    protected static string $resource = KonfigurasiAplikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
