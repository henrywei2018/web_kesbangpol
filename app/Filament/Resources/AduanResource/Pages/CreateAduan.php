<?php

namespace App\Filament\Resources\AduanResource\Pages;

use App\Filament\Resources\AduanResource;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;

class CreateAduan extends CreateRecord
{
    protected static string $resource = AduanResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Menyisipkan user_id dari user yang sedang login sebelum menyimpan
        $data['user_id'] = Auth::id();

        // Menghasilkan ticket unik untuk aduan
        $data['ticket'] = 'TICKET-' . strtoupper(uniqid());

        return $data;
    }
}
