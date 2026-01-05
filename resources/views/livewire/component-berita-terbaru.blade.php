<div class="px-3 mt-4">
    <h3 class="text-color-primary text-capitalize font-weight-bold text-5 m-0 mb-3">Berita/Artikel Terbaru</h3>
    <div class="pb-2 mb-1">
        @foreach($recentPosts as $recent)
            <a href="{{ route('post.show', $recent->slug) }}"
               class="text-color-default text-uppercase text-1 mb-0 d-block text-decoration-none">
                {{ $recent->published_at->format('d M Y') }}
                <span class="opacity-3 d-inline-block px-2">|</span>
                {{ $recent->author->name ?? 'Admin' }}
            </a>
            <a href="{{ route('post.show', $recent->slug) }}"
               class="text-color-dark text-hover-primary font-weight-bold text-3 d-block pb-3 line-height-4">
                {{ Str::words($recent->title, 10, '...') }}
            </a>
        @endforeach
    </div>
</div>
