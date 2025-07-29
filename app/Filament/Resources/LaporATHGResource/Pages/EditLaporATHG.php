<?php

namespace App\Filament\Resources\LaporATHGResource\Pages;

use App\Filament\Resources\LaporATHGResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporATHG extends EditRecord
{
    protected static string $resource = LaporATHGResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
