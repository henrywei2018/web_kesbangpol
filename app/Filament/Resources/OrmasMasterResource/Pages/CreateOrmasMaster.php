<?php

namespace App\Filament\Resources\OrmasMasterResource\Pages;

use App\Filament\Resources\OrmasMasterResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrmasMaster extends CreateRecord
{
    protected static string $resource = OrmasMasterResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}