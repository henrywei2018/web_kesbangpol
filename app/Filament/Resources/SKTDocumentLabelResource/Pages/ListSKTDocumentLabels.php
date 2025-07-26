<?php

namespace App\Filament\Resources\SKTDocumentLabelResource\Pages;

use App\Filament\Resources\SKTDocumentLabelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSKTDocumentLabels extends ListRecords
{
    protected static string $resource = SKTDocumentLabelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
