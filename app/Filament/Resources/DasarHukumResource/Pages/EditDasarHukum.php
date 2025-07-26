<?php

namespace App\Filament\Resources\DasarHukumResource\Pages;

use App\Filament\Resources\DasarHukumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDasarHukum extends EditRecord
{
    protected static string $resource = DasarHukumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
