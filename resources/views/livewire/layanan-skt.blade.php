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
                                data-appear-animation-delay="1200">LAYANAN SKT</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none active">Surat Keterangan Terdaftar</li>
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
    <p class="mb-5 pt-5 mt-5">Sub-title pellentesque pellentesque tempor tellus eget fermentum. usce lacllentesque eget tempor tellus ellentesque pelleinia tempor malesuada.</p>
    <div class="container container-xl-custom pt-1 mt-1">
        <div class="col-lg-12 " bis_skin_checked="1">
            <div class="tabs tabs-vertical tabs-left" bis_skin_checked="1">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item active" role="presentation">
                        <a class="nav-link active" href="#Registrasi" data-bs-toggle="tab" aria-selected="true"
                            role="tab">Tata Cara</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="#syarat" data-bs-toggle="tab" aria-selected="false" role="tab"
                            tabindex="-1">Syarat Ketentuan</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" href="#legalisasi" data-bs-toggle="tab" aria-selected="false" role="tab"
                            tabindex="-2">Legalisasi</a>
                    </li>
                </ul>
                <div class="tab-content" bis_skin_checked="1">
                    <div id="Registrasi" class="tab-pane active show" role="tabpanel" bis_skin_checked="1">
                        <div class="row" bis_skin_checked="1">
                            <div class="col" bis_skin_checked="1">
                                <h4 class="mb-2 mt-5"><strong>Tata cara mendapatkan layanan</strong></h4>

                                <div class="process process-vertical py-1" bis_skin_checked="1">
                                    <div class="process-step appear-animation animated fadeInUpShorter appear-animation-visible"
                                        data-appear-animation="fadeInUpShorter" data-appear-animation-delay="200"
                                        bis_skin_checked="1" style="animation-delay: 200ms;">
                                        <div class="process-step-circle" bis_skin_checked="1">
                                            <strong class="process-step-circle-content">1</strong>
                                        </div>
                                        <div class="process-step-content" bis_skin_checked="1">
                                            <h4 class="mb-1 text-4 font-weight-bold">Registrasi</h4>
                                            <p class="mb-0">Melakukan registrasi pada halaman berikut <a
                                                    href="{{ url('/admin/register') }}">Link</a> dengan mengisi seluruh
                                                form yang telah tersedia.</p>
                                        </div>
                                    </div>
                                    <div class="process-step appear-animation animated fadeInUpShorter appear-animation-visible"
                                        data-appear-animation="fadeInUpShorter" data-appear-animation-delay="400"
                                        bis_skin_checked="1" style="animation-delay: 400ms;">
                                        <div class="process-step-circle" bis_skin_checked="1">
                                            <strong class="process-step-circle-content">2</strong>
                                        </div>
                                        <div class="process-step-content" bis_skin_checked="1">
                                            <h4 class="mb-1 text-4 font-weight-bold">Verifikasi email</h4>
                                            <p class="mb-0">Melakukan verifikasi pendaftaran akun melalui tautan yang
                                                dikirimkan melalui email (dapat melakukan pengiriman ulang link dengan
                                                masuk menggunakan data pendaftaran pada tahap sebelumnya pada tautan
                                                berikut <a href="{{ url('/admin/login') }}">Link</a> ).</p>
                                        </div>
                                    </div>
                                    <div class="process-step appear-animation animated fadeInUpShorter appear-animation-visible"
                                        data-appear-animation="fadeInUpShorter" data-appear-animation-delay="600"
                                        bis_skin_checked="1" style="animation-delay: 600ms;">
                                        <div class="process-step-circle" bis_skin_checked="1">
                                            <strong class="process-step-circle-content">3</strong>
                                        </div>
                                        <div class="process-step-content" bis_skin_checked="1">
                                            <h4 class="mb-1 text-4 font-weight-bold">Lengkapi data diri</h4>
                                            <p class="mb-0">Setelah berhasil masuk silahkan melengkapi profil pengguna
                                                sebelum menggunakan layanan.</p>
                                        </div>
                                    </div>
                                    <div class="process-step appear-animation animated fadeInUpShorter appear-animation-visible"
                                        data-appear-animation="fadeInUpShorter" data-appear-animation-delay="800"
                                        bis_skin_checked="1" style="animation-delay: 800ms;">
                                        <div class="process-step-circle" bis_skin_checked="1">
                                            <strong class="process-step-circle-content">4</strong>
                                        </div>
                                        <div class="process-step-content" bis_skin_checked="1">
                                            <h4 class="mb-1 text-4 font-weight-bold">Pengunaan Layanan</h4>
                                            <p class="mb-0">Silahkan memilih layanan sesuai kebutuhan dan melengkapi
                                                seluruh persyartan yang dibutuhkan.</p>
                                        </div>
                                    </div>
                                    <div class="process-step appear-animation animated fadeInUpShorter appear-animation-visible"
                                        data-appear-animation="fadeInUpShorter" data-appear-animation-delay="800"
                                        bis_skin_checked="1" style="animation-delay: 800ms;">
                                        <div class="process-step-circle" bis_skin_checked="1">
                                            <strong class="process-step-circle-content">5</strong>
                                        </div>
                                        <div class="process-step-content" bis_skin_checked="1">
                                            <h4 class="mb-1 text-4 font-weight-bold">Legalisasi</h4>
                                            <p class="mb-0">Jika layanan telah selesai, silahkan cetak bukti layanan dan
                                                melaporkan pada pihak penyelenggara layanan.</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="syarat" class="tab-pane" role="tabpanel" bis_skin_checked="1">
                        <p><strong>Dokumen Syarat</strong></p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat.</p>
                        <p><strong>Ketentuan</strong></p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat.</p>
                    </div>
                    <div id="legalisasi" class="tab-pane" role="tabpanel" bis_skin_checked="1">
                        <p>Surat Keterangan</p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitat.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection