<div class="header-nav-main header-nav-main-square header-nav-main-dropdown-no-borders header-nav-main-dropdown-border-radius header-nav-main-text-capitalize header-nav-main-text-size-4 header-nav-main-arrows header-nav-main-full-width-mega-menu header-nav-main-mega-menu-bg-hover header-nav-main-effect-2">
    <nav class="collapse">
        <ul class="nav nav-pills" id="mainNav">
            <li class="dropdown" wire:click="setActive('home', 'home')">
                <a href="javascript:void(0)" class="nav-link {{ $activeMenu == 'home' ? 'active' : '' }}">
                    Home
                </a>
            </li>
            <li class="dropdown" wire:click="setActive('about')">
                <a class="nav-link dropdown-toggle {{ $activeMenu == 'about' ? 'active' : '' }}" href="javascript:void(0)">
                    Tentang Kami
                </a>
                <ul class="dropdown-menu">
                    <li wire:click="setActive('profile', 'profile')">
                        <a href="javascript:void(0)" class="dropdown-item">Profil Organisasi</a>
                    </li>
                    <li wire:click="setActive('vision', 'vision')">
                        <a href="javascript:void(0)" class="dropdown-item">Visi Misi</a>
                    </li>
                    <li wire:click="setActive('functions', 'functions')">
                        <a href="javascript:void(0)" class="dropdown-item">Tugas & Fungsi</a>
                    </li>
                    <li wire:click="setActive('structure', 'structure')">
                        <a href="javascript:void(0)" class="dropdown-item">Struktur Organisasi</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown" wire:click="setActive('services')">
                <a class="nav-link dropdown-toggle {{ $activeMenu == 'services' ? 'active' : '' }}" href="javascript:void(0)">
                    Layanan
                </a>
                <ul class="dropdown-menu">
                    <li wire:click="setActive('skt', 'skt')">
                        <a href="javascript:void(0)" class="dropdown-item">Layanan SKT</a>
                    </li>
                    <li wire:click="setActive('skl', 'skl')">
                        <a href="javascript:void(0)" class="dropdown-item">Layanan SKL</a>
                    </li>
                        <a href="https://lapor.go.id" class="dropdown-item">Aduan Publik</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown" wire:click="setActive('informasi')">
                <a class="nav-link dropdown-toggle {{ $activeMenu == 'informasi' ? 'active' : '' }}" href="javascript:void(0)">
                    Informasi
                </a>
                <ul class="dropdown-menu">
                    <li wire:click="setActive('berita', 'berita')">
                        <a href="javascript:void(0)" class="dropdown-item">Berita</a>
                    </li>
                    <li wire:click="setActive('infografis', 'infografis')">
                        <a href="javascript:void(0)" class="dropdown-item">Infografis</a>
                    </li>
                    <li wire:click="setActive('publikasi', 'publikasi')">
                        <a href="javascript:void(0)" class="dropdown-item">Publikasi</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>
