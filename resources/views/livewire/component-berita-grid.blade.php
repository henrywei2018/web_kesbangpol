            <div class="col-lg-8 mb-5 mb-lg-0">
                       
                        <div class="row justify-content-center appear-animation " data-appear-animation="fadeIn"
                            data-appear-animation-delay="300">
                            @foreach ($records as $record)
                            <div class="col-md-6 mb-3">
                                <!-- Reduced bottom margin for more compact layout -->
                                <a href="{{ route('post.show', $record->slug) }}" class="text-decoration-none">
                                    <div class="card card-border card-border-bottom card-border-hover bg-color-light box-shadow-6 box-shadow-hover anim-hover-translate-top-10px transition-3ms"
                                        style="overflow: hidden;">
                                        <div class="card-img-top position-relative"
                                            style="overflow: hidden; padding: 0;">
                                            <!-- Removed padding for full image width -->
                                            <div class="p-absolute rounded-3 top-0 left-0 d-flex justify-content-end py-2 px-3 z-index-3"
                                                style="position: absolute; z-index: 3;">
                                                <!-- Removed unnecessary padding for the date badge -->
                                                <span
                                                    class="text-center bg-primary text-color-light font-weight-semibold line-height-2 px-2 py-1">
                                                    <!-- Reduced padding inside the date badge -->
                                                    <span class="position-relative text-4 z-index-2">
                                                        {{ $record->published_at->format('d') }}
                                                        <span
                                                            class="d-block text-0 positive-ls-2">{{ strtoupper($record->published_at->format('M')) }}</span>
                                                    </span>
                                                </span>
                                            </div>
                                            <img src="{{ $record->image ?? asset('img/default-blog-image.jpg') }}"
                                                class="img-fluid w-100"
                                                style="object-fit: cover; height: 200px; border-top-left-radius: inherit; border-top-right-radius: inherit;"
                                                alt="{{ $record->title }}" />
                                            <!-- Full width image with object-fit for proper scaling -->
                                        </div>
                                        <div class="card-body py-3 px-2">
                                            <span
                                                class="d-block text-color-grey font-weight-semibold positive-ls-2 text-2 mb-1">
                                                {{ $record->category->name ?? 'Admin' }}</span>
                                            <h4
                                                class="font-weight-bold text-4 custom-primary-font custom-primary-font text-color-hover-primary mb-1">
                                                {{ Str::words($record->title, 12, '...') }}</h4>
                                            <p class="card-text mb-2">{{ Str::limit($record->excerpt, 80) }}</p>
                                            <a href="{{ route('post.show', $record->slug) }}"
                                                class="read-more text-color-primary font-weight-semibold text-2">Read
                                                More <i class="fas fa-angle-right position-relative top-1 ms-1"></i></a>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endforeach
    


                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-center">
                            {{ $records->links() }}
                        </div>
                    </div>