<div class="px-3 mt-4">
    <h3 class="text-color-primary text-capitalize font-weight-bold text-5 m-0">Categories</h3>
    <ul class="nav nav-list flex-column mt-2 mb-0 p-relative right-9">
        <li class="nav-item">
            <a wire:click.prevent="selectCategory('')" href="#" class="nav-link bg-transparent border-0">
                All Categories
            </a>
        </li>
        @foreach($categories as $category)
            <li class="nav-item">
                <a wire:click.prevent="selectCategory({{ $category->id }})" href="#"
                   class="nav-link bg-transparent border-0">
                    {{ $category->name }} ({{ $category->posts_count }})
                </a>
            </li>
        @endforeach
    </ul>
</div>
