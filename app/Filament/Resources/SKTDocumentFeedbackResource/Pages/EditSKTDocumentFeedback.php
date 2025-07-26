<?php

namespace App\Filament\Resources\SKTDocumentFeedbackResource\Pages;

use App\Filament\Resources\SKTDocumentFeedbackResource;
use Filament\Actions;
use App\Models\SKTDocumentLabel;
use App\Models\SKTDocumentFeedback;
use Filament\Resources\Pages\EditRecord;

class EditSKTDocumentFeedback extends EditRecord
{
    protected static string $resource = SKTDocumentFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function afterSave(): void
    {
        // Get the current SKL record being edited
        $skt = $this->record;

        // Retrieve the form state to get the submitted values
        $formState = $this->form->getState();

        // Loop through each document label and save/update the feedback and verified status
        foreach (SKTDocumentLabel::all() as $label) {
            // Retrieve the verified and feedback fields for this document label
            $verifiedField = $formState['verified_' . $label->id] ?? false;  // Defaults to false if not set
            $feedbackField = $formState['feedback_' . $label->id] ?? null;    // Defaults to null if not set

            // Create or update the feedback for this document label and SKL
            SKTDocumentFeedback::updateOrCreate(
                [
                    'skt_id' => $skt->id,  // Foreign key to the SKL table
                    'skt_document_label_id' => $label->id,  // Foreign key to the document label
                ],
                [
                    'verified' => $verifiedField,  // Save the verified status
                    'feedback' => $feedbackField,  // Save the feedback text
                ]
            );
        }

        // Now check if all feedback for this SKL is verified
        $allVerified = SKTDocumentFeedback::where('skt_id', $skt->id)->pluck('verified');

        // If all feedback is verified, mark SKL status as 'terbit'
        if ($allVerified->every(fn($verified) => $verified == true)) {
            $skt->status = 'terbit';  // All feedback verified, mark SKL as published
        } else {
            // If any feedback is not verified, set status to 'perbaikan'
            $skt->status = 'perbaikan';  // Not all feedback is verified, requires revision
        }

        // Save the updated status for SKL
        $skt->save();
    }
}
