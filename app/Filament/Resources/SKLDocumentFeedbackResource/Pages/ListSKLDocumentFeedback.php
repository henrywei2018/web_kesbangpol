<?php

namespace App\Filament\Resources\SKLDocumentFeedbackResource\Pages;

use App\Filament\Resources\SKLDocumentFeedbackResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSKLDocumentFeedback extends ListRecords
{
    protected static string $resource = SKLDocumentFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
