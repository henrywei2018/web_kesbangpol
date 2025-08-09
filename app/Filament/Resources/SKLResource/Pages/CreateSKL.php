<?php

namespace App\Filament\Resources\SKLResource\Pages;

use App\Filament\Resources\SKLResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\DocumentLabel;
use App\Models\SKLDocumentFeedback;

class CreateSKL extends CreateRecord
{
    protected static string $resource = SKLResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Dapatkan id dari user yang sedang login dan set nilai id_pemohon
        $data['id_pemohon'] = Auth::id(); // atau auth()->user()->id;

        return $data;
    }
    protected function afterCreate(): void
    {
        $skl = $this->record; // The SKL record that was just created

        // Retrieve the form state
        $formState = $this->form->getState();

        // Loop through each document label and create an empty feedback entry
        foreach (DocumentLabel::all() as $label) {
            SKLDocumentFeedback::create([
                'skl_id' => $skl->id,
                'document_label_id' => $label->id,
                'verified' => false, // Use form state for verified field
                'feedback' => null,  // Use form state for feedback
            ]);
        }
    }
}
