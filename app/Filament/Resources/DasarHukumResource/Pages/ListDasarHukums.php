<?php

namespace App\Filament\Resources\DasarHukumResource\Pages;

use App\Filament\Resources\DasarHukumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDasarHukums extends ListRecords
{
    protected static string $resource = DasarHukumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
