<?php

namespace App\Filament\Resources\SptPegawaiResource\Pages;

use App\Filament\Resources\SptPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSptPegawais extends ListRecords
{
    protected static string $resource = SptPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
