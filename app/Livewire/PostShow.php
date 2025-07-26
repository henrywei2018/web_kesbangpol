<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Blog\Post;
use App\Models\Blog\Category;

class PostShow extends Component
{
    public $post;

    public function mount($slug)
    {
        // Fetch the post by slug
        $this->post = Post::where('slug', $slug)->firstOrFail();

        $this->processContent();
        $this->incrementCount($this->post);
    }
    
    private function processContent()
    {
        $content = $this->post->content;

        // Extract image URLs
        preg_match_all('/!\[\]\(([^)]+)\)/', $content, $matches);

        // Store extracted image URLs
        $this->images = $matches[1] ?? [];

        // Remove image Markdown syntax and retain text content
        $this->textContent = preg_replace('/!\[\]\([^)]+\)/', '', $content);
    }

    public function incrementCount($post) 
    {
        $viewedPosts = session()->get('viewed_posts', []);

        if (!in_array($post->id, $viewedPosts)) {
            $post->increment('view_count');
            session()->push('viewed_posts', $post->id);
        }
    }

    public function render()
    {
        $recentPosts = Post::latest()->take(5)->get();
        $categories = Category::withCount('posts')->get();

        return view('livewire.post-show', [
            'post' => $this->post,
            'textContent' => $this->textContent,
            'recentPosts' => $recentPosts,
            'categories' => $categories,
            'images' => $this->images,
        ]);
    }
}
