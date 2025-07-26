<?php

namespace App\Filament\Resources\AduanAdminResource\Pages;

use App\Filament\Resources\AduanAdminResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\AduanKomentar;
use Illuminate\Support\Facades\Auth;


class EditAduanAdmin extends EditRecord
{
    protected static string $resource = AduanAdminResource::class;

    // Mengatur form schema untuk menampilkan detail aduan dan daftar komentar
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
                'pesan' => 'Terima kasih, dengan ini kami nyatakan pelaporan ' . $this->record->judul . ' dinyatakan selesai. silahkan lakukan laporan baru dengan judul yang sesuai',
            ]);

            // Redirect ke halaman index jika status = selesai
            $this->redirect($this->getResource()::getUrl('index'));
        } else {
            // Redirect kembali ke halaman edit jika status bukan selesai
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->record->id]));
        }
    }
    
    
}
