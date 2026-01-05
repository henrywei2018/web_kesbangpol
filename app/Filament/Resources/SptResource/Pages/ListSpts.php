<?php

namespace App\Filament\Resources\SptResource\Pages;

use App\Filament\Resources\SptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSpts extends ListRecords
{
    protected static string $resource = SptResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
