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
                                data-appear-animation-delay="1200">LAYANAN PPID</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none"><a href="demo-architecture-2.html"
                                        class="text-decoration-none">Home</a></li>
                                <li class="text-transform-none active">PPID</li>
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
    <div class="container container-xl-custom">
        <div class="row">
        <div class="col-lg-12">
            <div class="tabs tabs-vertical tabs-left">
                {{-- Navigation Tabs --}}
                <ul class="nav nav-tabs" role="tablist">
                    @php
                        $tabItems = [
                            'maklumat' => 'Maklumat Layanan',
                            'dasar-hukum' => 'Dasar Hukum',
                            'profil-ppid' => 'Profil PPID',
                            'prosedur' => 'Prosedur Layanan',
                            'daftar-informasi' => 'Daftar Informasi Publik'
                        ];
                    @endphp
                    
                    @foreach ($tabItems as $id => $title)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $id === 'maklumat' ? 'active' : '' }}" 
                               href="#{{ $id }}" 
                               data-bs-toggle="tab" 
                               aria-selected="{{ $id === 'maklumat' ? 'true' : 'false' }}"
                               role="tab">{{ $title }}</a>
                        </li>
                    @endforeach
                </ul>

                {{-- Tab Contents --}}
                <div class="tab-content">
                    {{-- Service Information Tab --}}
                    <div id="maklumat" class="tab-pane active show" role="tabpanel">
                    <div class="row">
                            <div class="col">
                                <div class="text-center mb-4">                                    
                                    <h4 class="mb-2"><strong>Maklumat Pelayanan Informasi Publik</strong></h4>
                                    <h5 class="text-primary">Badan Kesatuan Bangsa dan Politik Provinsi Kalimantan Utara</h5>
                                </div>

                                <div class="service-points py-2 mx-2">
                                    @php
                                        $servicePoints = [
                                            ['icon' => 'fa-clock', 'text' => 'Memberikan pelayanan informasi yang cepat dan tepat waktu.'],
                                            ['icon' => 'fa-info-circle', 'text' => 'Memberikan kemudahan dalam mendapatkan informasi publik bidang komunikasi dan informatika yang diperlukan dengan mudah dan sederhana.'],
                                            ['icon' => 'fa-check-circle', 'text' => 'Menyediakan dan memberikan informasi publik yang akurat, benar, dan tidak menyesatkan.'],
                                            ['icon' => 'fa-list', 'text' => 'Menyediakan daftar informasi publik untuk informasi yang wajib disediakan dan diumumkan.'],
                                            ['icon' => 'fa-shield-alt', 'text' => 'Menjamin penggunaan seluruh informasi publik dan fasilitas pelayanan sesuai dengan ketentuan dan tata tertib yang berlaku.'],
                                            ['icon' => 'fa-building', 'text' => 'Menyiapkan ruang dan fasilitas yang nyaman dan tertata baik.'],
                                            ['icon' => 'fa-bolt', 'text' => 'Merespon dengan cepat permintaan informasi dan keberatan atas informasi publik yang disampaikan baik langsung maupun melalui media.'],
                                            ['icon' => 'fa-users', 'text' => 'Menyiapkan petugas informasi yang berdedikasi dan siap melayani.'],
                                            ['icon' => 'fa-eye', 'text' => 'Melakukan pengawasan internal dan evaluasi kinerja pelaksana.']
                                        ];
                                    @endphp

                                    @foreach($servicePoints as $index => $point)
                                        <div class="service-item d-flex align-items-start mb-4">
                                            <div class="service-icon me-3">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <strong>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</strong>
                                                </div>
                                            </div>
                                            <div class="service-content">
                                                <p class="mb-0">{{ $point['text'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="text-center mt-5">
                                        <p class="mb-1">Tanjung Selor, 1 Januari 2024</p>
                                        <p class="mb-1"><strong>Kepala Badan</strong></p>
                                        <p class="mb-4">Selaku</p>
                                        <p><strong>Pejabat Pengelola Informasi dan Dokumentasi</strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Other Tab Contents --}}
                    <div id="dasar-hukum" class="tab-pane" role="tabpanel">
                        <livewire:page.ppid.dasar-hukum />
                    </div>

                    <div id="profil-ppid" class="tab-pane" role="tabpanel">
                        <livewire:page.ppid.profil-ppid />
                    </div>

                    <div id="prosedur" class="tab-pane" role="tabpanel">
                        <livewire:page.ppid.prosedur />
                    </div>

                    <div id="daftar-informasi" class="tab-pane" role="tabpanel">
                        <livewire:page.ppid.daftar-informasi />
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

@endsection