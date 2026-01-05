@extends('components.layouts.app')

@section('content')
<section class="custom-page-header-1 page-header page-header-modern page-header-lg bg-primary border-0 z-index-1 my-0">
    <div class="custom-page-header-1-wrapper overflow-hidden">
        <div class="custom-bg-grey-1 py-5 appear-animation" data-appear-animation="maskUp"
            data-appear-animation-delay="800">
            <div class="container py-3 my-3">
                <div class="row">
                    <div class="col-md-12 align-self-center p-static text-center">
                        <div class="overflow-hidden mb-2">
                            <h1 class="font-weight-black text-12 mb-0 appear-animation" data-appear-animation="maskUp"
                                data-appear-animation-delay="1200">Berita</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none active">Berita</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="custom-page-wrapper pt-5">
    <div class="container container-xl-custom pt-5 mt-5">
        <div class="row pt-5">
            <div class="col position-relative">
                <div class="position-absolute z-index-0 appear-animation" data-appear-animation="fadeInRightShorter"
                    data-appear-animation-delay="2000" style="top: 100px; left: -80px;">
                    <h2
                        class="text-color-dark custom-stroke-text-effect-1 custom-big-font-size-1 font-weight-black opacity-1 mb-0">
                        BANGSA & POLITIK</h2>
                </div>
            </div>
        </div>

        
    <div class="py-5 my-4">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mb-5 mb-lg-0">
                <div class="row justify-content-center">
                    @if(count($posts) > 0)
                        @foreach($posts as $post)
                            <div class="col-md-6 mb-3" wire:key="post-{{ $post->id }}">
                                <a href="{{ route('post.show', $post->slug) }}" class="text-decoration-none">
                                    <div class="card card-border card-border-bottom card-border-hover bg-color-light box-shadow-6 box-shadow-hover anim-hover-translate-top-10px transition-3ms">
                                        <div class="card-img-top position-relative">
                                            <div class="p-absolute top-0 left-0 d-flex justify-content-end py-2 px-3 z-index-3">
                                                <span class="text-center bg-primary text-color-light font-weight-semibold line-height-2 px-2 py-1">
                                                    <span class="position-relative text-4 z-index-2">
                                                        {{ $post->published_at->format('d') }}
                                                        <span class="d-block text-0 positive-ls-2">
                                                            {{ strtoupper($post->published_at->format('M')) }}
                                                        </span>
                                                    </span>
                                                </span>
                                            </div>
                                            <img src="{{ $post->getFirstMediaUrl('blog/posts') }}"
                                                 class="img-fluid w-100"
                                                 style="object-fit: cover; height: 200px;"
                                                 alt="{{ $post->title }}" />
                                        </div>
                                        <div class="card-body py-3 px-2">
                                            <span class="d-block text-color-grey font-weight-semibold positive-ls-2 text-2 mb-1">
                                                BY {{ $post->author->name ?? 'Admin' }}
                                            </span>
                                            <h4 class="font-weight-bold text-4 text-color-hover-primary mb-1">
                                                {{ Str::words($post->title, 12, '...') }}
                                            </h4>
                                            <p class="card-text mb-2">{{ Str::limit($post->excerpt, 80) }}</p>
                                            <span class="read-more text-color-primary font-weight-semibold text-2">
                                                Read More <i class="fas fa-angle-right position-relative top-1 ms-1"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach

                        @if($hasMore)
                            <div class="col-12 text-center mt-4" wire:loading.remove>
                                <button wire:click="loadMore" 
                                        class="btn btn-primary">
                                    Load More
                                </button>
                            </div>
                        @endif

                        <div class="col-12 text-center mt-4" wire:loading>
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    @else
                        <div class="col-12 text-center">
                            <p>No posts found.</p>
                        </div>
                    @endif
                </div>
            </div>
@push('scripts')
<script>
    document.addEventListener('livewire:initialized', function () {
        const options = {
            root: null,
            rootMargin: '100px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    Livewire.dispatch('load-more');
                }
            });
        }, options);

        function observeLastPost() {
            const posts = document.querySelectorAll('.col-md-6.mb-3');
            if (posts.length > 0) {
                observer.observe(posts[posts.length - 1]);
            }
        }

        observeLastPost();

        Livewire.on('postsLoaded', () => {
            observeLastPost();
        });
    });
</script>
@endpush
                    {{-- Sidebar --}}
                    <div class="blog-sidebar col-lg-4 pt-4 pt-lg-0 appear-animation"
                        data-appear-animation="fadeInUpShorter" data-appear-animation-delay="300">
                        <aside class="sidebar">
                            {{-- About the Blog --}}
                            <div class="px-3 mb-4">
                                <h3 class="text-color-primary text-capitalize font-weight-bold text-5 m-0 mb-3">
                                    Berita/Artikel
                                </h3>
                                <p class="m-0">Halaman ini menyajikan Berita/Artikel terkini dan terupdate secara
                                    profesional.
                                </p>
                            </div>
                            <div class="py-1 clearfix">
                                <hr class="my-2">
                            </div>

                            {{-- Search Form --}}
                            <div class="px-3 mt-4">
                                <form wire:submit.prevent="render">
                                    <div class="input-group mb-3 pb-1">
                                        <!-- Bind search input to Livewire's search property with debounce -->
                                        <input wire:model.debounce.300ms="search"
                                            class="form-control box-shadow-none text-1 border-0 bg-color-grey"
                                            placeholder="Search..." name="s" id="s" type="text">

                                        <button type="submit" class="btn bg-color-grey text-1 p-2">
                                            <i class="fas fa-search m-2"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="py-1 clearfix">
                                <hr class="my-2">
                            </div>

                            {{-- Recent Posts --}}
                            <div class="px-3 mt-4">
                                <h3 class="text-color-primary text-capitalize font-weight-bold text-5 m-0 mb-3">
                                    Berita/Artikel
                                    Terbaru
                                </h3>
                                <div class="pb-2 mb-1">
                                    @foreach($recentPosts as $recent)
                                    <a href="{{ route('post.show', $recent->slug) }}"
                                        class="text-color-default text-uppercase text-1 mb-0 d-block text-decoration-none">
                                        {{ $recent->published_at->format('d M Y') }}
                                        <span class="opacity-3 d-inline-block px-2">|</span>
                                        {{ $recent->author->name ?? 'Admin' }}
                                    </a>
                                    <a href="{{ route('post.show', $recent->slug) }}"
                                        class="text-color-dark text-hover-primary font-weight-bold text-3 d-block pb-3 line-height-4">
                                        {{ Str::words($recent->title, 12, '...') }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="py-1 clearfix">
                                <hr class="my-2">
                            </div>

                            {{-- Categories --}}
                            <div class="px-3 mt-4">
                                <h3 class="text-color-primary text-capitalize font-weight-bold text-5 m-0">Categories
                                </h3>
                                <ul class="nav nav-list flex-column mt-2 mb-0 p-relative right-9">
                                    <!-- "All Categories" link to reset category filter -->
                                    <li class="nav-item">
                                        <a class="nav-link bg-transparent border-0" href="{{ route('post.index') }}">
                                            All Categories
                                        </a>
                                    </li>
                                    @foreach($categories as $category)
                                    <li class="nav-item">
                                        <!-- Dynamically update the URL when clicking on a category -->
                                        <a class="nav-link bg-transparent border-0"
                                            href="{{ route('post.index', ['category' => $category->slug]) }}">
                                            {{ $category->name }} ({{ $category->posts_count }})
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                        </aside>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

@endsection