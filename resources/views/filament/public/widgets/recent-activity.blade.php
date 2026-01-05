<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Aktivitas Terbaru
        </x-slot>

        <div class="space-y-4">
            @forelse($activities as $activity)
                <div class="flex items-start space-x-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex-shrink-0">
                        @if($activity['type'] === 'permohonan')
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <x-heroicon-o-document-text class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                        @else
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400" />
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $activity['title'] }}
                            </h4>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $activity['date']->diffForHumans() }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $activity['description'] }}
                        </p>
                        
                        <div class="flex items-center justify-between mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($activity['status'] === 'Selesai') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($activity['status'] === 'Ditolak') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($activity['status'] === 'Diproses') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @endif">
                                {{ $activity['status'] }}
                            </span>
                            
                            <a href="{{ $activity['url'] }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                Lihat Detail →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <x-heroicon-o-document-text class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Belum ada aktivitas</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Mulai dengan mengajukan permohonan informasi.
                    </p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>