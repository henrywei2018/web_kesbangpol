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
                                data-appear-animation-delay="1200">Galeri</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none active">Galeri</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="position-relative">
    <img src="images/dayak patternn vert@0,13x.png" class="img-fluid position-absolute top-0 right-0 " alt=""
        style="transform: translateX(60%);" />
</div>
<div class="container py-5" style="margin-top: 30mm;">
    <!-- Filter Tabs -->
    <ul class="nav nav-pills sort-source sort-source-style-3 justify-content-center" data-sort-id="portfolio"
        data-option-key="filter" data-plugin-options="{'layoutMode': 'fitRows', 'filter': '*'}">
        <li class="nav-item active" wire:click.prevent="applyFilter('*')" data-option-value="*">
            <a class="nav-link text-2-5 text-uppercase active" href="#">Show All</a>
        </li>
        <li class="nav-item" wire:click.prevent="applyFilter('kegiatan')" data-option-value=".kegiatan">
            <a class="nav-link text-2-5 text-uppercase" href="#">Kegiatan</a>
        </li>
        <li class="nav-item" wire:click.prevent="applyFilter('awards')" data-option-value=".awards">
            <a class="nav-link text-2-5 text-uppercase" href="#">Awards</a>
        </li>
        <li class="nav-item" wire:click.prevent="applyFilter('lainnya')" data-option-value=".lainnya">
            <a class="nav-link text-2-5 text-uppercase" href="#">Lainnya</a>
        </li>
    </ul>

    <!-- Portfolio Grid -->
    <div class="sort-destination-loader sort-destination-loader-showing mt-4 pt-2">
        <div class="row portfolio-list sort-destination" data-sort-id="portfolio">
            <!-- Loop through the galeris -->
            @foreach($galeris as $galeri)
            <div class="col-md-6 col-lg-4 isotope-item {{ $galeri->kategori }}">
                <div class="portfolio-item">
                    <a href="{{ route('galeri.show', $galeri->slug) }}">
                        <span class="thumb-info thumb-info-lighten border-radius-0">
                            <span class="thumb-info-wrapper border-radius-0">

                                <!-- Loop through all images in the 'images' array -->
                                @if(!empty($galeri->images) && is_array($galeri->images))
                                <div class="owl-carousel owl-theme dots-inside m-0"
                                    data-plugin-options="{'items': 1, 'margin': 20, 'animateOut': 'fadeOut', 'autoplay': true, 'autoplayTimeout': 3000}">
                                    @foreach($galeri->images as $image)
                                    <span><img src="{{ asset('storage/' . $image) }}" class="img-fluid border-radius-0"
                                            alt="{{ $galeri->judul }}"></span>
                                    @endforeach
                                </div>
                                @else
                                <!-- Fallback if no images are available -->
                                <img src="{{ asset('img/default-placeholder.png') }}" class="img-fluid border-radius-0"
                                    alt="Default Image">
                                @endif

                                <span class="thumb-info-title">
                                    <span class="thumb-info-inner">{{ $galeri->judul }}</span>
                                    <span class="thumb-info-type">{{ ucfirst($galeri->kategori) }}</span>
                                </span>

                                <span class="thumb-info-action">
                                    <span class="thumb-info-action-icon bg-dark opacity-8">
                                        <i class="fas fa-plus"></i>
                                    </span>
                                </span>

                            </span>
                        </span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $galeris->links() }}
        </div>
    </div>
</div>
@endsection