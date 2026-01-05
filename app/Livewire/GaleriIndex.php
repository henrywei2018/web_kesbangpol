<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Galeri;


class GaleriIndex extends Component
{
    use WithPagination;

    public $filter = null; // Filter for categories
    public $search = ''; // Search term
    protected $paginationTheme = 'bootstrap';

    // Sync filter and search with the URL query parameters
    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => null]
    ];

    public function applyFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage(); // Reset pagination when filter changes
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Reset pagination when search changes
    }

    public function render()
    {
        // Query galeris based on the selected filter and search term
        $galeris = Galeri::when($this->filter, function ($query) {
                        return $query->where('kategori', $this->filter);
                    })
                    ->when($this->search, function ($query) {
                        return $query->where('judul', 'like', '%' . $this->search . '%');
                    })
                    ->paginate(12); // Adjust pagination as needed

        return view('livewire.galeri-index', [
            'galeris' => $galeris,
            'filter' => $this->filter, // Pass the filter to the view
        ]);
    }
}
