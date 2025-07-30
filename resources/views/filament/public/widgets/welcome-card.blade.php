<x-filament-widgets::widget>
    <div class="fi-wi-welcome-card relative overflow-hidden bg-gradient-to-r from-red-600 to-amber-700 dark:from-red-700 dark:to-amber-800 rounded-xl p-6 text-white">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" fill="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
        
        <div class="relative flex items-center space-x-4">
            <div class="flex-shrink-0">
                @if($user->getFilamentAvatarUrl())
                    <img src="{{ $user->getFilamentAvatarUrl() }}" 
                         alt="{{ $user->getFilamentName() }}" 
                         class="w-16 h-16 rounded-full object-cover border-2 border-white/30 shadow-lg">
                @else
                    <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-xl">
                            {{ strtoupper(substr($user->firstname ?? $user->username, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </div>
            
            <div class="flex-1">
                <h2 class="text-2xl font-bold mb-1">
                    {{ $greeting }}, {{ $user->firstname ?? $user->username }}!
                </h2>
                <p class="text-blue-100 mb-3">
                    Portal layanan publik untuk mengajukan permohonan dan melacak status pengajuan Anda.
                </p>
                <div class="flex items-center text-sm text-blue-200">
                    <x-heroicon-o-calendar class="w-4 h-4 mr-2" />
                    Terakhir login: {{ $user->last_login_at?->format('d M Y, H:i') ?? 'Pertama kali login' }}
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>