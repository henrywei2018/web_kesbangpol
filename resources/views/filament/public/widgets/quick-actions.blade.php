<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Aksi Cepat
        </x-slot>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('filament.public.resources.permohonan-informasi.create') }}" 
               class="group flex flex-col items-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-plus-circle class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                    Permohonan Baru
                </span>
            </a>
            
            <a href="{{ route('filament.public.resources.keberatan-informasi.create') }}" 
               class="group flex flex-col items-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                    Ajukan Keberatan
                </span>
            </a>
            
            <a href="panel/my-profile" 
               class="group flex flex-col items-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition-all">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-user class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                    Update Profil
                </span>
            </a>
            <a href="panel/lapor-a-t-h-gs" 
               class="group flex flex-col items-center p-6 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all">
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-shield-exclamation class="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
                <span class="text-sm font-medium text-gray-900 dark:text-white text-center">
                    Lapor ATHG
                </span>
            </a>
        </div>
        
        <!-- Tips Section -->
        <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-start space-x-3">
                <x-heroicon-o-light-bulb class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
                <div>
                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-1">💡 Tips</h4>
                    <p class="text-xs text-blue-800 dark:text-blue-200">
                        Pastikan untuk melengkapi profil Anda untuk mempercepat proses permohonan informasi.
                    </p>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>