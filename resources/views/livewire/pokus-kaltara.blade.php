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
                                data-appear-animation-delay="1200">POKUS KALTARA</h1>
                        </div>
                    </div>
                    <div class="col-md-12 align-self-center">
                        <div class="overflow-hidden">
                            <ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation"
                                data-appear-animation="maskUp" data-appear-animation-delay="1450">
                                <li class="text-transform-none">
                                    <a href="{{ route('beranda') }}" class="text-decoration-none">Home</a>
                                </li>
                                <li class="text-transform-none active">POKUS KALTARA</li>
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
                                'overview' => 'Overview',
                                'search' => 'Pencarian',
                                'statistics' => 'Statistik',
                                'regions' => 'Per Wilayah',
                                'categories' => 'Per Kategori',
                                'services' => 'Layanan'
                            ];
                        @endphp
                        
                        @foreach ($tabItems as $id => $title)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $activeTab === $id ? 'active' : '' }}" 
                                   href="#{{ $id }}" 
                                   data-bs-toggle="tab" 
                                   aria-selected="{{ $activeTab === $id ? 'true' : 'false' }}" 
                                   role="tab"
                                   wire:click="setActiveTab('{{ $id }}')">
                                    {{ $title }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    {{-- Tab Contents --}}
                    <div class="tab-content">
                        {{-- Overview Tab --}}
                        <div id="overview" class="tab-pane {{ $activeTab === 'overview' ? 'active' : '' }}" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <!--<h3 class="font-weight-bold text-6 mb-3">Ringkasan</h3>-->
                                    <img src="{{ asset('images/Info_pokus_kaltara.png') }}" alt="Info Pokus Kaltara" class="w-100">
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <!--<h3 class="font-weight-bold text-6 mb-3">Ringkasan</h3>-->
                                    <iframe width="100%" height="512" src="https://www.youtube.com/embed/videoseries?si=U7tK8H9ZQPJtt_wq&amp;list=PLHsuEVtwnAjal_a70AWYKNjo-qa38JaFe" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                </div>
                            </div>

                            <div class="row">
                                @foreach($regions as $region)
                                    <div class="col-lg-6 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h5 class="card-title mb-0">{{ $region['kab_kota'] }}</h5>
                                                    <span class="badge badge-primary">{{ $region['total'] }} ORMAS</span>
                                                </div>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" 
                                                         style="width: {{ !empty($regions) ? ($region['total'] / collect($regions)->max('total')) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        {{ !empty($stats['total_ormas']) && $stats['total_ormas'] > 0 ? number_format(($region['total'] / $stats['total_ormas']) * 100, 1) : 0 }}% dari total ORMAS
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Categories Tab --}}
                        <div id="categories" class="tab-pane {{ $activeTab === 'categories' ? 'active' : '' }}" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h3 class="font-weight-bold text-6 mb-3">Distribusi per Kategori</h3>
                                    <p class="text-4 mb-4">
                                        Klasifikasi organisasi kemasyarakatan berdasarkan ciri khusus dan bidang kegiatan utama.
                                    </p>
                                </div>
                            </div>

                            <div class="row">
                                @foreach($categories as $category)
                                    <div class="col-lg-6 mb-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h5 class="card-title mb-0">{{ $category['ciri_khusus'] }}</h5>
                                                    <span class="badge badge-success">{{ $category['total'] }} ORMAS</span>
                                                </div>
                                                <div class="progress" style="height: 10px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                         style="width: {{ !empty($categories) ? ($category['total'] / collect($categories)->max('total')) * 100 : 0 }}%">
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        {{ !empty($stats['total_ormas']) && $stats['total_ormas'] > 0 ? number_format(($category['total'] / $stats['total_ormas']) * 100, 1) : 0 }}% dari total ORMAS
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Category Descriptions --}}
                                <div class="col-12 mt-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Penjelasan Kategori ORMAS</h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $categoryDescriptions = [
                                                    'Keagamaan' => 'Organisasi yang bergerak dalam bidang keagamaan, dakwah, dan pengembangan nilai-nilai spiritual.',
                                                    'Kewanitaan' => 'Organisasi yang fokus pada pemberdayaan perempuan, kesetaraan gender, dan perlindungan hak-hak perempuan.',
                                                    'Kepemudaan' => 'Organisasi yang menghimpun dan memberdayakan generasi muda untuk berpartisipasi dalam pembangunan.',
                                                    'Kesamaan Profesi' => 'Organisasi yang menghimpun individu dengan profesi yang sama untuk pengembangan kompetensi dan advokasi.',
                                                    'Kesamaan Kegiatan' => 'Organisasi yang terbentuk berdasarkan kesamaan minat dan aktivitas tertentu.',
                                                    'Kesamaan Bidang' => 'Organisasi yang bergerak dalam bidang atau sektor yang sama.',
                                                    'Mitra K/L' => 'Organisasi yang bermitra dengan Kementerian/Lembaga dalam pelaksanaan program pembangunan.'
                                                ];
                                            @endphp

                                            <div class="row">
                                                @foreach($categoryDescriptions as $name => $description)
                                                    <div class="col-lg-6 mb-3">
                                                        <h6 class="font-weight-bold">{{ $name }}</h6>
                                                        <p class="text-muted small mb-0">{{ $description }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Services Tab --}}
                        <div id="services" class="tab-pane {{ $activeTab === 'services' ? 'active' : '' }}" role="tabpanel">
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h3 class="font-weight-bold text-6 mb-3">Layanan Registrasi ORMAS</h3>
                                    <p class="text-4 mb-4">
                                        Informasi lengkap mengenai layanan registrasi dan pengelolaan organisasi kemasyarakatan 
                                        di Provinsi Kalimantan Utara.
                                    </p>
                                </div>
                            </div>

                            {{-- Service Cards --}}
                            <div class="row mb-5">
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100 border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">
                                                <i class="fas fa-file-alt me-2"></i>
                                                Layanan SKT
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text mb-3">
                                                Surat Keterangan Terdaftar (SKT) untuk organisasi kemasyarakatan yang baru mendaftar 
                                                atau melakukan perubahan data organisasi.
                                            </p>
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Registrasi ORMAS baru</li>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Perubahan data organisasi</li>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Perpanjangan status</li>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Verifikasi dokumen</li>
                                            </ul>
                                            <div class="text-center mt-4">
                                                <a href="{{ route('layanan.skt') }}" class="btn btn-primary">
                                                    <i class="fas fa-arrow-right me-2"></i>Akses Layanan SKT
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-6 mb-4">
                                    <div class="card h-100 border-info">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">
                                                <i class="fas fa-clipboard-list me-2"></i>
                                                Layanan SKL
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text mb-3">
                                                Surat Keterangan Lapor (SKL) untuk pelaporan kegiatan dan aktivitas 
                                                organisasi kemasyarakatan yang telah terdaftar.
                                            </p>
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Laporan kegiatan tahunan</li>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Laporan keuangan organisasi</li>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Update data keanggotaan</li>
                                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Monitoring kepatuhan</li>
                                            </ul>
                                            <div class="text-center mt-4">
                                                <a href="{{ route('layanan.skl') }}" class="btn btn-info">
                                                    <i class="fas fa-arrow-right me-2"></i>Akses Layanan SKL
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Process Flow --}}
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Alur Proses Registrasi ORMAS</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @php
                                                    $processSteps = [
                                                        ['step' => '1', 'title' => 'Persiapan Dokumen', 'desc' => 'Menyiapkan dokumen persyaratan sesuai ketentuan yang berlaku.'],
                                                        ['step' => '2', 'title' => 'Registrasi Online', 'desc' => 'Melakukan registrasi akun dan mengisi formulir aplikasi secara online.'],
                                                        ['step' => '3', 'title' => 'Upload Dokumen', 'desc' => 'Mengunggah semua dokumen persyaratan dalam format digital.'],
                                                        ['step' => '4', 'title' => 'Verifikasi', 'desc' => 'Tim verifikasi melakukan pemeriksaan kelengkapan dan keabsahan dokumen.'],
                                                        ['step' => '5', 'title' => 'Approval', 'desc' => 'Persetujuan dari pejabat berwenang setelah dokumen dinyatakan lengkap.'],
                                                        ['step' => '6', 'title' => 'Penerbitan Surat', 'desc' => 'Penerbitan SKT atau SKL sesuai dengan jenis permohonan yang diajukan.']
                                                    ];
                                                @endphp

                                                @foreach($processSteps as $process)
                                                    <div class="col-lg-4 col-md-6 mb-4">
                                                        <div class="text-center">
                                                            <div class="rounded-circle bg-primary text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                                <strong>{{ $process['step'] }}</strong>
                                                            </div>
                                                            <h6 class="font-weight-bold">{{ $process['title'] }}</h6>
                                                            <p class="text-muted small">{{ $process['desc'] }}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Contact Information --}}
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="font-weight-bold mb-3">Butuh Bantuan?</h5>
                                            <p class="mb-3">Tim POKUS KALTARA siap membantu Anda dalam proses registrasi dan pengelolaan ORMAS.</p>
                                            <div class="row justify-content-center">
                                                <div class="col-md-4 mb-2">
                                                    <i class="fas fa-phone text-primary me-2"></i>
                                                    <strong>Telepon:</strong> (0556) 21234
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <i class="fas fa-envelope text-primary me-2"></i>
                                                    <strong>Email:</strong> pokus@kaltaraprov.go.id
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <i class="fas fa-clock text-primary me-2"></i>
                                                    <strong>Jam Layanan:</strong> 08:00 - 16:00 WIT
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* PPID-style tabs */
.tabs-vertical.tabs-left .nav-tabs {
    border-bottom: 0;
    border-right: 1px solid #dee2e6;
}

.tabs-vertical.tabs-left .nav-tabs .nav-item {
    float: none;
    margin-bottom: 0;
    margin-right: -1px;
}

.tabs-vertical.tabs-left .nav-tabs .nav-link {
    border: 1px solid transparent;
    border-radius: 0.375rem 0 0 0.375rem;
    margin-right: 0;
    text-align: left;
    padding: 1rem 1.5rem;
    color: #6c757d;
    font-weight: 500;
    transition: all 0.3s ease;
}

.tabs-vertical.tabs-left .nav-tabs .nav-link:hover {
    border-color: #e9ecef #dee2e6 #e9ecef #e9ecef;
    background-color: #f8f9fa;
    color: #495057;
}

.tabs-vertical.tabs-left .nav-tabs .nav-link.active {
    color: #495057;
    background-color: #fff;
    border-color: #dee2e6 #fff #dee2e6 #dee2e6;
    font-weight: 600;
}

.tabs-vertical.tabs-left .tab-content {
    padding-left: 2rem;
}

/* Feature boxes styling */
.feature-box {
    transition: all 0.3s ease;
}

.feature-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.feature-box-icon i {
    transition: all 0.3s ease;
}

.feature-box:hover .feature-box-icon i {
    transform: scale(1.1);
}

/* Service items */
.service-item {
    transition: all 0.3s ease;
    padding: 1rem;
    border-radius: 0.5rem;
}

.service-item:hover {
    background-color: #f8f9fa;
    transform: translateX(10px);
}

.service-icon {
    flex-shrink: 0;
}

/* Cards styling */
.card {
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
    font-weight: 600;
}

/* Progress bars */
.progress {
    border-radius: 1rem;
    overflow: hidden;
}

.progress-bar {
    transition: width 1s ease-in-out;
}

/* Badges */
.badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
    border-radius: 1rem;
}

.badge-outline-primary {
    color: #007bff;
    border: 1px solid #007bff;
    background: transparent;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .tabs-vertical.tabs-left .nav-tabs {
        border-right: 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .tabs-vertical.tabs-left .nav-tabs .nav-link {
        border-radius: 0.375rem 0.375rem 0 0;
        text-align: center;
    }
    
    .tabs-vertical.tabs-left .nav-tabs .nav-link.active {
        border-color: #dee2e6 #dee2e6 #fff #dee2e6;
    }
    
    .tabs-vertical.tabs-left .tab-content {
        padding-left: 0;
        padding-top: 1rem;
    }
    
    .service-item:hover {
        transform: none;
    }
}

/* Loading animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

[wire\:loading.remove] {
    animation: fadeIn 0.3s ease-in-out;
}

/* Custom spacing */
.spacer {
    height: 2rem;
}

/* Consistent typography */
h3.font-weight-bold {
    color: #2c3e50;
}

.text-muted {
    color: #6c757d !important;
}

/* Custom button styles */
.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Table responsive */
.table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching animation
    const tabLinks = document.querySelectorAll('.nav-tabs .nav-link');
    const tabPanes = document.querySelectorAll('.tab-pane');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs and panes
            tabLinks.forEach(l => l.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding pane
            const targetId = this.getAttribute('href').substring(1);
            const targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });
    
    // Smooth scroll to top when tab changes
    window.addEventListener('tab-changed', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});
</script>
@endpush