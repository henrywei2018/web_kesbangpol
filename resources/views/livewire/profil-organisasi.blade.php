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
                                data-appear-animation-delay="1200">Profil Organisasi</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none active">Profil</li>
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
                        PROFIL ORGANISASI</h2>
                </div>
            </div>
        </div>
        <div class="container pt-4 pb-5 mb-5">
            <div class="row">
                <div class="col">
                    <p class="font-weight-medium text-4-5 line-height-5">Badan Kesatuan Bangsa dan Politik</p>
                    <p class="text-3-5">Badan Kesatuan Bangsa dan Politik adalah perangkat pemerintah yang bertugas
                        melaksanakan
                        urusan pemerintah di bidang kesatuan bangsa dan politik dalam negeri dipimpin oleh Kepala Badan
                        yang
                        bertanggung jawab kepada Gubernur melalui Sekretaris Daerah.</p>
                </div>
            </div>
            <div class="row pt-5 pb-5 align-items-center">
                <div class="col-lg-6 text-center p-relative pt-5">

                    <div class="appear-animation custom-element-wrapper custom-element-9"
                        data-appear-animation="expandIn" data-appear-animation-delay="1000">
                        <div class="bg-color-primary particle particle-dots w-100 h-100 opacity-3"></div>
                    </div>

                    <div class="appear-animation custom-element-wrapper custom-element-10"
                        data-appear-animation="expandIn" data-appear-animation-delay="1200">
                        <div class="bg-color-primary particle particle-dots w-100 h-100 opacity-3"></div>
                    </div>

                    <div class="appear-animation custom-element-wrapper custom-element-11 p-relative rotate-r-45"
                        data-appear-animation="fadeIn" data-appear-animation-delay="300">
                        <img class="img-fluid" src="assets/img/demos/business-consulting-4/generic/generic-6.jpg"
                            alt="">
                    </div>

                </div>
                <div class="col-lg-6 pt-5 mt-5 pt-lg-0 mt-lg-0">
                    <div class="appear-animation" data-appear-animation="fadeIn" data-appear-animation-delay="300">
                        <p class="font-weight-medium text-4-5 line-height-5"></p>
                        <p class="text-3-5"></p>

                        <ul class="list list-icons list-icons-style-2 list-icons-lg">
                            <li class="line-height-9 text-3-5 mb-1">
                                <i class="fas fa-check border-width-2 text-3"></i>Merumuskan kebijakan teknis di bidang
                                kesatuan
                                bangsa dan politik.
                            </li>
                            <li class="line-height-9 text-3-5 mb-1">
                                <i class="fas fa-check border-width-2 text-3"></i>Melaksanakan kebijakan di bidang
                                pembinaan
                                ideologi Pancasila dan wawasan kebangsaan.
                            </li>
                            <li class="line-height-9 text-3-5 mb-1">
                                <i class="fas fa-check border-width-2 text-3"></i>Menyelenggarakan politik dalam negeri
                                dan
                                kehidupan demokrasi.
                            </li>
                            <li class="line-height-9 text-3-5 mb-1">
                                <i class="fas fa-check border-width-2 text-3"></i>Membina kerukunan antar suku, umat
                                beragama,
                                ras, dan golongan lainnya.
                            </li>
                            <li class="line-height-9 text-3-5 mb-1">
                                <i class="fas fa-check border-width-2 text-3"></i>Membina dan memberdayakan organisasi
                                kemasyarakatan.
                            </li>
                            <li class="line-height-9 text-3-5 mb-1">
                                <i class="fas fa-check border-width-2 text-3"></i>Melaksanakan kewaspadaan nasional dan
                                penanganan konflik sosial.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection