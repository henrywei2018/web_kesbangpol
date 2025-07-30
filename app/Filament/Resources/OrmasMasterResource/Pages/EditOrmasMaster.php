<?php

namespace App\Filament\Resources\OrmasMasterResource\Pages;

use App\Filament\Resources\OrmasMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrmasMaster extends EditRecord
{
    protected static string $resource = OrmasMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
