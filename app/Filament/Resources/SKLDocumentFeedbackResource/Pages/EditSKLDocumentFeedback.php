<?php

namespace App\Filament\Resources\SKLDocumentFeedbackResource\Pages;

use App\Filament\Resources\SKLDocumentFeedbackResource;
use Filament\Actions;
use App\Models\SKL;
use App\Models\DocumentLabel;
use App\Models\SKLDocumentFeedback;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;

class EditSKLDocumentFeedback extends EditRecord
{
    protected static string $resource = SKLDocumentFeedbackResource::class;

    protected function afterSave(): void
    {
        // Get the current SKL record being edited
        $skl = $this->record;

        // Retrieve the form state to get the submitted values
        $formState = $this->form->getState();

        // Loop through each document label and save/update the feedback and verified status
        foreach (DocumentLabel::all() as $label) {
            // Retrieve the verified and feedback fields for this document label
            $verifiedField = $formState['verified_' . $label->id] ?? false;  // Defaults to false if not set
            $feedbackField = $formState['feedback_' . $label->id] ?? null;    // Defaults to null if not set

            // Create or update the feedback for this document label and SKL
            SKLDocumentFeedback::updateOrCreate(
                [
                    'skl_id' => $skl->id,  // Foreign key to the SKL table
                    'document_label_id' => $label->id,  // Foreign key to the document label
                ],
                [
                    'verified' => $verifiedField,  // Save the verified status
                    'feedback' => $feedbackField,  // Save the feedback text
                ]
            );
        }

        // Now check if all feedback for this SKL is verified
        $allVerified = SKLDocumentFeedback::where('skl_id', $skl->id)->pluck('verified');

        // If all feedback is verified, mark SKL status as 'terbit'
        if ($allVerified->every(fn($verified) => $verified == true)) {
            $skl->status = 'terbit';  // All feedback verified, mark SKL as published
        } else {
            // If any feedback is not verified, set status to 'perbaikan'
            $skl->status = 'perbaikan';  // Not all feedback is verified, requires revision
        }

        // Save the updated status for SKL
        $skl->save();
    }
    public static function getEloquentQuery(): Builder
{
    // Eager load documentFeedbacks to ensure it's available for both list and edit pages
    return SKL::query()->with('documentFeedbacks');
}
}
