<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Blog\Post;

class PostCount extends Widget
{
    protected static string $view = 'filament.widgets.post-count';

    public $postCount;
    public function mount()
    {
        $this->postCount = Post::count();
    }

    protected array|string|int $columnSpan = 1;
}
