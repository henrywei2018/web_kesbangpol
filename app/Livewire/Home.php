<?php

namespace App\Livewire;

use App\Models\Banner;
use Livewire\Component;
use App\Models\Blog\Post;
use App\Models\Infographic;
use App\Models\Pegawai;
use App\Models\BannerCategory;
use App\Models\Publication;
use Illuminate\Support\Facades\Storage;

class Home extends Component
{
    public function render()
    {
        // Fetch the latest 3 posts
        $latestPosts = Post::orderBy('published_at', 'desc')->take(5)->get();

        $popularPosts = Post::orderBy('view_count', 'desc')->take(5)->get();

        $featuredPosts = Post::with(['media', 'category'])
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => $post->id,
                    'slug' => $post->slug,
                    'title' => $post->title,
                    'category' => $post->category?->name ?? 'Uncategorized',
                    'published_at' => $post->published_at->format('Y-m-d'),
                    'view_count' => $post->view_count ?? 0,
                    'image_url' => $post->getFirstMediaUrl('blog/posts') ?: asset('img/default-blog-image.jpg'),
                ];
            })
            ->toArray();
        // Fetch the latest 3 infographics
        $latestInfografis = Infographic::orderBy('created_at', 'desc')->take(4)->get();

        // Fetch banners for "slider" category
        $sliderCategory = BannerCategory::where('name', 'slider')->first();
        $sliderBanners = $sliderCategory ?
            Banner::where('banner_category_id', $sliderCategory->id)->with('media')->orderBy('sort')->take(4)->get() : collect();

        // Fetch banners for "card" category
        $cardCategory = BannerCategory::where('name', 'card')->first();

        $cardBanners = $cardCategory
            ? Banner::where('banner_category_id', $cardCategory->id)
            ->whereHas('media', function ($query) {
                $query->where('collection_name', 'banners'); // Filter media collection name explicitly
            })
            ->with(['media' => function ($query) {
                $query->where('collection_name', 'banners'); // Fetch only relevant media
            }])
            ->orderBy('sort')
            ->get()
            : collect();
        $pegawai = Pegawai::all();
        $daftardip = Publication::query()
            ->with(['category', 'subcategory', 'media'])
            ->where('subcategory_id', '9')
            ->orderBy('publication_date', 'desc')->get();

        $publikasi = Publication::orderBy('publication_date', 'desc')->with(['category', 'subcategory', 'media'])->take(10)->get();
        return view('livewire.home', compact('latestPosts', 'latestInfografis', 'sliderBanners', 'cardBanners', 'popularPosts', 'featuredPosts','publikasi','daftardip','pegawai'));
    }
}
