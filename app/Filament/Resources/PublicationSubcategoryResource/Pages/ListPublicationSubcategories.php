<?php

namespace App\Filament\Resources\PublicationSubcategoryResource\Pages;

use App\Filament\Resources\PublicationSubcategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicationSubcategories extends ListRecords
{
    protected static string $resource = PublicationSubcategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
