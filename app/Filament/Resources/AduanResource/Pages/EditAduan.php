<?php

namespace App\Filament\Resources\AduanResource\Pages;

use App\Filament\Resources\AduanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\AduanKomentar;
use Illuminate\Support\Facades\Auth;

class EditAduan extends EditRecord
{
    protected static string $resource = AduanResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Logika untuk menyimpan komentar baru dari form
        if (!empty($data['new_comment'])) {
            AduanKomentar::create([
                'ticket' => $this->record->ticket,
                'user_id' => Auth::id(),
                'pesan' => $data['new_comment'],
            ]);

            // Kosongkan field komentar baru setelah disimpan
            $data['new_comment'] = '';
        }

        return $data;
    }
    protected function afterSave(): void
    {
        if ($this->record->status === 'selesai') {
            // Menambahkan entri baru di komentar saat status selesai
            AduanKomentar::create([
                'ticket' => $this->record->ticket,
                'user_id' => Auth::id(),
                'pesan' => 'Terima kasih, dengan ini kami nyatakan pelaporan dinyatakan selesai.',
            ]);

            // Redirect ke halaman index jika status = selesai
            $this->redirect($this->getResource()::getUrl('index'));
        } else {
            // Redirect kembali ke halaman edit jika status bukan selesai
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record->id]));
        }
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
