@extends('components.layouts.app')
@section('content')
<section class="section bg-primary border-0 position-relative z-index-1 py-0 m-0">
	
	<div class="custom-slider-background thumb-info-wrapper-opacity-1 overflow-hidden">		
		<!-- First Slider Section -->
		<div class="custom-slider-background-image-stage-outer  appear-animation" data-appear-animation="maskUp"
			data-appear-animation-delay="1600">
			<div class="custom-slider-background-image-stage">
				@foreach($sliderBanners as $banner)
				@php
				$imageUrl = $banner->getFirstMediaUrl('banners') ?: 'assets/img/default-image.jpg'; // Fallback if media is missing
				@endphp
				<div class="custom-slider-background-image-item overlay overlay-show overlay-op-6"
					style="background-image: url('{{ $imageUrl }}'); background-size: cover; background-position: center;">
				</div>
				@endforeach
			</div>
		</div>
				
	</div>
	<div class="owl-carousel-wrapper appear-animation" data-appear-animation="maskUp" data-appear-animation-delay="1600"
		style="height: 600px;">
		<div id="slider"
			class="owl-carousel dots-inside dots-horizontal-center show-dots-xs custom-dots-position nav-style-1 nav-inside nav-inside-plus nav-light nav-lg nav-font-size-lg mb-0"
			data-plugin-options="{'responsive': {'0': {'items': 1, 'dots': true, 'nav': false}, '479': {'items': 1}, '768': {'items': 1}, '979': {'items': 1}, '1199': {'items': 1, 'nav': true, 'navVerticalOffset': '-100px', 'dots': false}}, 'loop': false, 'autoHeight': false, 'margin': 0, 'dots': true, 'dotsVerticalOffset': '-115px', 'nav': false, 'animateIn': 'fadeIn', 'animateOut': 'fadeOut', 'mouseDrag': false, 'touchDrag': false, 'pullDrag': false, 'autoplay': false, 'autoplayTimeout': 9000, 'autoplayHoverPause': true, 'rewind': true}">

			<!-- Carousel Slide 1 -->
			<div class="position-relative overflow-hidden"
				data-dynamic-height="['600px','600px','600px','550px','500px']" style="height: 600px;">
				<div
					class="container container-xl-custom custom-container-style-2 position-relative z-index-3 h-100 pt-5 mt-5 mt-sm-3">
					<div class="row align-items-center h-100">
						<div class="col">
							<div class="overflow-hidden mb-2 mb-sm-1 mb-md-0">
								<h3 class="text-color-light font-weight-black line-height-1 text-10 text-md-10 text-lg-12 ls-0 mb-0 appear-animation"
									data-appear-animation="maskUp">Badan Kesatuan Bangsa & Politik</h3>
							</div>
							<div class="overflow-hidden opacity-8 mb-1">
								<h2 class="text-color-light line-height-6 line-height-md-2 text-5 text-md-6 positive-ls-3 mb-0 appear-animation"
									data-appear-animation="maskUp" data-appear-animation-delay="250">Provinsi Kalimantan Utara</h2>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Carousel Slide 2 -->
			<div class="position-relative overflow-hidden"
				data-dynamic-height="['600px','600px','600px','550px','500px']" style="height: 600px;">
				<div
					class="container container-xl-custom custom-container-style-2 position-relative z-index-3 h-100 pt-5 mt-5 mt-sm-3">
					<div class="row align-items-center justify-content-end h-100">
						<div class="col text-end">
							<div class="overflow-hidden mb-2 mb-sm-1 mb-md-0">
								<h3 class="text-color-light font-weight-black line-height-1 text-10 text-md-10 text-lg-12 ls-0 mb-0 appear-animation"
									data-appear-animation="maskUp">Badan Kesatuan Bangsa & Politik</h3>
							</div>
							<div class="overflow-hidden opacity-8 mb-1">
								<h2 class="text-color-light line-height-6 line-height-md-2 text-5 text-md-6 positive-ls-3 mb-0 appear-animation"
									data-appear-animation="maskUp" data-appear-animation-delay="250">Provinsi Kalimantan Utara</h2>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<div class="absolute bottom-0 left-0 w-full">
    <svg viewBox="0 0 1440 100" preserveAspectRatio="none">
      <path 
        d="M0,0 C480,120 960,120 1440,0 L1440,120 L0,120 Z" 
        fill="white">
      </path>
    </svg>
  </div>
</section>
<div class="custom-page-wrapper">
	
	<section class="section bg-transparent border-0 position-relative py-0 m-0">
		<div class="container container-xl-custom custom-container-style custom-margin-top">
			<div class="row mb-5">
				<div class="col">
					<div class="overflow-hidden">
						<div class="owl-carousel-wrapper position-relative z-index-1 appear-animation"
							data-appear-animation="maskUp" data-appear-animation-delay="50" style="height: 250px;">
							<div class="owl-carousel owl-theme owl-loaded owl-drag owl-carousel-init"
								data-plugin-carousel=""
								data-plugin-options="{ 
														&quot;dots&quot;: true, 
														&quot;autoplay&quot;: true, 
														&quot;autoplayTimeout&quot;: 3000, 
														&quot;loop&quot;: true, 
														&quot;margin&quot;: 10, 
														&quot;nav&quot;: false, 
														&quot;responsive&quot;: {
															&quot;0&quot;: { &quot;items&quot;: 1 }, 
															&quot;600&quot;: { &quot;items&quot;: 3 }, 
															&quot;1000&quot;: { &quot;items&quot;: 6 } 
														}
													}">
								<div class="owl-stage-outer">
								<div class="owl-stage" style="transform: translate3d(0px, 0px, 0px); transition: 0.25s;">
									@foreach($cardBanners as $banner)
									<div class="owl-item" style=" margin-right: 10px;">
											<a href="{{ $banner->click_url }}" target="{{ $banner->click_url_target }}">
												<img id="article-image-card"
													src="{{ $banner->getFirstMediaUrl('banners', 'thumb') }}"
													style="height: auto; width: 100%; border-radius: 1.7rem;"
													alt="{{ $banner->alt_text ?? 'Default alt text' }}">
											</a>
									</div>
									@endforeach
								</div>
								</div>
								
							</div>

						</div>
					</div>
				</div>
			</div>
	</section>

	<section class="section bg-transparent border-0 position-relative m-0">
		<div class="container container-xl-custom py-1">
			
			<div class="container container-xl-custom py-1">
				<div class="row pb-1 pt-2">
					<div class="row">
						<div class="d-flex align-items-center justify-content-between py-3" style="height: 52px;">
							<h5 class="text-4 flex-shrink-0 d-flex align-items-center m-0">
								<strong class="font-weight-bold rounded-3 text-1 px-3 text-light py-2 bg-primary text-nowrap d-inline-flex align-items-center">
								Berita terkini
								</strong>
							</h5>
							
							<div class="divider divider-primary divider-sm mx-4 flex-grow-1 align-self-center">
							
							</div>
							
							<a href="http://127.0.0.1:8000/blog" class="btn btn-outline btn-primary  rounded-3  font-weight-bold text-3 px-5 py-2">Lihat Semua</a>
						</div>
											
						<div class="container col-lg-7 py-3">
								<!-- Article Section -->
								<div>
									<article id="article-content" class="card rounded-3 border-0 thumb-info thumb-info-no-borders thumb-info-bottom-info thumb-info-bottom-info-dark overflow-hidden">
										<div class="thumb-info-wrapper thumb-info-wrapper-opacity-1 position-relative" style="height: 460px; overflow: hidden;">
											<a id="article-link" href="#">
													<span class="thumb-info-action-icon thumb-info-action-icon-light"><i class="fas fa-play-circle text-dark text-dark"></i></span>
											</a>
											<img id="article-image"
												src="{{ asset('img/default-blog-image.jpg') }}"
												class="img-fluid"
												style="height: 100%; width: auto;"
												alt="Default Title">
											<div class="thumb-info-title position-absolute bottom-0 w-100 bg-black bg-opacity-50 text-white px-4 py-3 rounded-bottom">
												<div id="article-category" class="thumb-info-type bg-dark px-2 py-1 d-inline-block rounded mb-2">
													Uncategorized
												</div>
												<h2 id="article-title" class="font-weight-bold  text-light line-height-1 text-5 mb-1">
													Untitled
												</h2>
												<div class="d-flex justify-content-between align-items-center text-light mt-3">
													<p id="article-meta" class="text-uppercase text-1 text-light opacity-8 mb-0">
														<time pubdate datetime="">
															01 Jan 1970
														</time>
														<span class="opacity-8 d-inline-block px-2 text-light">|</span>
														0 <i class="icon-book-open icons"> kali dibaca</i>
													</p>
													<div class="d-flex justify-content-between align-items-center owl-nav">
														<button id="prev-article" class="btn btn-tertiary btn-sm">
															<i class="bi bi-chevron-left"></i>
															<
																</button>
																<span id="article-counter" class="text-light small px-2">Article 1 of 1</span>
																<button id="next-article" class="btn btn-tertiary btn-sm">
																	> <i class="bi bi-chevron-right"></i>
																</button>
													</div>
												</div>
											</div>
										</div>
									</article>
									@push('scripts')
									<script>
										document.addEventListener('DOMContentLoaded', () => {
											// Parse the JSON data from PHP
											const articles = @json($featuredPosts);
											const totalArticles = articles.length;

											if (totalArticles === 0) {
												console.warn('No articles available.');
												return;
											}

											let currentIndex = 0;

											// DOM Elements
											const articleImage = document.getElementById('article-image');
											const articleLink = document.getElementById('article-link');
											const articleCategory = document.getElementById('article-category');
											const articleTitle = document.getElementById('article-title');
											const articleMeta = document.getElementById('article-meta');
											const articleCounter = document.getElementById('article-counter');
											const prevButton = document.getElementById('prev-article');
											const nextButton = document.getElementById('next-article');

											const updateArticleContent = (index) => {
												const article = articles[index];

												if (!article) {
													console.warn(`No article found at index ${index}`);
													return;
												}

												// Access array elements using bracket notation
												articleImage.src = article['image_url'];
												articleLink.href = `/blog/${article['slug']}`;
												articleImage.alt = article['title'];
												articleCategory.textContent = article['category'];
												articleTitle.textContent = article['title'];

												const publishDate = new Date(article['published_at']).toLocaleDateString('en-US', {
													year: 'numeric',
													month: 'short',
													day: 'numeric'
												});

												articleMeta.innerHTML = `
							<time pubdate datetime="${article['published_at']}">
								${publishDate}
							</time>
							<span class="opacity-3 d-inline-block px-2">|</span>
							${article['view_count']} <i class="icon-book-open icons"> kali dibaca</i>
						`;

												articleCounter.textContent = `Article ${index + 1} of ${totalArticles}`;

												// Debug log
												console.log('Updated article:', article);
											};

											const navigate = (direction) => {
												if (direction === 'next') {
													currentIndex = (currentIndex + 1) % totalArticles;
												} else if (direction === 'prev') {
													currentIndex = (currentIndex - 1 + totalArticles) % totalArticles;
												}
												updateArticleContent(currentIndex);
											};

											// Event Listeners
											prevButton.addEventListener('click', () => navigate('prev'));
											nextButton.addEventListener('click', () => navigate('next'));

											// Initialize first article
											updateArticleContent(currentIndex);

											// Debug log
											console.log('Initial articles data:', articles);
										});
									</script>
									@endpush
								</div>
						</div>
							<!-- Right Column -->
						<div class="col-lg-5 py-3">
								<div class="tabs">
									<ul class="nav nav-tabs nav-justified flex-column flex-md-row" role="tablist">
										<li class="nav-item" role="presentation">
											<a class="nav-link" href="#popular10" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Populer</a>
										</li>
										<li class="nav-item active" role="presentation">
											<a class="nav-link active" href="#recent10" data-bs-toggle="tab" aria-selected="true" role="tab">Terbaru</a>
										</li>
									</ul>
									<div class="tab-content">
										<div id="popular10" class="tab-pane" role="tabpanel">
											@foreach($popularPosts as $post)
											<div class="col-md-12 col-lg-12 appear-animation animated fadeInUpShorter appear-animation-visible" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="600" style="animation-delay: 600ms;">
												<div class="card card-text-color-hover-light border-0 bg-color-hover-primary transition-2ms ">
													<div class="card-body" style="
										padding-top: 10px;
										padding-bottom: 10px;
										padding-right: 10px;
										padding-left: 10px;
										">
														<h4 class="card-title mb-1 text-4 line-height-1 font-weight-bold transition-2ms"><a href="{{ route('post.show', $post->slug) }}" class="text-decoration-none text-primary">
																{{ Str::words($post->title, 10, '...') }}
															</a></h4>
															@php
															$date = \Carbon\Carbon::parse($post->published_at)->locale('id');
															@endphp
																<div class="p-absolute bottom-2 right-1 d-flex justify-content-end py-1 px-1 z-index-3">
																<p class="text-uppercase text-1 text-color-default mb-0 px-2">
																	{{ $post->published_at->diffForHumans() }}	
																</p>
																<span class="text-center bg-primary rounded-2 text-color-light font-weight-semibold line-height-1 px-1 py-1">
																		<span class="position-relative text-2 z-index-2">
																			{{ $date->format('d') }}
																			<span class="d-block text-0 positive-ls-2">
																			{{ $date->format('M') }}
																			</span>
																		</span>
																	</span>
																</div>
														<p class="text-uppercase text-1 text-color-default mb-0">
															{{ $post->view_count }} <i class="icon-book-open icons"> Baca</i> 
														</p>
													</div>
												</div>

											</div>
											@endforeach
										</div>
										<div id="recent10" class="tab-pane active show" role="tabpanel">
											@foreach($latestPosts as $post)
											<div class="col-md-12 col-lg-12 appear-animation animated fadeInUpShorter appear-animation-visible" data-appear-animation="fadeInUpShorter" data-appear-animation-delay="600" style="animation-delay: 600ms;">
												<div class="card card-text-color-hover-light border-0 bg-color-hover-primary transition-2ms ">
													<div class="card-body" style="
									padding-top: 10px;
									padding-bottom: 10px;
									padding-right: 10px;
									padding-left: 10px;
									">
														<h4 class="card-title mb-1 text-4 line-height-1 font-weight-bold transition-2ms"><a href="{{ route('post.show', $post->slug) }}" class="text-decoration-none text-primary">
																{{ Str::words($post->title, 10, '...') }}
															</a>
														</h4>
														@php
															$date = \Carbon\Carbon::parse($post->published_at)->locale('id');
														@endphp
															<div class="p-absolute bottom-2 right-1 d-flex justify-content-end py-1 px-1 z-index-3">
																<p class="text-uppercase text-1 text-color-default mb-0 px-2">
																	{{ $post->published_at->diffForHumans() }}	
																</p>
																<span class="text-center bg-primary rounded-2 text-color-light font-weight-semibold line-height-1 px-1 py-1">
																	<span class="position-relative text-2 z-index-2">
																		{{ $date->format('d') }}
																		<span class="d-block text-0 positive-ls-2">
																		{{ $date->format('M') }}
																		</span>
																	</span>
																</span>
															</div>
														<p class="text-uppercase text-1 text-color-default mb-0">
															{{ $post->view_count }} <i class="icon-book-open icons"> Baca</i>
														</p>
													</div>
												</div>
											</div>
											@endforeach
										</div>

									</div>
								</div>
						</div>
					</div>
				</div>
			</div>

			<div class="container container-xl-custom py-1">
				<div class="row pb-1 pt-2">
					<div class="col-md-8 pt-3">
						<div class="heading heading-border heading-middle-border mt-3 pt-6">
							<h3 class="text-4"><strong class="font-weight-bold rounded-3 text-1 px-3 text-light py-2 bg-primary">Infografis</strong></h3>
						</div>
						<div class="row pb-1"> <!-- Bagian infografis -->
							<div class="col-lg-5 pb-1">
								@if($latestInfografis->isNotEmpty())
								<article class="thumb-info thumb-info-no-zoom bg-transparent border-radius-0 pb-2 mb-2">
									<div class="row">
										<div class="col">
											<div class="large-image-frame">
												<a href="{{ route('infographic.show', $latestInfografis[0]->slug) }}">
													<img src="{{ $latestInfografis[0]->getFirstMediaUrl('infographics', 'thumb') }}" alt="{{ $latestInfografis[0]->title }}">
													<div class="image-overlay">
														<div class="overlay-content">
															<span class="date">{{ $latestInfografis[0]->created_at->format('F d, Y') }}</span>
															<h4>{{ $latestInfografis[0]->judul }}</h4>
														</div>
													</div>
												</a>
											</div>
										</div>
									</div>
								</article>
								@endif
							</div>

							<div class="col-lg-6">
								@foreach($latestInfografis->skip(1) as $infographic)
								<article class="thumb-info thumb-info-no-zoom bg-transparent border-radius-0 pb-4 mb-2">
									<div class="row align-items-center pb-1">
										<div class="col-sm-4">
											<div class="small-image-frame">
												<a href="{{ route('infographic.show', $infographic->slug) }}">
													<img src="{{$infographic->getFirstMediaUrl('infographics') }}" alt="{{ $infographic->title }}">
												</a>
											</div>
										</div>
										<div class="col-sm-7 ps-sm-0">
											<div class="thumb-info-caption-text">
												<div class="d-inline-block text-default text-1 float-none">
													<a href="{{ route('infographic.show', $infographic->slug) }}" class="text-decoration-none text-color-default">
														{{ $infographic->created_at->format('F d, Y') }}
													</a>
												</div>
												<h4 class="d-block pb-2 line-height-2 text-3 text-dark font-weight-bold mb-0">
													<a href="{{ route('infographic.show', $infographic->slug) }}" class="text-decoration-none text-color-dark">
														{{ $infographic->judul }}
													</a>
												</h4>
											</div>
										</div>
									</div>
								</article>
								@endforeach
							</div>
						</div>
						<div class="row pb-6 text-center">
							<a href="{{ url('/infografis') }}"
								class="btn btn-primary btn-outline rounded-3 font-weight-bold text-3 px-5 py-2">Lihat Semua</a>
						</div>
						<div class="heading heading-border heading-middle-border mt-3  pt-6">
							<h3 class="text-4"><strong class="font-weight-bold rounded-3 text-1 px-3 text-light py-2 bg-primary">Publikasi</strong></h3>
						</div>
						<div class="row pb-1"> <!-- Bagian Publikasi -->
							<div class="col">
								<div class="owl-carousel owl-theme stage-margin rounded-nav nav-dark nav-icon-1 nav-size-md nav-position-1 owl-loaded owl-drag owl-carousel-init"
									data-plugin-options="{'responsive': {'0': {'items': 1}, '479': {'items': 1}, '768': {'items': 2}, '979': {'items': 2}, '1199': {'items': 3}}, 'margin': 10, 'loop': true, 'nav': true, 'dots': false, 'stagePadding': 40}">

									@foreach($publikasi as $item)
									<div class="item">
										<div class="featured-box h-100">
											<div class="box-content">
												<div class="pdf-icon text-center mb-3">
													<i class="far fa-file-pdf fa-3x text-danger"></i>
												</div>
												<h5 class="text-center">{{ Str::limit($item->title, 50) }}</h5>
												<p>{{ $item->publication_date }}</p>
												<div class="item-footer">
													
													@if($item->hasPdf())
													<a href="{{ $item->getPdfUrl() }}"
														target="_blank"
														class="btn btn-sm btn-primary"
														data-toggle="tooltip"
														data-placement="top"
														title="Download {{ $item->judul }}">
														Download
													</a>
													@else
													<span class="text-muted">No File</span>
													@endif
												</div>
											</div>
										</div>
									</div>
									@endforeach
								</div>
							</div>
						</div>
						<div class="row pb-6 text-center">
							<a href="{{ url('/publikasi') }}"
								class="btn btn-primary btn-outline rounded-3 font-weight-bold text-3 px-5 py-2">Lihat Semua</a>
						</div>
						<div class="heading heading-border heading-middle-border mt-3 pt-6">
							<h3 class="text-4"><strong class="font-weight-bold rounded-3 text-1 px-3 text-light py-2 bg-primary">Daftar Informasi Publik</strong></h3>
						</div>
						<div class="row pb-1"> <!-- Bagian Daftar informasi Publik -->
							<div class="col">
							<div class="owl-carousel owl-theme stage-margin rounded-nav nav-dark nav-icon-1 nav-size-md nav-position-1 owl-loaded owl-drag owl-carousel-init"
							data-plugin-options="{'responsive': {'0': {'items': 1}, '479': {'items': 1}, '768': {'items': 2}, '979': {'items': 2}, '1199': {'items': 3}}, 'margin': 10, 'loop': true, 'nav': true, 'dots': false, 'stagePadding': 40}">

									@foreach($daftardip as $item)
									<div class="item">
										<div class="featured-box h-100">
											<div class="box-content">
												<div class="pdf-icon text-center mb-3">
													<i class="far fa-file-pdf fa-3x text-danger"></i>
												</div>
												<h5 class="text-center">{{ Str::limit($item->title, 50) }}</h5>
												<p>{{ $item->publication_date }}</p>
												<div class="item-footer">									
													@if($item->hasPdf())
													<a href="{{ $item->getPdfUrl() }}"
														target="_blank"
														class="btn btn-sm btn-primary"
														data-toggle="tooltip"
														data-placement="top"
														title="Download {{ $item->judul }}">
														Download
													</a>
													@else
													<span class="text-muted">No File</span>
													@endif
												</div>
											</div>
										</div>
									</div>
									@endforeach
								</div>
							</div>
						</div>

						<style>
							.featured-box {
								background: #FFF;
								border-radius: 8px;
								box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
								transition: transform 0.2s ease;
							}

							.featured-box:hover {
								transform: translateY(-3px);
							}

							.box-content {
								padding: 20px;
								min-height: 220px;
								display: flex;
								flex-direction: column;
							}

							h5 {
								font-size: 1rem;
								font-weight: 600;
								margin-bottom: 10px;
							}

							.item-footer {
								margin-top: auto;
								display: flex;
								align-items: center;
								padding-top: 15px;
								border-top: 1px solid #eee;
								justify-content: space-around;
							}

							.date {
								font-size: 0.85rem;
								color: #666;
							}

							.btn-danger {
								background-color: #ff0000;
								border-color: #ff0000;
							}

							.owl-nav button {
								width: 30px !important;
								height: 30px !important;
								background: rgba(255, 255, 255, 0.7) !important;
								border-radius: 50% !important;
								box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
							}

							.owl-nav button span {
								color: #333;
							}
						</style>
						<div class="row pb-3 text-center">
							<a href="{{ url('/layanan-ppid') }}"
								class="btn btn-primary btn-outline rounded-3 font-weight-bold text-3 px-5 py-2">Lihat Semua</a>
						</div>
						<style>
							.large-image-frame {
								display: block;
								position: relative;
								border-radius: 12px;
								overflow: hidden;
								box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
								height: 380px;
							}

							.small-image-frame {
								display: block;
								position: relative;
								border-radius: 8px;
								overflow: hidden;
								box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
								height: 100px;
								max-width: 200px;
							}

							.large-image-frame img,
							.small-image-frame img {
								width: 100%;
								height: 100%;
								object-fit: cover;
							}

							/* Hover effects only for large image */
							.large-image-frame img {
								transition: all 0.3s ease;
							}

							.large-image-frame:hover img {
								transform: scale(1.1);
							}

							.image-overlay {
								position: absolute;
								bottom: 0;
								left: 0;
								right: 0;
								padding: 20px;
								background: linear-gradient(to top, rgba(239, 13, 13, 0.9), transparent);
								opacity: 0;
								transition: all 0.3s ease;
							}

							.large-image-frame:hover .image-overlay {
								opacity: 1;
							}

							.overlay-content {
								position: relative;
								z-index: 2;
							}

							.overlay-content .date {
								font-size: 0.8rem;
								display: block;
								margin-bottom: 1px;
								color: #fff;
							}

							.overlay-content h4 {
								font-size: 1.2rem;
								margin: 0;
								color: #fff;
								font-weight: bold;
							}

							.large-image-frame a {
								text-decoration: none;
								display: block;
								height: 100%;
							}
						</style>
					</div>
					<div class="col-md-4">
						<aside class="sidebar pt-3 pb-4" style="padding-left:10px;">
							{{-- resources/views/livewire/widgets/gpr-widget.blade.php --}}
							<!--<div class="widget-card post-card">-->
							<!--	<link rel="stylesheet" href="https://widget.kominfo.go.id/gpr-widget-kominfo.min.css">-->
							<!--	<script type="text/javascript" src="https://widget.kominfo.go.id/gpr-widget-kominfo.min.js"></script>-->
							<!--	<div id="gpr-kominfo-widget-container"></div>-->
							<!--</div>-->

							<!--<style>-->
							
							<!--	#gpr-kominfo-widget-header,-->
							<!--	#gpr-kominfo-widget-footer {-->
							<!--		display: none !important;-->
							<!--	}-->

							<!--	.widget-card {-->
							<!--		background-color: var(--light) !important;-->
							<!--		width: 316px !important;-->
							<!--		min-width: 300px !important;-->
							<!--		max-width: 340px !important;-->
							<!--		border-radius: 8px !important;-->
							<!--		padding-left: 20px !important;-->
							<!--		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);-->
							<!--		padding: 10px;-->
							<!--		margin-bottom: 10px;-->
							<!--	}-->

							
							<!--	#gpr-kominfo-widget-container {-->
							<!--		width: 100% !important;-->
							<!--		padding: 0 !important;-->
							<!--		margin: 0 !important;-->
							<!--		border: none !important;-->
							<!--	}-->

							<!--	#gpr-kominfo-widget-body {-->
							<!--		padding: 0 !important;-->
							<!--		margin: 0 !important;-->
							<!--	}-->

							<!--	#gpr-kominfo-widget-list {-->
							<!--		padding: 0;-->
							<!--		margin: 0;-->
							<!--		width: 298px;-->
							<!--		list-style: none;-->
							<!--	}-->

							<!--	.gpr-kominfo-widget-list-item {-->
							<!--		padding: 12px 0;-->
							<!--		border-bottom: 1px solid #f4f4f4;-->
							<!--	}-->

							<!--	.gpr-kominfo-widget-list-item:last-child {-->
							<!--		border-bottom: none;-->
							<!--		padding-bottom: 0;-->
							<!--	}-->

							<!--	.gpr-kominfo-widget-list-item a {-->
							<!--		color: #212529;-->
							<!--		text-decoration: none;-->
							<!--		font-size: 12px;-->
							<!--		line-height: 1.2;-->
							<!--		font-weight: 500;-->
							<!--		margin-top: 5px;-->
							<!--		display: block;-->
							<!--	}-->

							<!--	.gpr-kominfo-widget-list-item a:hover {-->
							<!--		color: #0056b3;-->
							<!--	}-->

							<!--	.gpr-small-date {-->
							<!--		color: #999;-->
							<!--		font-size: 12px;-->
							<!--	}-->

							<!--	.gpr-kominfo-align-left b {-->
							<!--		color: #666;-->
							<!--		font-size: 12px;-->
							<!--	}-->

							<!--	.gpr-kominfo-clear-fix {-->
							<!--		clear: both;-->
							<!--	}-->
							<!--</style>-->

							<h5 class="font-weight-semi-bold pt-2">Instagram</h5>
							<div class="igwrapper rounded-3">
								<style>
									.igwrapper {
										background: #fff;
										position: relative;
										width: 316px;
										border-radius: 80px;
										box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
									}

									.igwrapper iframe {
										border: 0;
										position: relative;
										z-index: 2;
										width: 100% !important;
										min-width: 300x !important;
									}

									.igwrapper a {
										color: rgba(0, 0, 0, 0);
										position: absolute;
										left: 0;
										top: 0;
										z-index: 0;
									}

									.instagram-media {
										min-width: 300px !important;
										width: 300px !important;
									}
								</style>
								<script async src="https://www.instagram.com/embed.js"></script>
								<blockquote class="instagram-media"
									data-instgrm-permalink="https://www.instagram.com/bakesbangpol.kaltara/"
									data-instgrm-version="14"
									style="background:#FFF; border:0; border-radius:16px; box-shadow:none; margin:0; padding:0;">
								</blockquote>
							</div>
							<!--<h5 class="font-weight-semi-bold pt-2">Facebook</h5>-->
							<!--<div class="social-card">-->
							<!--	<div class="fb-wrapper p-0">-->
							<!--		<div class="fb-page"-->
							<!--			data-href="https://www.facebook.com/diskominfokaltara"-->
							<!--			data-tabs="timeline"-->
							<!--			data-width=""-->
							<!--			data-height=""-->
							<!--			data-small-header="false"-->
							<!--			data-adapt-container-width="true"-->
							<!--			data-hide-cover="false"-->
							<!--			data-show-facepile="true">-->
							<!--		</div>-->
							<!--	</div>-->
							<!--</div>-->

							<!--<style>-->
							<!--	.social-card {-->
							<!--		background: #FFF;-->
							<!--		width: 316px;-->
							<!--		border-radius: 8px;-->
							<!--		box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);-->
							<!--		overflow: hidden;-->
							<!--	}-->

							<!--	.fb-wrapper {-->
							<!--		width: 100%;-->
							<!--		overflow: hidden;-->
							<!--	}-->

							<!--	.fb-page {-->
							<!--		width: 100% !important;-->
							<!--	}-->

							<!--	.fb-page span,-->
							<!--	.fb-page iframe {-->
							<!--		width: 336px !important;-->
							<!--		height: 444px !important;-->
							<!--	}-->

							<!--	@media (max-width: 576px) {-->
							<!--		.widget-card {-->
							<!--			width: 100%;-->
							<!--		}-->

							<!--		.fb-page,-->
							<!--		.fb-page span,-->
							<!--		.fb-page iframe {-->
							<!--			width: 100% !important;-->
							<!--		}-->
							<!--	}-->
							<!--</style>-->

							<!-- Facebook SDK -->
							<!--<div id="fb-root"></div>-->
							<!--<script async defer crossorigin="anonymous"-->
							<!--	src="https://connect.facebook.net/id_ID/sdk.js#xfbml=1&version=v18.0">-->
							<!--</script>-->
						</aside>

					</div>
				</div>
			</div>

	</section>
	<div class="container container-xl-custom">
		<div class="row py-5 my-5">
			<div class="col-lg-6 col-xl-7 mx-auto mb-5 mb-lg-0 appear-animation"
				data-appear-animation="fadeInLeftShorterPlus" data-appear-animation-delay="500">
				<div class="owl-carousel owl-theme nav-style-1 nav-outside nav-font-size-lg custom-nav-grey mb-0"
					data-plugin-options="{'responsive': {'576': {'items': 1}, '768': {'items': 2}, '992': {'items': 1}, '1200': {'items': 2}}, 'loop': true, 'nav': true, 'dots': false, 'margin': 20}">
					@foreach($pegawai as $p)
					<div>
						
						<div class="card rounded-3">
							<img class="card-img-top border-radius-12"
                                src="{{ $p->getFirstMediaUrl('pegawai_photos', 'compressed') ?: asset('assets/img/demos/architecture-2/authors/author-1.jpg') }}" 
                                alt="{{ $p->nama_pegawai }}"
                                style="height: 300px; object-fit: cover;" />
							<div class="card-body text-center">
								<h4 class="card-title font-weight-extra-bold text-color-dark text-5 mb-1">{{ $p->nama_pegawai }}</h4>
								<h3 class="text-color-default text-3-5 ls-0 font-weight-normal mb-2 pb-1">{{ $p->jabatan }}
								</h3>
							</div>
						</div>
						
					</div>
					@endforeach
				</div>
			</div>
			<div class="col-lg-5 col-xl-4 text-end position-relative appear-animation"
				data-appear-animation="fadeInLeftShorterPlus" data-appear-animation-delay="250">
				<div class="position-absolute z-index-0 appear-animation" data-appear-animation="fadeInLeftShorter"
					data-appear-animation-delay="500" style="top: 102px; right: -50px;">
					<h2
						class="text-color-dark custom-stroke-text-effect-1 custom-big-font-size-3 font-weight-black opacity-1 mb-0">
						PEGAWAI</h2>
				</div>
				<h2 class="text-color-default positive-ls-3 line-height-3 text-4 mb-2"></h2>
				<h3 class="text-transform-none text-color-dark font-weight-black text-10 line-height-2 mb-4">Kenali Tim Kami</h3>
				<img src="{{ url('/images/divider_vector.png') }}" class="img-fluid opacity-8 mb-1 mt-1" alt="" />
				<p class="custom-font-tertiary text-5 line-height-4 mb-4 mt-2">Temui para profesional berdedikasi yang menjadi kunci keberhasilan kami.</p>
				<p class="text-3-5 pb-3 mb-4">Dengan keahlian dan komitmen tinggi di bidangnya masing-masing, setiap anggota tim kami berperan penting dalam mewujudkan visi dan misi organisasi.</p>
				<a href="{{ url('/kontak-kami')}}"
					class="btn btn-primary custom-btn-style-1 custom-btn-style-1-right font-weight-bold text-3 px-5 py-3">Hubungi Kami</a>
			</div>
		</div>
	</div>
</div>

<div class="position-relative">
	<img src="{{ url('/images/dayak patternn vert@0,25x_v3.png') }}"
		class="img-fluid position-absolute bottom-0 left-0 z-index-0" alt="" />
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://widget.kominfo.go.id/gpr-widget-kominfo.min.js"></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v21.0"></script>
@endpush