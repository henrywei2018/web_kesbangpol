@php
    $widgets = $this->getHeaderWidgets();
@endphp

<x-filament-panels::page>
    @if ($widgets)
        <div class="space-y-6">
            {{-- Introduction Section --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                            <x-heroicon-o-building-office-2 class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            Tentang Data ORMAS
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Data ini menampilkan informasi statistik Organisasi Kemasyarakatan (ORMAS) yang terdaftar 
                            di Provinsi Kalimantan Utara. Data diperbarui secara otomatis berdasarkan proses registrasi 
                            melalui Surat Keterangan Terdaftar (SKT) dan Surat Keterangan Laporan (SKL).
                        </p>
                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Terdaftar Lengkap: Telah menyelesaikan seluruh proses administrasi
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                Dalam Proses: Masih dalam tahap penyelesaian dokumen
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Additional Information Section --}}
            <div class="bg-gray-50 rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Informasi Tambahan
                </h3>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">
                            <x-heroicon-m-document-text class="w-5 h-5 inline mr-2 text-blue-600" />
                            Surat Keterangan Terdaftar (SKT)
                        </h4>
                        <p class="text-sm text-gray-600">
                            Proses registrasi ORMAS yang memerlukan verifikasi dokumen pendirian, 
                            struktur organisasi, dan kelengkapan administrasi lainnya.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">
                            <x-heroicon-m-clipboard-document-list class="w-5 h-5 inline mr-2 text-green-600" />
                            Surat Keterangan Laporan (SKL)
                        </h4>
                        <p class="text-sm text-gray-600">
                            Proses pelaporan kegiatan ORMAS yang menunjukkan aktivitas dan 
                            kontribusi organisasi kepada masyarakat.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="bg-blue-50 rounded-lg border border-blue-200 p-6">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-information-circle class="w-6 h-6 text-blue-600" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">
                            Butuh Bantuan?
                        </h3>
                        <p class="text-blue-800 mb-3">
                            Untuk informasi lebih lanjut mengenai registrasi ORMAS atau bantuan terkait 
                            proses administrasi, silakan hubungi:
                        </p>
                        <div class="space-y-2 text-sm text-blue-800">
                            <div class="flex items-center space-x-2">
                                <x-heroicon-m-phone class="w-4 h-4" />
                                <span>Telp: -</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <x-heroicon-m-envelope class="w-4 h-4" />
                                <span>Email: kesbangpol@kaltaraprov.go.id</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>