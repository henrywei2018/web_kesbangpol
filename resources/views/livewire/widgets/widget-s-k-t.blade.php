<div class="col-span-4 gap-6 shadow rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark lg:col-span-6 2xl:col-span-3">
    <div class="col-span-6 border-b border-stroke px-3 py-1 dark:border-strokedark md:px-5 md:py-5 xl:px-5.5">
        <div class="flex items-start justify-between">
            <h6 class="grow text-15">Rekapitulasi SKT</h6>
        </div>

        <div>
            <div class="flex items-center justify-between mt-5 mb-2">
                <p class="text-slate-500 dark:text-zink-200">SKT Selesai : {{ $sktSelesai }}</p>
            </div>
            <div class="w-full bg-gray-200 rounded-full dark:bg-gray-700">
                <div class="bg-primary-600 text-xs font-medium text-white text-center p-0.5 leading-none rounded-full" style="width: {{ $completionPercentage }}%"> {{ $completionPercentage }}%
                </div>
            </div>
            <div class="grid mt-3 xl:grid-cols-2">
                <div class="flex items-center gap-2">
                    <div class="shrink-0">
                        <i data-lucide="calendar-days" class="inline-block mb-1 align-middle size-4"></i>
                    </div>
                    <p class="mb-0 text-slate-500 dark:text-zink-200">Perlu tindak lanjut: <span class="font-medium text-slate-800 dark:text-zink-50">{{ $sktProses->count() }}</span></p>
                </div>
            </div>
        </div>
        <h6 class="mt-4 mb-3">Daftar Permohonan SKT</h6>
        <ul class="divide-y divide-slate-200 dark:divide-zink-500">
            @foreach ($sktProses as $skt)
            <li class="flex items-center gap-3 py-2 first:pt-0 last:pb-0">
                <div class="w-8 h-8 rounded-full shrink-0 bg-slate-100 dark:bg-zink-600">
                    <img src="{{ url($skt->getAvatarUrl()) }}" alt="{{ $skt->user->username ?? 'Default Avatar' }}" class="w-8 h-8 rounded-full">
                </div>
                <div class="grow">
                    <h6 class="font-small">{{ $skt->nama_ormas }}</h6>
                    <p class="text-slate-500 dark:text-zink-100">{{ $skt->email }}</p>
                </div>
                <div class="relative shrink-0 group">
                    <a
                        href="{{ url('/admin/s-k-t-document-feedbacks/' . $skt->id . '/edit') }}"
                        class="relative flex items-center justify-center w-8 h-8 text-white rounded-full animate-pulse"
                        style="background-color: #FACC15; transition: background-color 0.3s;"
                        onmouseover="this.style.backgroundColor='#FBBF24';"
                        onmouseout="this.style.backgroundColor='#FACC15';">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 17l4-4m0 0l-4-4m4 4H5" />
                        </svg>
                    </a>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
