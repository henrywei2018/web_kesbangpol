<section class="section border-0 bg-quaternary m-0">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col col-lg-9 text-center">

                <div class="divider divider-small divider-small-lg mt-0 text-center">
                    <hr class="bg-primary border-radius m-auto">
                </div>
                <div class="overflow-hidden mb-1">
                    <h3 class="font-weight-semi-bold text-color-grey text-uppercase positive-ls-3 text-4 line-height-2 line-height-sm-7 mb-0">What Is Happening</h3>
                </div>
                <h2 class="text-color-dark font-weight-bold text-8 pb-4 mb-0">Latest News</h2>

            </div>
        </div>

        <div class="row justify-content-center">
            @foreach($news as $item)
            <div class="col-9 col-md-6 col-lg-4 mb-4 mb-lg-0">
                <a href="{{ $item['link'] }}" class="text-decoration-none">
                    <div class="card border-0 bg-transparent">
                        <div class="card-img-top position-relative overlay overflow-hidden border-radius">
                            <div class="position-absolute bottom-10 right-0 d-flex justify-content-end w-100 py-3 px-4 z-index-3">
                                <span class="text-center bg-primary text-color-light border-radius font-weight-semibold line-height-2 px-3 py-2">
                                    <span class="position-relative text-6 z-index-2">
                                        {{ $item['date'] }}
                                        <span class="d-block text-0 positive-ls-2 px-1">{{ $item['month'] }}</span>
                                    </span>
                                </span>
                            </div>
                            <img src="{{ $item['image'] }}" class="img-fluid border-radius" alt="{{ $item['title'] }}" />
                        </div>
                        <div class="card-body py-4 px-0">
                            <span class="d-block text-color-grey font-weight-semibold positive-ls-2 text-2">{{ $item['author'] }}</span>
                            <h4 class="font-weight-bold text-5 text-color-hover-primary mb-2">{{ $item['title'] }}</h4>
                            <a href="{{ $item['link'] }}" class="read-more text-color-primary font-weight-semibold mt-0 text-2">Read More <i class="fas fa-angle-right position-relative top-1 ms-1"></i></a>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

    </div>
</section>
