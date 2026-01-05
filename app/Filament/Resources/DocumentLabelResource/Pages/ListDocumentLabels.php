<?php

namespace App\Filament\Resources\DocumentLabelResource\Pages;

use App\Filament\Resources\DocumentLabelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentLabels extends ListRecords
{
    protected static string $resource = DocumentLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
