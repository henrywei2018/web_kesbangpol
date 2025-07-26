<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Blog\Category;

class ComponentBeritaCategory extends Component
{
    public $categories;
    public $selectedCategory = '';

    public function mount()
    {
        $this->categories = Category::withCount('posts')->get();
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->dispatch('global-filter-updated', ['category' => $categoryId]);
    }

    public function render()
    {
        return view('livewire.component-berita-category', [
            'categories' => $this->categories,
        ]);
    }
}