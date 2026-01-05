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
                                data-appear-animation-delay="1200">BERITA</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none">Blog</li>
                                <li class="text-transform-none active">{{ $post->title }}</li>
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
                    <!-- Post Content Column -->
                    <div class="col-lg-8 mb-5 mb-lg-0">
                        <article>
                            <div class="card border-0">
                                <div class="card-body z-index-1 p-0">
                                    <!-- Post Meta Data -->
                                    <div class="post-image pb-4">
                                        <img 
                                            class="card-img-top rounded-0" 
                                            src="{{ $post->getFirstMediaUrl('blog/posts') }}" 
                                            alt="{{ $post->title }}">
                                    </div>
                                    <p class="text-uppercase text-1 mb-3 text-color-default">
                                        <time pubdate datetime="{{ $post->created_at->toDateString() }}">
                                            {{ $post->created_at->format('d M Y') }}
                                        </time>
                                        <span class="opacity-3 d-inline-block px-2">|</span>
                                        {{ $post->view_count }} kali baca
                                        <span class="opacity-3 d-inline-block px-2">|</span>
                                        {{ $post->author->name ?? 'Admin' }}
                                    </p>

                                    <!-- Post Content -->
                                    


                                    <!-- Text Content -->
                                    <p>{!! nl2br(e($textContent)) !!}</p>
                                    <div class="card-body p-0">
                                        <div class="container-fluid p-0">
                                            <div class="row g-0">
                                                <div class="col" style="min-height: 250px; overflow: hidden; position: relative;">
                                                    <div class="row portfolio-list lightbox" 
                                                         data-plugin-options="{'delegate': 'a.lightbox-portfolio', 'type': 'image', 'gallery': {'enabled': true}}">
                                                        @foreach($images as $image)
                                                            <div class="col-12 col-sm-6 col-lg-3 appear-animation" 
                                                                 data-appear-animation="expandIn" 
                                                                 data-appear-animation-delay="200">
                                                                <div class="portfolio-item">
                                                                    <span class="thumb-info thumb-info-lighten thumb-info-centered-icons border-radius-0">
                                                                        <span class="thumb-info-wrapper border-radius-0">
                                                                            <img src="{{ $image }}" 
                                                                                 class="img-fluid border-radius-0" 
                                                                                 alt="Image" style="height: 100%; width: auto; object-fit: cover; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); overflow: hidden;">
                                                                            <span class="thumb-info-action">
                                                                                <a href="{{ $image }}" class="lightbox-portfolio">
                                                                                    <span class="thumb-info-action-icon thumb-info-action-icon-light">
                                                                                        <i class="fas fa-search text-dark"></i>
                                                                                    </span>
                                                                                </a>
                                                                            </span>
                                                                        </span>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                
                                    <!-- Social Share Buttons -->
                                    <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
                                        <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                                        <a class="a2a_button_facebook"></a>
                                        <a class="a2a_button_x"></a>
                                        <a class="a2a_button_copy_link"></a>
                                    </div>
                                    <script async src="https://static.addtoany.com/menu/page.js"></script>
                
                                    <!-- Divider -->
                                    <hr class="my-5">

                    <!-- Post Author Section -->
                                    <div class="post-block post-author">
                                        <h3 class="text-color-dark text-capitalize font-weight-bold text-5 m-0 mb-3">Author</h3>
                                        <div class="img-thumbnail img-thumbnail-no-borders d-block pb-3">
                                            <a href="#">
                                                <img src="{{ $post->author?->getFilamentAvatarUrl() ?? asset('img/avatars/avatar.jpg') }}" class="rounded-circle" alt="Author Avatar">
                                            </a>
                                        </div>
                                        <p>
                                        <strong class="name">
                                            <a href="#" class="text-4 text-dark pb-2 pt-2 d-block">
                                                {{ $post->author->name ?? 'Admin' }}
                                            </a>
                                        </strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Sidebar Column -->
                    @include('components.layouts.partials.post-sidebar', ['recentPosts' => $recentPosts, 'categories' =>
                    $categories])

                </div>
            </div>
        </div>
    </div>
</div>

@endsection