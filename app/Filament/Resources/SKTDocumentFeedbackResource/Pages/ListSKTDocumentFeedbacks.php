<?php

namespace App\Filament\Resources\SKTDocumentFeedbackResource\Pages;

use App\Filament\Resources\SKTDocumentFeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSKTDocumentFeedbacks extends ListRecords
{
    protected static string $resource = SKTDocumentFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
