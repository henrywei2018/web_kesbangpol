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
                                data-appear-animation-delay="1200">{{ $title }}</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none active">{{ $title }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="position-relative">
    <img src="{{ url('/images/dayak patternn vert@0,13x.png') }}" class="img-fluid position-absolute top-0 right-0 "
        alt="" style="transform: translateX(60%);" />
</div>
<div class="custom-page-wrapper pt-5">
    <div class="row pt-5 mt-5">
        <div class="col">
            <div class="tabs tabs-bottom tabs-center tabs-simple">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#tabsNavigationSimple1" data-bs-toggle="tab">Struktur
                            Organisasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tabsNavigationSimple2" data-bs-toggle="tab">Profil Pegawai</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tabsNavigationSimple1">
                        <div class="image-magnifier-container row flex-column flex-lg-row justify-content-center">
                            @if($struktur_chart_image)
                                <img id="magnifier-image" src="{{ Storage::url($struktur_chart_image) }}"
                                    class="img-fluid" alt="{{ $title }}" />
                            @else
                                <img id="magnifier-image" src="{{ asset('SO-2022-270x118-Baru-min-1024x448.jpg') }}"
                                    class="img-fluid" alt="Organization Image" />
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane" id="tabsNavigationSimple2">
                        <div class="row">
                            <div class="col">
                                <h4>{{ $description }}</h4>
                                <div class="lightbox"
                                    data-plugin-options="{'delegate': 'a', 'type': 'image', 'gallery': {'enabled': true}, 'mainClass': 'mfp-with-zoom', 'zoom': {'enabled': true, 'duration': 300}}">
                                    <div class="owl-carousel owl-theme stage-margin owl-carousel-init"
                                        data-plugin-options="{'items': 4, 'margin': 10, 'loop': false, 'nav': true, 'dots': false, 'stagePadding': 40}">
                                        @foreach($pegawai as $p)
                                        <div>
                                            <a class="img-thumbnail img-thumbnail-no-borders img-thumbnail-hover-icon"
                                                href="{{ $p->getFirstMediaUrl('pegawai_photos', 'compressed') ?: asset('assets/img/demos/architecture-2/authors/author-1.jpg') }}">
                                                <img class="img-fluid" src="{{ $p->getFirstMediaUrl('pegawai_photos', 'compressed') ?: asset('assets/img/demos/architecture-2/authors/author-1.jpg') }}" alt={{ $p->nama_pegawai }}>
                                            </a>
                                            <div class="team-info">
                                                <h5>{{ $p->nama_pegawai }}</h5>
                                                <span>{{ $p->nip }}</span>
                                                <p>{{ $p->jabatan }}-{{ $p->pangkat_golongan }}</p>
                                                
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container container-xl-custom pt-5 mt-5">
        <div class="row pt-5">
            <div class="col position-relative">
                <div class="position-absolute z-index-0 appear-animation" data-appear-animation="fadeInRightShorter"
                    data-appear-animation-delay="2000" style="top: 100px; left: -80px;">
                    <h2
                        class="text-color-dark custom-stroke-text-effect-1 custom-big-font-size-1 font-weight-black opacity-1 mb-0">
                        {{ strtoupper($title) }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection