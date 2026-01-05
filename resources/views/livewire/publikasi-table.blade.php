<div id="examples" class="container py-2">
    <div class="row">
        <div class="col-md-12 col-lg-12 mb-5 mb-lg-0 appear-animation animated fadeInUpShorter appear-animation-visible"
            data-appear-animation="fadeInUpShorter" data-appear-animation-delay="600" style="animation-delay: 600ms;">
            <h4 class="mb-4">Kumpulan Publikasi</h4>

            <div class="card card-border card-border-top bg-color-light"
                style="box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                <!-- Added custom box-shadow -->
                <div class="card-body">
                    <input type="text" wire:model.live="search" placeholder="Ketikan Pencarian Anda"
                        class="form-control mb-3" />

                    <table class="table table-hover" cellpadding="0" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th width="5%">#</th> <!-- Added for the indexing number -->
                                <th width="5%"></th> <!-- Added for the PDF icon -->
                                <th width="20%">Judul</th>
                                <th width="45%">Deskripsi</th>
                                <th width="15%">Tanggal</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($publikasi as $item)
                            <tr>   
                            <td>{{ $loop->iteration }}</td> <!-- Indexing number for each row -->
                        <td>
                            @if($item->hasMedia('publikasi'))
                                <i class="fas fa-file-pdf text-danger"></i> <!-- PDF icon -->
                            @else
                                <span class="text-muted">No PDF</span>
                            @endif
                        </td>
                                <td>{{ $item->judul }}</td>
                                <td>{{ $item->deskripsi }}</td>
                                <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                <td>
                                    @if($item->hasMedia('publikasi'))
                                    <a href="{{ $item->getFirstMediaUrl('publikasi') }}" target="_blank"
                                        class="btn btn-sm btn-primary" data-toggle="tooltip" data-placement="top"
                                        title="Download {{ $item->judul }}">
                                        Download
                                    </a>
                                    @else
                                    <span class="text-muted">No File</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div>
                        {{ $publikasi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Tooltip Script -->
    <script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
    </script>
</div>