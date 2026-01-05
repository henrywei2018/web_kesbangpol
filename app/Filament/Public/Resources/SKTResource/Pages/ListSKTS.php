<?php

namespace App\Filament\Public\Resources\SKTResource\Pages;

use App\Filament\Public\Resources\SKTResource;
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
