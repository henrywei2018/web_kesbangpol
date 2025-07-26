<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Infographic;
use Livewire\WithPagination;

class InfographicIndex extends Component
{
    use WithPagination;

    public $filter = '*';  // For category filtering
    protected $paginationTheme = 'bootstrap';

    // Available categories for filtering
    public $categories = [
        'ekonomi' => 'Ekonomi',
        'kesehatan' => 'Kesehatan',
        'pendidikan' => 'Pendidikan',
        'pemerintahan' => 'Pemerintahan',
        'sosial' => 'Sosial',
        'teknologi' => 'Teknologi',
        'lainnya' => 'Lainnya',
    ];

    public function applyFilter($category)
    {
        $this->filter = $category;
        $this->resetPage(); // Reset pagination when filter changes
    }

    public function render()
    {
        $infographics = Infographic::when($this->filter !== '*', function ($query) {
            $query->where('kategori', $this->filter);
        })->paginate(9);

        return view('livewire.infographic-index', [
            'infographics' => $infographics,
            'categories' => $this->categories,
        ]);
    }
}