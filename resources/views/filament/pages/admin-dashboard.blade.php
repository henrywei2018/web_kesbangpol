{{-- resources/views/filament/pages/admin-dashboard.blade.php --}}

<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Users --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/20 transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Total Pengguna</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($overviewStats['totalUsers']) }}</p>
                        <div class="flex items-center mt-2 text-sm text-emerald-600 dark:text-emerald-400">
                            <x-heroicon-m-arrow-trending-up class="w-4 h-4 mr-1" />
                            <span>{{ $overviewStats['monthlyGrowth'] }}% bulan ini</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50">
                        <x-heroicon-o-users class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </div>

            {{-- Total Permohonan --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/20 transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Permohonan Informasi</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($overviewStats['totalPermohonan']) }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $overviewStats['pendingApprovals'] }} menunggu persetujuan</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50">
                        <x-heroicon-o-document-text class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
            </div>

            {{-- Total SKT --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/20 transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">SKT Terdaftar</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($overviewStats['totalSKT']) }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Surat Keterangan Terdaftar</p>
                    </div>
                    <div class="p-3 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800/50">
                        <x-heroicon-o-shield-check class="w-6 h-6 text-violet-600 dark:text-violet-400" />
                    </div>
                </div>
            </div>

            {{-- Total ATHG --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/20 transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Laporan ATHG</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($overviewStats['totalATHG']) }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ancaman, Tantangan, Hambatan, Gangguan</p>
                    </div>
                    <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/50">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Overview Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Permohonan Status --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Status Permohonan Informasi</h3>
                    <a href="{{ route('filament.admin.resources.permohonan-informasi-publiks.index') }}" 
                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                        <x-heroicon-m-arrow-top-right-on-square class="w-4 h-4 mr-1" />
                        Lihat Semua
                    </a>
                </div>
                <div class="space-y-4">
                    @foreach($permohonanStats as $status => $count)
                        <div class="flex justify-between items-center p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600/50">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium border
                                @switch($status)
                                    @case('pending')
                                        bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/50
                                        @break
                                    @case('diproses')
                                        bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800/50
                                        @break
                                    @case('selesai')
                                        bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/50
                                        @break
                                    @case('ditolak')
                                        bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800/50
                                        @break
                                    @default
                                        bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600
                                @endswitch
                            ">
                                {{ ucfirst($status) }} ({{ $count }})
                            </span>
                            <div class="w-1/3 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500
                                    @switch($status)
                                        @case('pending')
                                            bg-amber-500
                                            @break
                                        @case('diproses')
                                            bg-blue-500
                                            @break
                                        @case('selesai')
                                            bg-emerald-500
                                            @break
                                        @case('ditolak')
                                            bg-red-500
                                            @break
                                    @endswitch
                                " style="width: {{ $overviewStats['totalPermohonan'] > 0 ? ($count / $overviewStats['totalPermohonan']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- User Roles Distribution --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Distribusi Peran Pengguna</h3>
                    <a href="{{ route('filament.admin.resources.users.index') }}" 
                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/30 transition-colors">
                        <x-heroicon-m-cog-6-tooth class="w-4 h-4 mr-1" />
                        Kelola Pengguna
                    </a>
                </div>
                <div class="space-y-4">
                    @foreach($userRoleStats as $role => $count)
                        <div class="flex justify-between items-center p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600/50">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">
                                {{ str_replace('_', ' ', $role) }}
                            </span>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $count }}</span>
                                <div class="w-20 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" 
                                         style="width: {{ $overviewStats['totalUsers'] > 0 ? ($count / $overviewStats['totalUsers']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ATHG & SKT Stats Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- ATHG Categories --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Kategori Laporan ATHG</h3>
                    <a href="{{ route('filament.admin.resources.athg.index') }}" 
                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 hover:bg-amber-100 dark:hover:bg-amber-900/30 transition-colors">
                        <x-heroicon-m-chart-bar class="w-4 h-4 mr-1" />
                        Lihat Detail
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($athgStats as $category => $count)
                        <div class="text-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">{{ $count }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $category }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- SKT Status --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Status SKT</h3>
                    <a href="{{ route('filament.admin.resources.reviu-skt.index') }}" 
                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800/50 hover:bg-violet-100 dark:hover:bg-violet-900/30 transition-colors">
                        <x-heroicon-m-document-check class="w-4 h-4 mr-1" />
                        Kelola SKT
                    </a>
                </div>
                <div class="space-y-3">
                    @foreach($sktStats as $status => $count)
                        <div class="flex justify-between items-center p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600/50">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $status }}</span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium
                                @switch($status)
                                    @case('pengajuan')
                                        bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-700
                                        @break
                                    @case('verifikasi')
                                        bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-700
                                        @break
                                    @case('selesai')
                                        bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700
                                        @break
                                    @case('ditolak')
                                        bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-700
                                        @break
                                @endswitch
                            ">
                                {{ $count }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Performance Metrics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/20 transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rata-rata Waktu Proses</h3>
                    <div class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/50">
                        <x-heroicon-o-clock class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $performanceMetrics['avgProcessingTime'] }} hari</p>
                <p class="text-sm text-emerald-600 dark:text-emerald-400 flex items-center">
                    <x-heroicon-m-arrow-down class="w-4 h-4 mr-1" />
                    12% lebih cepat dari bulan lalu
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/20 transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Tingkat Approval</h3>
                    <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50">
                        <x-heroicon-o-check-circle class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $performanceMetrics['approvalRate'] }}%</p>
                <p class="text-sm text-emerald-600 dark:text-emerald-400 flex items-center">
                    <x-heroicon-m-arrow-up class="w-4 h-4 mr-1" />
                    2.3% dari bulan lalu
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg dark:hover:shadow-gray-900/20 transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-600">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Kepuasan Pengguna</h3>
                    <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/50">
                        <x-heroicon-o-star class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $performanceMetrics['userSatisfaction'] }}/5</p>
                <p class="text-sm text-emerald-600 dark:text-emerald-400 flex items-center">
                    <x-heroicon-m-arrow-up class="w-4 h-4 mr-1" />
                    0.2 dari bulan lalu
                </p>
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Aktivitas Terkini</h3>
                <button class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <x-heroicon-m-arrow-path class="w-4 h-4 mr-1" />
                    Lihat Semua
                </button>
            </div>
            <div class="space-y-3">
                @forelse($recentActivities as $activity)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600/50 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg border flex items-center justify-center
                                @switch($activity['type'])
                                    @case('permohonan')
                                        bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800/50
                                        @break
                                    @case('keberatan')
                                        bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800/50
                                        @break
                                    @case('skt')
                                        bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800/50
                                        @break
                                    @case('athg')
                                        bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800/50
                                        @break
                                    @default
                                        bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600
                                @endswitch
                            ">
                                @switch($activity['type'])
                                    @case('permohonan')
                                        <x-heroicon-o-document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        @break
                                    @case('keberatan')
                                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                                        @break
                                    @case('skt')
                                        <x-heroicon-o-shield-check class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                        @break
                                    @case('athg')
                                        <x-heroicon-o-shield-exclamation class="w-5 h-5 text-red-600 dark:text-red-400" />
                                        @break
                                    @default
                                        <x-heroicon-o-document-text class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                                @endswitch
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $activity['user'] }} - {{ strtoupper($activity['type']) }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $activity['action'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium border
                                @switch($activity['status'])
                                    @case('pending')
                                        bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800/50
                                        @break
                                    @case('diproses')
                                        bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800/50
                                        @break
                                    @case('selesai')
                                        bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/50
                                        @break
                                    @case('ditolak')
                                        bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800/50
                                        @break
                                    @default
                                        bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600
                                @endswitch
                            ">
                                {{ ucfirst($activity['status']) }}
                            </span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                        <x-heroicon-o-inbox class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-500" />
                        <p class="text-gray-500 dark:text-gray-400">Belum ada aktivitas terkini</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-6">Aksi Cepat</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('filament.admin.resources.users.create') }}" 
                   class="group flex items-center justify-center p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-blue-400 dark:hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all duration-200">
                    <x-heroicon-o-plus class="w-5 h-5 text-gray-400 group-hover:text-blue-500 mr-2 transition-colors" />
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Tambah Pengguna</span>
                </a>
                
                <a href="{{ route('filament.admin.resources.permohonan-informasi-publiks.index') }}" 
                   class="group flex items-center justify-center p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-emerald-400 dark:hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-all duration-200">
                    <x-heroicon-o-document-text class="w-5 h-5 text-gray-400 group-hover:text-emerald-500 mr-2 transition-colors" />
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Kelola Permohonan</span>
                </a>
                
                <a href="{{ route('filament.admin.pages.manage-whats-app') }}" 
                   class="group flex items-center justify-center p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-violet-400 dark:hover:border-violet-500 hover:bg-violet-50 dark:hover:bg-violet-900/10 transition-all duration-200">
                    <x-heroicon-o-cog-6-tooth class="w-5 h-5 text-gray-400 group-hover:text-violet-500 mr-2 transition-colors" />
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">Konfigurasi</span>
                </a>
                
                <button class="group flex items-center justify-center p-4 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/10 transition-all duration-200">
                    <x-heroicon-o-chart-bar class="w-5 h-5 text-gray-400 group-hover:text-amber-500 mr-2 transition-colors" />
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Laporan</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Custom Styles for Enhanced Dark Mode --}}
    <style>
        /* Smooth transitions for dark mode toggle */
        * {
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }

        /* Enhanced hover effects */
        .hover-lift:hover {
            transform: translateY(-2px);
        }

        /* Progress bar animations */
        .progress-bar {
            animation: progressLoad 1s ease-out;
        }

        @keyframes progressLoad {
            from { width: 0%; }
        }

        /* Custom scrollbar for dark mode */
        .dark ::-webkit-scrollbar {
            width: 6px;
        }

        .dark ::-webkit-scrollbar-track {
            background: #374151;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #6b7280;
            border-radius: 3px;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }

        /* Improved focus states */
        .focus\:ring-brand:focus {
            --tw-ring-color: rgb(59 130 246 / 0.5);
        }

        /* Subtle shadow improvements */
        .card-shadow {
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        }

        .dark .card-shadow {
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.3), 0 1px 2px -1px rgb(0 0 0 / 0.3);
        }

        .card-shadow:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        .dark .card-shadow:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.4), 0 4px 6px -4px rgb(0 0 0 / 0.4);
        }
    </style>

    {{-- JavaScript for enhanced interactions --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh dashboard every 5 minutes (300000ms)
            const autoRefresh = setInterval(function() {
                // Only refresh if the page is visible to avoid unnecessary requests
                if (!document.hidden) {
                    window.location.reload();
                }
            }, 300000);

            // Pause auto-refresh when page is hidden
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    clearInterval(autoRefresh);
                } else {
                    // Restart auto-refresh when page becomes visible
                    setInterval(function() {
                        if (!document.hidden) {
                            window.location.reload();
                        }
                    }, 300000);
                }
            });

            // Enhanced hover effects for cards
            const cards = document.querySelectorAll('[class*="hover:shadow-lg"], [class*="hover:border-gray-300"]');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                    this.classList.add('card-shadow');
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.classList.remove('card-shadow');
                });
            });

            // Progress bar animation
            const progressBars = document.querySelectorAll('[style*="width:"]');
            progressBars.forEach(bar => {
                bar.classList.add('progress-bar');
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Add loading states for action buttons
            const actionButtons = document.querySelectorAll('a[href*="create"], a[href*="index"]');
            actionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('svg');
                    if (icon) {
                        icon.classList.add('animate-spin');
                    }
                    
                    // Remove animation after navigation starts
                    setTimeout(() => {
                        if (icon) {
                            icon.classList.remove('animate-spin');
                        }
                    }, 500);
                });
            });

            // Enhanced tooltips (if needed)
            const tooltipElements = document.querySelectorAll('[title]');
            tooltipElements.forEach(element => {
                element.addEventListener('mouseenter', function() {
                    const title = this.getAttribute('title');
                    if (title) {
                        // Create custom tooltip
                        const tooltip = document.createElement('div');
                        tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 dark:bg-gray-700 rounded shadow-lg';
                        tooltip.textContent = title;
                        
                        // Position tooltip
                        const rect = this.getBoundingClientRect();
                        tooltip.style.top = `${rect.top - 30}px`;
                        tooltip.style.left = `${rect.left + rect.width / 2}px`;
                        tooltip.style.transform = 'translateX(-50%)';
                        
                        document.body.appendChild(tooltip);
                        
                        // Remove title to prevent default tooltip
                        this.removeAttribute('title');
                        this.dataset.originalTitle = title;
                    }
                });
                
                element.addEventListener('mouseleave', function() {
                    // Remove custom tooltip
                    const tooltip = document.querySelector('.absolute.z-50');
                    if (tooltip) {
                        tooltip.remove();
                    }
                    
                    // Restore original title
                    if (this.dataset.originalTitle) {
                        this.setAttribute('title', this.dataset.originalTitle);
                        delete this.dataset.originalTitle;
                    }
                });
            });

            // Keyboard navigation improvements
            document.addEventListener('keydown', function(e) {
                // Add keyboard shortcuts for common actions
                if (e.ctrlKey || e.metaKey) {
                    switch(e.key) {
                        case 'r':
                            e.preventDefault();
                            window.location.reload();
                            break;
                        case 'h':
                            e.preventDefault();
                            window.location.href = '/admin';
                            break;
                    }
                }
            });

            // Performance monitoring
            if ('performance' in window) {
                window.addEventListener('load', function() {
                    const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                    if (loadTime > 3000) {
                        console.warn('Dashboard load time is slow:', loadTime + 'ms');
                    }
                });
            }

            // Error handling for failed AJAX requests (if you add them later)
            window.addEventListener('unhandledrejection', function(event) {
                console.error('Dashboard error:', event.reason);
                // You could show a user-friendly error message here
            });
        });

        // Utility function for number formatting
        function formatNumber(num) {
            if (num >= 1000000) {
                return (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return (num / 1000).toFixed(1) + 'K';
            }
            return num.toString();
        }

        // Function to update dashboard data (for future AJAX implementation)
        async function updateDashboardData() {
            try {
                // This would be implemented when you add real-time updates
                const response = await fetch('/admin/dashboard/data');
                const data = await response.json();
                
                // Update specific elements with new data
                // This is where you'd update counters, charts, etc.
                
            } catch (error) {
                console.error('Failed to update dashboard data:', error);
            }
        }
    </script>
</x-filament-panels::page>