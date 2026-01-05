<?php

namespace App\Filament\Resources\SKLResource\Pages;

use App\Filament\Resources\SKLResource;
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
