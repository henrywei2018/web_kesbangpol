<x-layouts.public-panel :pageTitle="'Dashboard'" :pageSubtitle="'Kelola permohonan dan keberatan informasi publik Anda'">
    <div class="space-y-6">
        <!-- Welcome Card -->
        <div class="card-theme card-theme-gradient p-6 border-l-4 border-theme-primary">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    @if(auth()->user()->getFilamentAvatarUrl())
                        <img src="{{ auth()->user()->getFilamentAvatarUrl() }}" 
                             alt="{{ auth()->user()->getFilamentName() }}" 
                             class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-lg">
                    @else
                        <div class="w-16 h-16 bg-theme-primary rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white font-bold text-xl">
                                {{ strtoupper(substr(auth()->user()->firstname ?? auth()->user()->username, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Selamat Datang, {{ auth()->user()->firstname ?? auth()->user()->username }}!
                    </h2>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">
                        Portal layanan publik untuk mengajukan permohonan dan melacak status pengajuan Anda.
                    </p>
                    <div class="flex items-center mt-3 text-sm text-gray-500 dark:text-gray-400">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                        Terakhir login: {{ auth()->user()->last_login_at?->format('d M Y, H:i') ?? 'Belum pernah' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-theme">
            <!-- Total Permohonan -->
            <div class="stat-theme-card card-theme card-theme-hover p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-theme-label">Total Permohonan</p>
                        <p class="stat-theme-value">{{ $stats['total_permohonan'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            @if($stats['total_permohonan'] > 0)
                                {{ $stats['permohonan_disetujui'] }} disetujui
                            @else
                                Belum ada permohonan
                            @endif
                        </p>
                    </div>
                    <div class="bg-theme-primary-light p-3 rounded-full">
                        <i data-lucide="file-text" class="w-6 h-6 text-theme-primary"></i>
                    </div>
                </div>
            </div>

            <!-- Permohonan Disetujui -->
            <div class="stat-theme-card card-theme card-theme-hover p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-theme-label">Disetujui</p>
                        <p class="stat-theme-value text-theme-success">{{ $stats['permohonan_disetujui'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            @if($stats['total_permohonan'] > 0)
                                {{ round(($stats['permohonan_disetujui'] / $stats['total_permohonan']) * 100) }}% tingkat persetujuan
                            @else
                                0% tingkat persetujuan
                            @endif
                        </p>
                    </div>
                    <div class="bg-theme-success-light p-3 rounded-full">
                        <i data-lucide="check-circle" class="w-6 h-6 text-theme-success"></i>
                    </div>
                </div>
            </div>

            <!-- Dalam Proses -->
            <div class="stat-theme-card card-theme card-theme-hover p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-theme-label">Dalam Proses</p>
                        <p class="stat-theme-value text-theme-warning">{{ $stats['permohonan_diproses'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Menunggu review
                        </p>
                    </div>
                    <div class="bg-theme-warning-light p-3 rounded-full">
                        <i data-lucide="clock" class="w-6 h-6 text-theme-warning"></i>
                    </div>
                </div>
            </div>

            <!-- Keberatan Aktif -->
            <div class="stat-theme-card card-theme card-theme-hover p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="stat-theme-label">Keberatan Aktif</p>
                        <p class="stat-theme-value text-theme-info">{{ $stats['keberatan_aktif'] }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Total keberatan: {{ $stats['total_keberatan'] }}
                        </p>
                    </div>
                    <div class="bg-theme-info-light p-3 rounded-full">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-theme-info"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activity -->
            <div class="card-theme">
                <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">5 terakhir</span>
                    </div>
                </div>
                <div class="p-6">
                    @if($recent_activities->count() > 0)
                        <div class="space-y-4">
                            @foreach($recent_activities as $activity)
                                <div class="flex items-start space-x-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                                    <div class="flex-shrink-0 w-2 h-2 rounded-full mt-2
                                        @if($activity['status'] === 'Selesai') bg-theme-success
                                        @elseif($activity['status'] === 'Ditolak') bg-theme-danger
                                        @elseif($activity['status'] === 'Diproses') bg-theme-warning
                                        @else bg-theme-info
                                        @endif">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $activity['title'] }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                            {{ $activity['description'] }}
                                        </p>
                                        <div class="flex items-center justify-between mt-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    @if($activity['status'] === 'Selesai') bg-theme-success-light text-theme-success
                                                    @elseif($activity['status'] === 'Ditolak') bg-theme-danger-light text-theme-danger
                                                    @elseif($activity['status'] === 'Diproses') bg-theme-warning-light text-theme-warning
                                                    @else bg-theme-info-light text-theme-info
                                                    @endif">
                                                    {{ $activity['status'] }}
                                                </span>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $activity['date']->diffForHumans() }}
                                                </p>
                                            </div>
                                            <a href="{{ $activity['url'] }}" 
                                               class="text-xs text-theme-primary hover:underline">
                                                Lihat Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i data-lucide="activity" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4"></i>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada aktivitas</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">
                                Mulai dengan mengajukan permohonan informasi
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card-theme">
                <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aksi Cepat</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <a href="{{ route('filament.public.resources.permohonan-informasi-publiks.create') }}" 
                           class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg hover:border-theme-primary hover:bg-theme-primary-light dark:hover:bg-theme-primary-light/50 transition-all group">
                            <i data-lucide="plus-circle" class="w-8 h-8 text-theme-primary mb-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white text-center">Permohonan Baru</span>
                        </a>
                        
                        <a href="{{ route('filament.public.resources.keberatan-informasi-publiks.create') }}" 
                           class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg hover:border-theme-warning hover:bg-theme-warning-light dark:hover:bg-theme-warning-light/50 transition-all group">
                            <i data-lucide="alert-circle" class="w-8 h-8 text-theme-warning mb-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white text-center">Ajukan Keberatan</span>
                        </a>
                        
                        <a href="#" 
                           class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg hover:border-theme-info hover:bg-theme-info-light dark:hover:bg-theme-info-light/50 transition-all group">
                            <i data-lucide="user-check" class="w-8 h-8 text-theme-info mb-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white text-center">Update Profil</span>
                        </a>
                        
                        <a href="panel/lapor-a-t-h-gs" 
                           class="flex flex-col items-center p-4 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg hover:border-theme-success hover:bg-theme-success-light dark:hover:bg-theme-success-light/50 transition-all group">
                            <i data-lucide="shield" class="w-8 h-8 text-theme-success mb-2 group-hover:scale-110 transition-transform"></i>
                            <span class="text-sm font-medium text-gray-900 dark:text-white text-center">Unduh Dokumen</span>
                        </a>
                    </div>
                    
                    <!-- Tips Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start space-x-3">
                            <i data-lucide="lightbulb" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0"></i>
                            <div>
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">💡 Tips</h4>
                                <p class="text-xs text-blue-800 dark:text-blue-200">
                                    Pastikan untuk melengkapi profil Anda untuk mempercepat proses permohonan informasi.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Requests Table -->
        @if($permohonan_terbaru->count() > 0)
        <div class="card-theme">
            <div class="p-6 border-b border-gray-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Permohonan Terbaru</h3>
                    <a href="{{ route('filament.public.resources.permohonan-informasi-publiks.index') }}" 
                       class="text-sm font-medium text-theme-primary hover:underline">
                        Lihat Semua
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table-theme">
                    <thead>
                        <tr>
                            <th>No. Permohonan</th>
                            <th>Informasi Diminta</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permohonan_terbaru as $permohonan)
                            <tr>
                                <td class="font-medium">#{{ $permohonan->id }}</td>
                                <td>
                                    <div class="max-w-xs">
                                        <p class="truncate font-medium">{{ $permohonan->rincian_informasi }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                            {{ $permohonan->tujuan_penggunaan_informasi }}
                                        </p>
                                    </div>
                                </td>
                                <td class="text-sm">{{ $permohonan->created_at->format('d M Y') }}</td>
                                <td>
                                    @php
                                        $status = $permohonan->latest_status ?? 'pengajuan';
                                        $badgeClass = match($status) {
                                            'disetujui' => 'badge-theme-success',
                                            'ditolak' => 'badge-theme-danger',
                                            'proses', 'review' => 'badge-theme-warning',
                                            default => 'badge-theme-info'
                                        };
                                    @endphp
                                    <span class="badge-theme {{ $badgeClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('filament.public.resources.permohonan-informasi-publiks.view', $permohonan->id) }}" 
                                           class="text-theme-primary hover:text-blue-600 text-sm">
                                            Lihat
                                        </a>
                                        @if($status === 'disetujui')
                                            <a href="#" class="text-theme-success hover:text-green-600 text-sm">
                                                Unduh
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Help & Support Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Help Section -->
            <div class="card-theme p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="help-circle" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Butuh Bantuan?</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                    Pelajari cara menggunakan portal ini dengan lebih efektif.
                </p>
                <div class="space-y-2">
                    <a href="#" class="block text-sm text-theme-primary hover:underline">
                        📖 Panduan Penggunaan
                    </a>
                    <a href="#" class="block text-sm text-theme-primary hover:underline">
                        ❓ FAQ (Pertanyaan Umum)
                    </a>
                    <a href="#" class="block text-sm text-theme-primary hover:underline">
                        📞 Hubungi Support
                    </a>
                </div>
            </div>

            <!-- System Status -->
            <div class="card-theme p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="activity" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status Sistem</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Portal Layanan</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">Online</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Database</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">Normal</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-300">Server Response</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">Fast</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                    <p class="text-xs text-green-800 dark:text-green-200">
                        ✅ Semua sistem berjalan normal. Tidak ada gangguan yang dilaporkan.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialize icons after page load
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            
            // Add loading animation for stats cards
            const statCards = document.querySelectorAll('.stat-theme-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 150);
            });
        });
    </script>
    @endpush
</x-layouts.public-panel>