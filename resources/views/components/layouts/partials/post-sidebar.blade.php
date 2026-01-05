<div class="blog-sidebar col-lg-4 pt-4 pt-lg-0 appear-animation" data-appear-animation="fadeInUpShorter"
     data-appear-animation-delay="300">
    <aside class="sidebar">
        {{-- About the Blog --}}
        <div class="px-3 mb-4">
            <h3 class="text-color-primary text-capitalize font-weight-bold text-5 m-0 mb-3">Berita/Artikel</h3>
            <p class="m-0">Halaman ini menyajikan Berita/Artikel terkini dan terupdate secara profesional.</p>

        </div>
        <div class="py-1 clearfix">
            <hr class="my-2">
        </div>

        <livewire:component-search />
        
        <div class="py-1 clearfix">
            <hr class="my-2">
        </div>

        {{-- Berita Terbaru --}}
        <livewire:component-berita-terbaru />
        <div class="py-1 clearfix">
            <hr class="my-2">
        </div>

        {{-- Categories --}}
        <livewire:component-berita-category />
    </aside>
</div>
