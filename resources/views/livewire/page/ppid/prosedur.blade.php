<div class="row">
    <div class="col mx-2">
        <h4 class="mb-2 mt-3"><strong>Prosedur Layanan</strong></h4>

        <div class="process process-vertical py-1">
            @php
            $steps = [
            [
            'title' => 'Registrasi',
            'content' => 'Melakukan registrasi pada halaman berikut <a href="' . url('/admin/register') . '">Link</a> dengan mengisi seluruh form yang telah tersedia.',
            'delay' => 200
            ],
            [
            'title' => 'Verifikasi Email',
            'content' => 'Melakukan verifikasi pendaftaran akun melalui tautan yang dikirimkan melalui email (dapat melakukan pengiriman ulang link dengan masuk menggunakan data pendaftaran pada tahap sebelumnya pada tautan berikut <a href="' . url('/admin/login') . '">Link</a> ).',
            'delay' => 400
            ],
            [
            'title' => 'Lengkapi Data Diri',
            'content' => 'Setelah berhasil masuk silahkan melengkapi profil pengguna sebelum menggunakan layanan.',
            'delay' => 600
            ],
            [
            'title' => 'Penggunaan Layanan',
            'content' => 'Silahkan memilih layanan sesuai kebutuhan dan melengkapi seluruh persyaratan yang dibutuhkan.',
            'delay' => 800
            ],
            [
            'title' => 'Legalisasi',
            'content' => 'Jika layanan telah selesai, silahkan cetak bukti layanan dan melaporkan pada pihak penyelenggara layanan.',
            'delay' => 1000
            ]
            ];
            @endphp

            @foreach($steps as $index => $step)
            <div class="process-step appear-animation animated fadeInUpShorter appear-animation-visible"
                data-appear-animation="fadeInUpShorter"
                data-appear-animation-delay="{{ $step['delay'] }}"
                style="animation-delay: {{ $step['delay'] }}ms">
                <div class="process-step-circle">
                    <strong class="process-step-circle-content">{{ $index + 1 }}</strong>
                </div>
                <div class="process-step-content">
                    <h4 class="mb-1 text-4 font-weight-bold">{{ $step['title'] }}</h4>
                    <p class="mb-0">{!! $step['content'] !!}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>