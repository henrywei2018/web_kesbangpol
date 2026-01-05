<?php

namespace App\Filament\Public\Resources\SKLResource\Pages;

use App\Filament\Public\Resources\SKLResource;
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
        // Validate unique ormas name across SKL and SKT tables
        $rule = new \App\Rules\UniqueOrmasNameRule(null, 'skl');
        if (!$rule->passes('nama_organisasi', $data['nama_organisasi'])) {
            $this->addError('data.nama_organisasi', $rule->message());
            $this->halt();
        }

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
