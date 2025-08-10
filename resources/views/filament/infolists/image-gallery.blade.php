{{-- File: resources/views/filament/infolists/image-gallery.blade.php --}}

<div class="space-y-4">
    @if (!empty($imageUrls))
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($imageUrls as $index => $imageUrl)
                <div class="relative group">
                    <img 
                        src="{{ $imageUrl }}" 
                        alt="Foto Kegiatan {{ $index + 1 }}"
                        class="w-full h-32 object-cover rounded-lg shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                        onclick="openImageModal('{{ $imageUrl }}', '{{ $downloadUrls[$index] ?? '#' }}')"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <a 
                                href="{{ $downloadUrls[$index] ?? '#' }}" 
                                class="inline-flex items-center px-3 py-1.5 bg-white rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50"
                                download
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M7 7h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2z" />
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 text-sm">Tidak ada foto yang diupload.</p>
    @endif
</div>

<script>
function openImageModal(imageUrl, downloadUrl) {
    // Create modal for full-size image viewing
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
    modal.innerHTML = `
        <div class="max-w-4xl max-h-full p-4">
            <div class="relative">
                <img src="${imageUrl}" class="max-w-full max-h-full object-contain rounded-lg">
                <button onclick="this.closest('.fixed').remove()" class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full p-2 hover:bg-opacity-75">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <a href="${downloadUrl}" download class="absolute bottom-2 right-2 text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md">
                    Download
                </a>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}
</script>