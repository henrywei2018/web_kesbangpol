@extends('components.layouts.app')

@section('content')
<section class="custom-page-header-1 page-header page-header-modern page-header-lg bg-primary border-0 z-index-1 my-0">
					<div class="custom-page-header-1-wrapper overflow-hidden">
						<div class="custom-bg-grey-1 py-5 appear-animation" data-appear-animation="maskUp" data-appear-animation-delay="800">
							<div class="container py-3 my-3">
								<div class="row">
									<div class="col-md-12 align-self-center p-static text-center">
										<div class="overflow-hidden mb-2">
											<h1 class="font-weight-black text-12 mb-0 appear-animation" data-appear-animation="maskUp" data-appear-animation-delay="1200">Galeri</h1>
										</div>
									</div>
									<div class="col-md-12 align-self-center">
										<div class="overflow-hidden">
											<ul class="custom-breadcrumb-style-1 breadcrumb breadcrumb-light custom-font-secondary d-block text-center custom-ls-1 text-5 appear-animation" data-appear-animation="maskUp" data-appear-animation-delay="1450">
												<li class="text-transform-none"><a href="demo-architecture-2.html" class="text-decoration-none">Home</a></li>
												<li class="text-transform-none active">{{ $galeri->judul }}</li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
                <div class="position-relative">
    <img src="{{ url('/images/dayak patternn vert@0,13x.png') }}" class="img-fluid position-absolute top-0 right-0 " alt=""
        style="transform: translateX(60%);" />
</div>
<div class="container pt-2 pb-4" style="margin-top: 30mm;">
    <div class="row pb-4 mb-2">
        <!-- Carousel Section for Galeri Images -->
        <div class="col-md-6 mb-4 mb-md-0 appear-animation" data-appear-animation="fadeInLeftShorter" data-appear-animation-delay="300">
            <div class="owl-carousel owl-theme nav-inside nav-inside-edge nav-squared nav-with-transparency nav-dark mt-3" data-plugin-options="{'items': 1, 'margin': 10, 'loop': false, 'nav': true, 'dots': false}">
                @foreach($galeri->images as $image)
                <div>
                    <div class="img-thumbnail border-0 border-radius-0 p-0 d-block">
                        <img src="{{ asset('storage/' . $image) }}" class="img-fluid border-radius-0" alt="{{ $galeri->judul }}">
                    </div>
                </div>
                @endforeach
            </div>

            <hr class="solid my-5 appear-animation" data-appear-animation="fadeIn" data-appear-animation-delay="1000">

            <div class="appear-animation" data-appear-animation="fadeInRightShorter" data-appear-animation-delay="1100">
                <strong class="text-uppercase text-1 me-3 text-dark float-start position-relative top-2">Share</strong>
                <ul class="social-icons">
                    <li class="social-icons-facebook"><a href="http://www.facebook.com/" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                    <li class="social-icons-twitter"><a href="http://www.twitter.com/" target="_blank" title="Twitter"><i class="fab fa-x-twitter"></i></a></li>
                    <li class="social-icons-linkedin"><a href="http://www.linkedin.com/" target="_blank" title="Linkedin"><i class="fab fa-linkedin-in"></i></a></li>
                </ul>
            </div>
        </div>

        <!-- Galeri Description Section -->
        <div class="col-md-6">
            <div class="overflow-hidden">
                <h2 class="text-color-dark font-weight-normal text-4 mb-0 appear-animation" data-appear-animation="maskUp" data-appear-animation-delay="600">
                    Galeri <strong class="font-weight-extra-bold">Description</strong>
                </h2>
            </div>
            <p class="appear-animation" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="800">
                {{ $galeri->deskripsi }}
            </p>

            <div class="overflow-hidden mt-4">
                <h2 class="text-color-dark font-weight-normal text-4 mb-0 appear-animation" data-appear-animation="maskUp" data-appear-animation-delay="1000">
                    Galeri <strong class="font-weight-extra-bold">Details</strong>
                </h2>
            </div>
            <ul class="list list-icons list-primary list-borders text-2 appear-animation" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="1200">
                <li><i class="fas fa-caret-right left-10"></i> <strong class="text-color-primary">Judul:</strong> {{ $galeri->judul }}</li>
                <li><i class="fas fa-caret-right left-10"></i> <strong class="text-color-primary">Kategori:</strong> {{ ucfirst($galeri->kategori) }}</li>
                <li><i class="fas fa-caret-right left-10"></i> <strong class="text-color-primary">Date:</strong> {{ $galeri->created_at->format('F d, Y') }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection