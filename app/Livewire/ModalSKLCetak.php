<?php

namespace App\Livewire;

use Livewire\Component;
use App\models\SKLDocumentFeedback;

class ModalSKLCetak extends Component
{
    public $isModalOpen = false;
    public $sklData;
    public $validityDate; // Tanggal masa berlaku yang akan diisi oleh user

    public function openModal($sklId)
    {
        // Ambil data SKL berdasarkan ID, sesuaikan dengan model dan logika Anda
        $this->sklData = SKLDocumentFeedback::find($sklId);
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function confirmPrint()
    {
        // Validasi input tanggal masa berlaku
        $this->validate([
            'validityDate' => 'required|date',
        ]);

        // Logika untuk mencetak SKL dengan tanggal masa berlaku
        if ($this->sklData) {
            // Panggil fungsi cetak di sini, misal generate PDF atau cetak langsung
            return redirect()->route('skl.print', [
                'id' => $this->sklData->id,
                'validityDate' => $this->validityDate, // Kirim tanggal masa berlaku ke fungsi cetak
            ]);
        }

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.print-skl-modal');
    }
}
