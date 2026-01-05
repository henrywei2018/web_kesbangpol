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
                                data-appear-animation-delay="1200">Infografis</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none active">Infografis</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Updated section to position the image behind -->
<div class="position-relative">
    <img src="{{ url('/images/dayak patternn vert@0,13x.png') }}" class="img-fluid position-absolute top-0 right-0 " alt=""
        style="transform: translateX(60%);" />
</div>
<div class="container pt-2 pb-4">
    <div class="container container-xl-custom pt-5 mt-5">
        <div class="row pt-5">
            <div class="col position-relative">
                <div class="position-absolute z-index-0 appear-animation" data-appear-animation="fadeInRightShorter"
                    data-appear-animation-delay="200" style="top: 10px; left: -80px;">
                    <h2
                        class="text-color-dark custom-stroke-text-effect-1 custom-big-font-size-1 font-weight-black opacity-1 mb-0">
                        INFOGRAFIS</h2>
                </div>
            </div>
            <div class="container py-2" style="margin-top: 100px;">
                <!-- Filter Tabs -->
                <ul class="nav nav-pills sort-source sort-source-style-3 justify-content-center"
                    data-sort-id="portfolio" data-option-key="filter">
                    <!-- Show All Filter -->
                    <li class="nav-item" wire:click.prevent="applyFilter('*')" data-option-value="*">
                        <a class="nav-link text-2-5 text-uppercase {{ $filter === '*' ? 'active' : '' }}" href="#">Show
                            All</a>
                    </li>

                    <!-- Loop through categories dynamically -->
                    @foreach($categories as $key => $label)
                    <li class="nav-item" wire:click.prevent="applyFilter('{{ $key }}')" data-option-value=".{{ $key }}">
                        <a class="nav-link text-2-5 text-uppercase {{ $filter === $key ? 'active' : '' }}"
                            href="#">{{ $label }}</a>
                    </li>
                    @endforeach
                </ul>
                <!-- Portfolio Grid -->
                <div class="sort-destination-loader sort-destination-loader-showing mt-4 pt-2">
                    <div class="row portfolio-list sort-destination" data-sort-id="portfolio">
                        @foreach($infographics as $infographic)
                        <div class="col-md-6 col-lg-4 isotope-item {{ $infographic->kategori }}">
                            <div class="portfolio-item">
                                <a href="{{ route('infographic.show', $infographic->slug) }}">
                                    <span class="thumb-info thumb-info-lighten border-radius-0">
                                        <span class="thumb-info-wrapper border-radius-0">
                                            <img src="{{ $infographic->getFirstMediaUrl('infographics', 'thumb') }}"
                                                class="img-fluid border-radius-0" alt="{{ $infographic->judul }}">
                                            <span class="thumb-info-title">
                                                <span class="thumb-info-inner">{{ $infographic->judul }}</span>
                                                <span
                                                    class="thumb-info-type">{{ ucfirst($infographic->kategori) }}</span>
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
                        {{ $infographics->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
