<?php

namespace App\Filament\Resources\SKTResource\Pages;

use App\Filament\Resources\SKTResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\SKTDocumentLabel;
use App\Models\SKTDocumentFeedback;

class CreateSKT extends CreateRecord
{
    protected static string $resource = SKTResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_pemohon'] = Auth::id(); // atau auth()->user()->id;
        return $data;
    }
    protected function afterCreate(): void
    {
        $skt = $this->record; 

        // Retrieve the form state
        $formState = $this->form->getState();

        // Loop through each document label and create an empty feedback entry
        foreach (SKTDocumentLabel::all() as $label) {
            SKTDocumentFeedback::create([
                'skt_id' => $skt->id,
                'skt_document_label_id' => $label->id,
                'verified' => false, // Use form state for verified field
                'feedback' => null,  // Use form state for feedback
            ]);
        }
    }
}

