<?php

namespace App\Livewire\Components;

use Livewire\Component;

class LatestNews extends Component
{
    public $news = [
        [
            'date' => '18',
            'month' => 'FEB',
            'title' => 'Lorem ipsum dolor sit a met, consectetur',
            'image' => '/assets/img/demos/business-consulting-4/blog/post-thumb-1.jpg',
            'link' => 'demo-business-consulting-4-blog-post.html',
            'author' => 'BY ADMIN',
        ],
        [
            'date' => '15',
            'month' => 'FEB',
            'title' => 'Lorem ipsum dolor sit a met, consectetur',
            'image' => '/assets/img/demos/business-consulting-4/blog/post-thumb-2.jpg',
            'link' => 'demo-business-consulting-4-blog-post.html',
            'author' => 'BY ADMIN',
        ],
        [
            'date' => '12',
            'month' => 'FEB',
            'title' => 'Lorem ipsum dolor sit a met, consectetur',
            'image' => '/assets/img/demos/business-consulting-4/blog/post-thumb-3.jpg',
            'link' => 'demo-business-consulting-4-blog-post.html',
            'author' => 'BY ADMIN',
        ],
    ];

    public function render()
    {
        return view('livewire.latest-news');
    }
}