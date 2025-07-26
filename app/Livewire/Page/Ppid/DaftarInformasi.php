<?php

namespace App\Livewire\Page\Ppid;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Publication;
use App\Models\PublicationSubcategory;
use Illuminate\Support\Facades\DB;

class DaftarInformasi extends Component
{
    use WithPagination;
    
    public $search = '';
    public $perPage = 10;
    public $selectedYear = '';
    public $availableYears = [];
    public $subcategoryId;

    public function mount()
    {
        // Get subcategory ID for "daftar_informasi_publik"
        $this->subcategoryId = PublicationSubcategory::where('slug', 'daftar-informasi-publik')
            ->value('id');

        // Get years only from publications in this subcategory
        $this->availableYears = Publication::select(DB::raw('YEAR(publication_date) as year'))
            ->where('subcategory_id', $this->subcategoryId)
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedYear()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedYear = '';
        $this->resetPage();
    }

    public function render()
    {   
        $query = Publication::query()
            ->with(['category', 'subcategory', 'media'])
            ->where('subcategory_id', $this->subcategoryId)
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
                });
                dd(get_class($query->getModel()));
            })
            
            ->when($this->selectedYear, function($query) {
                $query->whereYear('publication_date', $this->selectedYear);
            });

        $publikasi = $query->orderBy('publication_date', 'desc')
                        ->paginate($this->perPage);

        return view('livewire.page.ppid.daftar-informasi', [
            'publikasi' => $publikasi
        ]);
    }
}