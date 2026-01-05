<header id="header" class="header-transparent header-effect-shrink"
    data-plugin-options="{'stickyEnabled': true, 'stickyEffect': 'shrink', 'stickyEnableOnBoxed': true, 'stickyEnableOnMobile': false, 'stickyHeaderContainerHeight': 80, 'stickyStartAt': 50, 'stickyChangeLogo': false}">
    <div class="header-body border-top-0 bg-primary appear-animation" data-appear-animation="fadeInUpShorterPlus"
        data-appear-animation-delay="2000" data-plugin-options="{'forceAnimation': true}">
        <div class="header-container container-fluid">
            <div class="header-row">
                <div class="header-column align-items-start justify-content-center">
                    <div class="header-logo z-index-2 col-lg-2 px-0">
                        <a href="{{ url('/') }}">
                            <img alt="Kesbangpol Kaltara" width="280" height="60"
                                src="{{ asset('images/logo_white_small.png') }}">
                        </a>
                    </div>
                </div>
                <div class="header-column flex-row justify-content-end justify-content-lg-center">
                    <div
                        class="header-nav header-nav-line header-nav-bottom-line header-nav-bottom-line-effect-1 header-nav-dropdowns-dark header-nav-light-text justify-content-end">
                        <div
                            class="header-nav-main header-nav-main-arrows header-nav-main-mobile-dark header-nav-main-dropdown-no-borders header-nav-main-effect-3 header-nav-main-sub-effect-1">
                            <nav class="collapse">
                                <ul class="nav nav-pills" id="mainNav">
                                    <li class="dropdown">
                                        <a href="{{ url('/') }}" class="nav-link">
                                            Home
                                        </a>
                                    </li>
                                    <li class="dropdown">
                                        <a class="nav-link dropdown-toggle" href="#">
                                            Tentang Kami
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ url('/profil') }}" class="dropdown-item">Profil
                                                    Organisasi</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/visi-misi') }}" class="dropdown-item">Visi & Misi</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/tugas-fungsi') }}" class="dropdown-item">Tugas &
                                                    Fungsi</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/struktur-organisasi') }}"
                                                    class="dropdown-item">Struktur Organisasi</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a class="nav-link dropdown-toggle" href="#">
                                            Informasi
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ url('/blog') }}" class="dropdown-item">Berita</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/infografis') }}" class="dropdown-item">Infografis</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/publikasi') }}" class="dropdown-item">Publikasi</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a class="nav-link dropdown-toggle" href="#">
                                            Layanan
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ url('/pokuskaltara') }}" class="dropdown-item">POKUS KALTARA</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/layanan-skt') }}" class="dropdown-item">Layanan
                                                    SKT</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/layanan-skl') }}" class="dropdown-item">Layanan
                                                    SKL</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/kontak-kami') }}" class="dropdown-item">Layanan
                                                    Aduan</a>
                                            </li>
                                            <li>
                                                <a href="{{ url('/layanan-ppid') }}" class="dropdown-item">Layanan
                                                    PPID</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="{{ url('/galeri') }}" class="dropdown-item">Galeri</a>
                                    </li>
                                    <li class="dropdown">
                                        <a href="{{ url('/login') }}"
                                            class="dropdown-item btn btn-modern btn-outline btn-arrow-effect-1 anim-hover-translate-right-5px transition-2ms">Masuk/Daftar<i
                                                class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                                                
                    </div>

                    <button
                        class="btn header-btn-collapse-nav bg-transparent border-0 text-4 position-relative top-2 p-0 ms-4"
                        data-bs-toggle="collapse" data-bs-target=".header-nav-main nav">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="header-column align-items-end justify-content-center d-none d-lg-flex">
                    <!--<ul-->
                    <!--    class="header-social-icons social-icons social-icons-clean social-icons-icon-light social-icons-medium custom-social-icons-divider">-->
                    <!--    <li class="social-icons-facebook">-->
                    <!--        <a href="http://www.facebook.com/" target="_blank" title="Facebook"><i-->
                    <!--                class="fab fa-facebook-f"></i></a>-->
                    <!--    </li>-->
                    <!--    <li class="social-icons-twitter">-->
                    <!--        <a href="http://www.twitter.com/" target="_blank" title="Twitter"><i-->
                    <!--                class="fab fa-x-twitter"></i></a>-->
                    <!--    </li>-->
                    <!--    <li class="social-icons-linkedin">-->
                    <!--        <a href="http://www.linkedin.com/" target="_blank" title="Linkedin"><i-->
                    <!--                class="fab fa-linkedin-in"></i></a>-->
                    <!--    </li>-->
                    <!--</ul>-->
                </div>
            </div>
        </div>
    </div>
</header>