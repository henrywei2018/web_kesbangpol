<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-signal class="w-5 h-5" />
                <span>Status Sistem</span>
            </div>
        </x-slot>

        <div class="space-y-4">
            <!-- Portal Status -->
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Portal Layanan</span>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span class="text-sm text-green-600 dark:text-green-400 font-medium">Online</span>
                </div>
            </div>
            
            <!-- Database Status -->
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Database</span>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 {{ $database_status['status'] === 'online' ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></div>
                    <span class="text-sm {{ $database_status['status'] === 'online' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }} font-medium">
                        {{ $database_status['message'] }}
                    </span>
                </div>
            </div>
            
            <!-- Response Time -->
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Response Time</span>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 {{ $response_time === 'Fast' ? 'bg-green-500' : ($response_time === 'Normal' ? 'bg-yellow-500' : 'bg-red-500') }} rounded-full"></div>
                    <span class="text-sm {{ $response_time === 'Fast' ? 'text-green-600 dark:text-green-400' : ($response_time === 'Normal' ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }} font-medium">
                        {{ $response_time }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Status Message -->
        <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 dark:text-green-400" />
                <p class="text-xs text-green-800 dark:text-green-200">
                    Semua sistem berjalan normal. Tidak ada gangguan yang dilaporkan.
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>