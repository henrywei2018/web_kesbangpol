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
                                data-appear-animation-delay="1200">INFOGRAFIS</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none">Infografis</li>
                                <li class="text-transform-none active">{{ $infographic->judul }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="position-relative">
    <img src="{{ url('/images/dayak patternn vert@0,13x.png') }}" class="img-fluid position-absolute top-0 right-0 " alt=""
        style="transform: translateX(60%);" />
</div>
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
        <div class="container pt-2 pb-4">
            <div class="row pb-4 mb-2">
                <div class="col-md-6 mb-4 mb-md-0 appear-animation" data-appear-animation="fadeInLeftShorter"
                    data-appear-animation-delay="300">
                    <div class="owl-carousel owl-theme nav-inside nav-inside-edge nav-squared nav-with-transparency nav-dark mt-3"
                        data-plugin-options="{'items': 1, 'margin': 10, 'loop': false, 'nav': true, 'dots': false}">
                        <div>
                            <div class="img-thumbnail border-0 border-radius-0 p-0 d-block">
                                <img src="{{ $infographic->getFirstMediaUrl('infographics', 'compressed') }}"
                                    class="img-fluid border-radius-0" alt="{{ $infographic->judul }}">
                            </div>
                        </div>
                    </div>
                    <hr class="solid my-5">
                    <strong
                        class="text-uppercase text-1 me-3 text-dark float-start position-relative top-2">Share</strong>
                    <ul class="social-icons">
                        <li class="social-icons-facebook"><a href="http://www.facebook.com/" target="_blank"
                                title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                        <li class="social-icons-twitter"><a href="http://www.twitter.com/" target="_blank"
                                title="Twitter"><i class="fab fa-twitter"></i></a></li>
                        <li class="social-icons-linkedin"><a href="http://www.linkedin.com/" target="_blank"
                                title="Linkedin"><i class="fab fa-linkedin-in"></i></a></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h2 class="text-color-dark font-weight-normal text-5 mb-2">{{ $infographic->judul }}</h2>
                    <p>{{ $infographic->deskripsi }}</p>
                    <hr class="solid my-5">
                    <h3 class="text-color-dark font-weight-normal text-4 mb-2">Infographic <strong
                            class="font-weight-extra-bold">Details</strong></h3>
                    <ul class="list list-icons list-primary list-borders text-2">
                        <li><i class="fas fa-caret-right left-10"></i> <strong
                                class="text-color-primary">Category:</strong>
                            {{ ucfirst($infographic->kategori) }}</li>
                        <li><i class="fas fa-caret-right left-10"></i> <strong class="text-color-primary">Date:</strong>
                            {{ $infographic->created_at->format('F Y') }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection