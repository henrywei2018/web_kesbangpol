<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Blog\Post;
use Livewire\Attributes\On;

class ComponentBeritaGrid extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $category = '';
    public $tahun = '';
    public $search = '';
    
    // Add this to preserve the grid layout during Livewire updates
    protected $listeners = ['$refresh'];

    #[On('global-filter-updated')]
    public function applyFilters($filters)
    {
        if (isset($filters['category'])) {
            $this->category = $filters['category'];
        }
        if (isset($filters['tahun'])) {
            $this->tahun = $filters['tahun'];
        }
        if (isset($filters['search'])) {
            $this->search = $filters['search'];
        }
        
        $this->resetPage();
    }

    public function render()
    {
        $records = Post::with(['category', 'author'])
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->when($this->search, fn($query) => 
                $query->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->tahun, fn($query) => 
                $query->whereYear('created_at', $this->tahun))
            ->when($this->category, fn($query) => 
                $query->whereHas('category', fn($q) => 
                    $q->where('id', $this->category)))
            ->paginate(8);

        // Add wire:key to force proper re-rendering
        return view('livewire.component-berita-grid', [
            'records' => $records,
        ])->layout('components.layouts.app', ['wire:key' => now()]);
    }
}