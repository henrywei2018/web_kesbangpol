<?php

namespace App\Filament\Resources\DocumentLabelResource\Pages;

use App\Filament\Resources\DocumentLabelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocumentLabel extends EditRecord
{
    protected static string $resource = DocumentLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
