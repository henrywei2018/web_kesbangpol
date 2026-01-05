<?php

namespace App\Filament\Public\Resources\SKLResource\Pages;

use App\Filament\Public\Resources\SKLResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSKLs extends ListRecords
{
    protected static string $resource = SKLResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
