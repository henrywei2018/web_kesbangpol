{{-- resources/views/filament/modals/image-gallery.blade.php --}}
<div class="space-y-4">
    @if(!empty($images))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($images as $index => $imageUrl)
                <div class="relative group">
                    <img 
                        src="{{ $imageUrl }}" 
                        alt="Foto Kegiatan {{ $index + 1 }}"
                        class="w-full h-48 object-cover rounded-lg shadow-md cursor-pointer hover:shadow-lg transition-shadow duration-200"
                        onclick="openImageModal('{{ $imageUrl }}', '{{ $index + 1 }}')"
                    >
                    <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-20 transition-all duration-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity duration-200" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm5 2a2 2 0 11-4 0 2 2 0 014 0zm-2 1a1 1 0 100-2 1 1 0 000 2zm5.5 2.5a.5.5 0 11-1 0 .5.5 0 011 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                        Foto {{ $index + 1 }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-sm text-gray-500 text-center">
            <p>{{ count($images) }} foto tersedia. Klik pada gambar untuk memperbesar.</p>
        </div>

        {{-- Download All Images Button --}}
        <div class="flex justify-center space-x-4 pt-4 border-t">
            <a href="{{ route('lapor-giat.download-all-images', ['laporGiat' => $record->id]) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                Download Semua Foto (ZIP)
            </a>
        </div>
    @else
        <div class="text-center text-gray-500 py-8">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
            </svg>
            <p>Tidak ada foto kegiatan yang tersedia</p>
        </div>
    @endif
</div>

{{-- Full Screen Image Modal --}}
<div id="fullImageModal" class="fixed inset-0 bg-black bg-opacity-90 z-[100] hidden items-center justify-center">
    <div class="relative max-w-screen-lg max-h-screen mx-4">
        <button onclick="closeImageModal()" 
                class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
        </button>
        <img id="fullImage" 
             src="" 
             alt="" 
             class="max-w-full max-h-full object-contain rounded-lg">
        <div id="fullImageCaption" class="absolute bottom-4 left-4 bg-black bg-opacity-75 text-white px-3 py-1 rounded"></div>
    </div>
</div>

<script>
function openImageModal(imageUrl, caption) {
    const modal = document.getElementById('fullImageModal');
    const image = document.getElementById('fullImage');
    const captionEl = document.getElementById('fullImageCaption');
    
    image.src = imageUrl;
    image.alt = 'Foto Kegiatan ' + caption;
    captionEl.textContent = 'Foto Kegiatan ' + caption;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('fullImageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside the image
document.getElementById('fullImageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>

<style>
    #fullImageModal {
        z-index: 999999;
    }
</style>