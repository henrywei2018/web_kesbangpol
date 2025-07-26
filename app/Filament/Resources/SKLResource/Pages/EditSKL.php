<?php

namespace App\Filament\Resources\SKLResource\Pages;

use App\Filament\Resources\SKLResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\SKL;
use App\Models\DocumentLabel;
use App\Models\SKLDocumentFeedback;

class EditSKL extends EditRecord
{
    protected static string $resource = SKLResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
{
    $skl = $this->record;  // Mendapatkan record SKL yang sedang di-edit

    // Ambil nilai dari form state
    $formState = $this->form->getState();

    // Loop melalui setiap label dokumen dan update hanya sanggahan
    foreach (DocumentLabel::all() as $label) {
        // Ambil nilai sanggahan dari form state
        $feedbackField = $formState['feedback_' . $label->collection_name] ?? false;  // Defaults to false if not set
        $sanggahanField = $formState['sanggahan_' . $label->collection_name] ?? null;

        // Ambil record SKLDocumentFeedback
        $sanggahanRecord = SKLDocumentFeedback::where('skl_id', $skl->id)
            ->where('document_label_id', $label->id)
            ->first();

        // Jika tidak ada sanggahank record, buat record baru
        if (!$sanggahanRecord) {
            SKLDocumentFeedback::create([
                'skl_id' => $skl->id,
                'document_label_id' => $label->id,
                'verified' => false,
                'feedback' => null,
                'sanggahan' => $sanggahanField,
            ]);
        } else {
            // Jika ada, update hanya sanggahan, jangan ubah feedback
            $sanggahanRecord->update([
                'sanggahan' => $sanggahanField,  // Hanya update sanggahan
            ]);
        }
    }
    $skl->status = 'pengajuan';
    $skl->save();
    }
    

}
