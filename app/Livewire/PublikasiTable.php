<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Publikasi;

class PublikasiTable extends Component
{
    use WithPagination;

    public $search = ''; // Properti untuk pencarian
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage(); // Reset halaman ketika pencarian berubah
    }

    public function render()
{
    // Query dengan filter kategori dan pencarian berdasarkan judul
    $publikasi = Publikasi::where('judul', 'like', '%' . $this->search . '%')
                ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
                ->paginate($this->perPage);

    return view('livewire.publikasi-table', [
        'publikasi' => $publikasi,
    ]);
}
}