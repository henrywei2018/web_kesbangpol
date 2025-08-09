<?php

namespace App\Filament\Resources\SKTResource\Pages;

use App\Filament\Resources\SKTResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSKTS extends ListRecords
{
    protected static string $resource = SKTResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
