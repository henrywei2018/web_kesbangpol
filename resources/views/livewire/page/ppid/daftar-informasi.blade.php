<!-- resources/views/livewire/page/ppid/daftar-informasi.blade.php -->
<div id="examples" class="container py-2">
    <div class="row">
        <div class="col-md-12 col-lg-12 mb-5 mb-lg-0 appear-animation animated fadeInUpShorter appear-animation-visible"
            data-appear-animation="fadeInUpShorter" data-appear-animation-delay="600" style="animation-delay: 600ms;">
            <h4 class="mb-4">Daftar Informasi Publik</h4>

            <div class="card card-border card-border-top bg-color-light"
                style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <div class="card-body">
                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-8 mb-3">
                            <div class="input-group">
                                <input type="text" 
                                       wire:model.live.debounce.300ms="search" 
                                       class="form-control" 
                                       placeholder="Ketikan Pencarian Anda">
                                @if($search || $selectedYear)
                                    <button class="btn btn-outline-secondary" type="button" wire:click="resetFilters">
                                        <i class="fas fa-times"></i> Reset
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <select wire:model.live="selectedYear" class="form-control">
                                <option value="">Semua Tahun</option>
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Info Section -->
                    <div class="row mb-3">
                        <div class="col">
                            <small class="text-muted">
                                Menampilkan {{ $publikasi->firstItem() ?? 0 }} - {{ $publikasi->lastItem() ?? 0 }} 
                                dari {{ $publikasi->total() }} dokumen
                            </small>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="table-responsive">
        <table class="table table-hover" cellpadding="0" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="5%"></th>
                    <th width="20%">Judul</th>
                    <th width="35%">Deskripsi</th>
                    <th width="15%">Kategori</th>
                    <th width="10%">Tanggal</th>
                    <th width="10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($publikasi as $index => $item)
                <tr>
                    <td>{{ $publikasi->firstItem() + $index }}</td>
                    <td>
                        @if($item->hasPdf())
                            <i class="fas fa-file-pdf text-danger"></i>
                        @else
                            <span class="text-muted">No PDF</span>
                        @endif
                    </td>
                    <td>{{ $item->title }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ optional($item->category)->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->publication_date)->format('d M Y') }}</td>
                    <td>
                        @if($item->hasPdf())
                            <a href="{{ $item->getPdfUrl() }}" 
                               target="_blank"
                               class="btn btn-sm btn-primary" 
                               data-toggle="tooltip" 
                               data-placement="top"
                               title="Download {{ $item->title }}">
                                Download
                            </a>
                        @else
                            <span class="text-muted">No File</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-folder-open mb-2" style="font-size: 2rem;"></i>
                            <p class="mb-0">Tidak ada informasi publik ditemukan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $publikasi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div wire:loading class="position-fixed w-100 h-100" 
         style="top: 0; left: 0; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="position-absolute top-50 start-50 translate-middle">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush