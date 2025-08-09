<?php

namespace App\Filament\Resources\LaporATHGResource\Pages;

use App\Filament\Resources\LaporATHGResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLaporATHG extends ViewRecord
{
    protected static string $resource = LaporATHGResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}