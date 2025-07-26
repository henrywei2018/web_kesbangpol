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
                                data-appear-animation-delay="1200">Lapor Bang!</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none active">Kontak-Kami</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="custom-page-wrapper pt-5 pb-1">
    <div class="spacer py-4 my-5"></div>
    <div class="container container-xl-custom pb-5 mb-5">
        <div class="row pb-3">
            <div class="col-lg-5 position-relative">
                <div class="position-absolute z-index-0 appear-animation" data-appear-animation="fadeInRightShorter"
                    data-appear-animation-delay="2000" style="top: 60px; left: -206px;">
                    <h2
                        class="text-color-dark custom-stroke-text-effect-1 custom-big-font-size-1 font-weight-black opacity-1 mb-0">
                        LAPOR BANG!</h2>
                </div>
                
                <div class="overflow-hidden mb-2">
                    <h2 class="text-color-default positive-ls-3 line-height-3 text-4 mb-0 appear-animation"
                        data-appear-animation="maskUp" data-appear-animation-delay="1500">Sampaikan Laporan, Kritik dan Saran anda</h2>
                </div>
                <div class="overflow-hidden mb-4">
                    <h3 class="text-transform-none text-color-dark font-weight-black text-10 line-height-2 mb-0 appear-animation"
                        data-appear-animation="maskUp" data-appear-animation-delay="1700">Lapor Bang!</h3>
                </div>
                <div class="overflow-hidden mb-2" data-appear-animation="fadeInRightShorter"
                    data-appear-animation-delay="2000" style="top: 110px; left: -76px;">
                    <img src="{{ asset('assets/img/laporbang-assets-croped.png') }}" class="img-fluid opacity-10 mb-4 mt-2 appear-animation"
                    data-appear-animation="fadeInUpShorterPlus" data-appear-animation-delay="1900" alt="" />
                </div>
            </div>
            <livewire:contact-form />

        </div>
    </div>
</div>


@endsection