<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Blog\Post;

class ComponentBeritaTerbaru extends Component
{
    public $recentPosts = [];

    public function mount()
    {
        $this->recentPosts = Post::with('author')
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.component-berita-terbaru', [
            'recentPosts' => $this->recentPosts,
        ]);
    }
}
