<div class="col-lg-7 mx-auto">
    <!-- Tabs -->
    <ul class="nav nav-tabs nav-fill mb-4" role="tablist">
        <li class="nav-item">
            <button
                class="nav-link {{ $activeTab === 'submit' ? 'active' : '' }}"
                wire:click="switchTab('submit')"
                type="button">
                <i class="fas fa-edit me-2"></i>Kirim Aduan
            </button>
        </li>
        <!-- <li class="nav-item">
            <button
                class="nav-link {{ $activeTab === 'track' ? 'active' : '' }}"
                wire:click="switchTab('track')"
                type="button">
                <i class="fas fa-search me-2"></i>Lacak Aduan
            </button>
        </li> -->
    </ul>

    <!-- Submit Form Tab -->
    @if($activeTab === 'submit')
    @if($showForm)
    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <h4 class="card-title">Sampaikan Aduan Anda</h4>
                <p class="text-muted">Silakan lengkapi form di bawah ini</p>
            </div>

            @if($isLocalEnv)
            <button
                type="button"
                wire:click="autofill"
                class="btn btn-secondary mb-3">
                Auto-fill Form (Dev Only)
            </button>
            @endif

            <form wire:submit.prevent="submitForm">
                <!-- Alerts -->
                @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif

                @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif

                <!-- Nama -->
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input
                        type="text"
                        id="nama"
                        class="form-control @error('nama') is-invalid @enderror"
                        wire:model="nama"
                        placeholder="Masukkan nama lengkap">
                    @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        class="form-control @error('email') is-invalid @enderror"
                        wire:model="email"
                        placeholder="Masukkan email">
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nomor Telepon -->
                <div class="mb-3">
                    <label for="telpon" class="form-label">Nomor Telepon</label>
                    <div class="input-group">
                        <span class="input-group-text">+62</span>
                        <input
                            type="tel"
                            id="telpon"
                            class="form-control @error('telpon') is-invalid @enderror"
                            wire:model.live="telpon"
                            placeholder="812-3456-7890">
                    </div>
                    @error('telpon')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Judul Aduan -->
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Aduan</label>
                    <input
                        type="text"
                        id="judul"
                        class="form-control @error('judul') is-invalid @enderror"
                        wire:model="judul"
                        placeholder="Masukkan judul aduan">
                    @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Kategori -->
                <div class="mb-3">
                    <label for="kategori" class="form-label">Kategori</label>
                    <select
                        id="kategori"
                        class="form-select @error('kategori') is-invalid @enderror"
                        wire:model="kategori">
                        <option value="">Pilih kategori</option>
                        @foreach($kategoriOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea
                        id="deskripsi"
                        class="form-control @error('deskripsi') is-invalid @enderror"
                        wire:model="deskripsi"
                        rows="5"
                        placeholder="Jelaskan detail aduan Anda"></textarea>
                    @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Turnstile (Non-Local Environments) -->
                <div class="mb-3">
                    <div 
                        id="turnstile-container"
                        class="cf-turnstile" 
                        data-theme="light"
                    ></div>
                    @error('turnstileResponse')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Aduan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @else
    <!-- Success View -->
    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <h4 class="text-success">Aduan Berhasil Terkirim!</h4>
                <div class="border border-success rounded p-3 d-inline-block">
                    <p class="mb-1">Nomor Tiket Anda:</p>
                    <h3 class="text-success mb-0">{{ $ticketNumber }}</h3>
                </div>
            </div>

            <div class="mt-4">
                <h5>Detail Aduan:</h5>
                <table class="table">
                    <tr>
                        <td class="fw-bold" width="30%">Nama</td>
                        <td>{{ $submittedAduan->nama }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Email</td>
                        <td>{{ $submittedAduan->email }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Telepon</td>
                        <td>{{ $submittedAduan->telpon }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Judul</td>
                        <td>{{ $submittedAduan->judul }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Kategori</td>
                        <td>{{ $submittedAduan->kategori }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Deskripsi</td>
                        <td>{{ $submittedAduan->deskripsi }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @endif

    @elseif($activeTab === 'track')
    <!-- Search Ticket Form -->
    <div class="card">
        <div class="card-body">
            <div class="text-center mb-4">
                <h4 class="card-title">Lacak Aduan Anda</h4>
                <p class="text-muted">Masukkan nomor tiket Anda untuk melihat status</p>
            </div>

            @if (session()->has('search-error'))
                <div class="alert alert-danger">
                    {{ session('search-error') }}
                </div>
            @endif


            <form wire:submit.prevent="searchTicket">
                <div class="mb-3">
                    <label for="searchTicket" class="form-label">Nomor Tiket</label>
                    <input
                        type="text"
                        id="searchTicket"
                        class="form-control @error('searchTicket') is-invalid @enderror"
                        wire:model="searchTicket"
                        placeholder="Masukkan nomor tiket">
                    @error('searchTicket')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Cari Tiket
                    </button>
                </div>
            </form>

            @if($searchResult)
            <div class="mt-4">
                <h5>Detail Tiket:</h5>
                <table class="table">
                    <tr>
                        <td class="fw-bold" width="30%">Nomor Tiket</td>
                        <td>{{ $searchResult->ticket }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Nama</td>
                        <td>{{ $searchResult->nama }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Email</td>
                        <td>{{ $searchResult->email }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Judul</td>
                        <td>{{ $searchResult->judul }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Kategori</td>
                        <td>{{ $searchResult->kategori }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status</td>
                        <td>{{ $searchResult->status }}</td>
                    </tr>
                </table>
            </div>
            @endif

        </div>
    </div>
    @endif
</div>

@push('scripts')
{{-- Include Turnstile Service --}}
<script src="{{ asset('js/turnstile-service.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const SITE_KEY = '{{ $siteKey ?? "" }}';
        
        // Initialize Turnstile
        TurnstileService.init(SITE_KEY, '#turnstile-container', {
            callback: function(token) {
                if (window.Livewire) {
                    const el = document.querySelector('[wire\\:id]');
                    if (el) {
                        window.Livewire.find(el.getAttribute('wire:id'))
                            .set('turnstileResponse', token);
                    }
                }
            }
        });

        // Handle Livewire updates
        document.addEventListener('livewire:updated', function(event) {
            const container = document.querySelector('#turnstile-container');
            
            if (container) {
                // Clean existing widget first
                TurnstileService.cleanup('#turnstile-container');
                
                // Re-initialize after cleanup
                setTimeout(() => {
                    TurnstileService.init(SITE_KEY, '#turnstile-container', {
                        callback: function(token) {
                            if (window.Livewire) {
                                const el = document.querySelector('[wire\\:id]');
                                if (el) {
                                    window.Livewire.find(el.getAttribute('wire:id'))
                                        .set('turnstileResponse', token);
                                }
                            }
                        }
                    });
                }, 200);
            }
        });
    });
</script>
@endpush
@push('styles')
<style>
    .nav-tabs .nav-link {
        border: none;
        color: #f13316;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active {
        color: #0088cc;
        border-bottom: 2px solid #0088cc;
        background: none;
    }

    .badge {
        padding: 0.5em 1em;
    }

    .bg-warning {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    .bg-info {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
    }

    .bg-success {
        background-color: #d4edda !important;
        color: #155724 !important;
    }

    .bg-danger {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }

    .timeline {
        position: relative;
        padding: 1rem 0;
    }

    .timeline-item {
        position: relative;
        padding-left: 3rem;
        margin-bottom: 2rem;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-item-connected::before {
        content: '';
        position: absolute;
        left: 1.25rem;
        top: 2.5rem;
        bottom: -2rem;
        width: 2px;
        background: #e9ecef;
    }

    .timeline-marker {
        position: absolute;
        left: 0;
        top: 0;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        text-align: center;
        line-height: 2.5rem;
        color: white;
        z-index: 1;
    }

    .timeline-marker i {
        font-size: 1rem;
    }

    .timeline-content {
        padding: 0.5rem 1rem;
        background: white;
        border-radius: 0.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    }

    /* Status Colors */
    .bg-warning .fas {
        color: #856404;
    }

    .bg-info .fas {
        color: #0c5460;
    }

    .bg-success .fas {
        color: #155724;
    }

    .bg-danger .fas {
        color: #721c24;
    }
</style>
@endpush
