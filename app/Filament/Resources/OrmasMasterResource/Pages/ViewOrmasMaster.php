<?php

namespace App\Filament\Resources\OrmasMasterResource\Pages;

use App\Filament\Resources\OrmasMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrmasMaster extends ViewRecord
{
    protected static string $resource = OrmasMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}