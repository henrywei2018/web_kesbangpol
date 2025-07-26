<?php

namespace App\Filament\Resources\SKTResource\Pages;

use App\Filament\Resources\SKTResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\SKT;
use App\Models\SKTDocumentLabel;
use App\Models\SKTDocumentFeedback;

class EditSKT extends EditRecord
{
    protected static string $resource = SKTResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
{
    $skt = $this->record;  // Mendapatkan record SKL yang sedang di-edit

    // Ambil nilai dari form state
    $formState = $this->form->getState();

    // Loop melalui setiap label dokumen dan update hanya sanggahan
    foreach (SKTDocumentLabel::all() as $label) {
        // Ambil nilai sanggahan dari form state
        $feedbackField = $formState['feedback_' . $label->collection_name] ?? false;  // Defaults to false if not set
        $sanggahanField = $formState['sanggahan_' . $label->collection_name] ?? null;

        // Ambil record SKLDocumentFeedback
        $sanggahanRecord = SKTDocumentFeedback::where('skt_id', $skt->id)
            ->where('skt_document_label_id', $label->id)
            ->first();

        // Jika tidak ada sanggahank record, buat record baru
        if (!$sanggahanRecord) {
            SKTDocumentFeedback::create([
                'skt_id' => $skt->id,
                'skt_document_label_id' => $label->id,
                'verified' => false,
                'feedback' => null,
                'sanggahan' => null,
            ]);
        } else {
            // Jika ada, update hanya sanggahan, jangan ubah feedback
            $sanggahanRecord->update([
                'sanggahan' => $sanggahanField,  // Hanya update sanggahan
            ]);
        }
    }
    $skt->status = 'pengajuan';
    $skt->save();
    }
}
