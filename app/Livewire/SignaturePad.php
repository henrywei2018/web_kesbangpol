<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Spt;
use App\Models\SptPegawai;
use Illuminate\Support\Facades\Storage;

class SignaturePad extends Component
{
    use WithFileUploads;

    public $spt;
    public $signatureData;
    public $sptPegawaiRecords;

    protected $listeners = ['saveSignature'];

    public function mount($id)
    {   
        if (! request()->hasValidSignature()) {
        abort(403, 'Link expired or invalid');
        }
        $this->spt = Spt::findOrFail($id);
        $this->sptPegawaiRecords = SptPegawai::with(['pegawai', 'spt'])
                            ->where('spt_id', $id)
                            ->get();
    }

    public function saveSignature($signatureData)
    {
        $this->signatureData = $signatureData;

        $this->validate([
            'signatureData' => 'required|string',
        ]);

        try {
            // Sanitize base64 string
            $signatureData = str_replace(['data:image/png;base64,', ' '], ['', '+'], $this->signatureData);
            $fileName = 'signatures/' . uniqid() . '.png';
            Storage::disk('public')->put($fileName, base64_decode($signatureData));

            // Update the signature field in the database
            $this->spt->update(['signature_data' => $fileName]);

            $this->dispatchBrowserEvent('signatureSaved');
            session()->flash('message', 'Signature saved successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving signature: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.signature-pad', [
            'spt' => $this->spt,
            'sptPegawaiRecords' => $this->sptPegawaiRecords,
        ])->layout('components.layouts.app', [
            'excludeHeader' => true,
            'excludeScripts' => true,
        ]);
    }
}
