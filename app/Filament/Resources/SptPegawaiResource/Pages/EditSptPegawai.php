<?php

namespace App\Filament\Resources\SptPegawaiResource\Pages;

use App\Filament\Resources\SptPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSptPegawai extends EditRecord
{
    protected static string $resource = SptPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
