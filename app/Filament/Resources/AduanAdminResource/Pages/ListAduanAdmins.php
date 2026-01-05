<?php

namespace App\Filament\Resources\AduanAdminResource\Pages;

use App\Filament\Resources\AduanAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAduanAdmins extends ListRecords
{
    protected static string $resource = AduanAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
