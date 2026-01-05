<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\Blog\Post;
use App\Models\Blog\Category;
use Illuminate\Support\Facades\Log;

class PostIndex extends Component
{
    public $search = '';
    public $category = null;
    public $perPage = 20;
    public $loadedPosts = [];
    public $hasMore = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => null],
    ];

    public function mount()
    {
        $this->loadPosts();
    }

    public function loadMore()
    {
        Log::info('Loading more posts. Current perPage: ' . $this->perPage);
        $this->perPage += 20;
        $this->loadPosts();
    }

    private function loadPosts()
    {
        $query = Post::query()
            ->with(['author', 'category'])
            ->whereNotNull('published_at')
            ->when($this->search, fn($query) => 
                $query->where('title', 'like', '%' . $this->search . '%'))
            ->when($this->category, fn($query) => 
                $query->whereHas('category', fn($q) => 
                    $q->where('slug', $this->category)))
            ->orderByDesc('published_at');

        $totalPosts = $query->count();
        Log::info('Total posts available: ' . $totalPosts);

        $posts = $query->take($this->perPage)->get();
        Log::info('Posts loaded: ' . $posts->count());

        $this->loadedPosts = $posts;
        $this->hasMore = $totalPosts > $posts->count();

        Log::info('Has more posts: ' . ($this->hasMore ? 'true' : 'false'));
    }

    public function updatedSearch()
    {
        $this->perPage = 20;
        $this->loadPosts();
    }

    public function updatedCategory()
    {
        $this->perPage = 20;
        $this->loadPosts();
    }

    public function render()
    {
        return view('livewire.post-index', [
            'posts' => $this->loadedPosts,
            'hasMore' => $this->hasMore,
            'categories' => Category::withCount('posts')->get(),
            'recentPosts' => Post::latest('published_at')->take(5)->get()
        ]);
    }
}