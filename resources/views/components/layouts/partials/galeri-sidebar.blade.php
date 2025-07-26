<div class="col-lg-3 position-relative">
    <aside class="sidebar" id="sidebar" data-plugin-sticky data-plugin-options="{'minWidth': 991, 'containerSelector': '.container', 'padding': {'top': 110}}">

        <h5 class="font-weight-semi-bold">Filter By</h5>
        
        <ul class="nav nav-list flex-column sort-source mb-5" data-sort-id="portfolio" data-option-key="filter" data-plugin-options="{'layoutMode': 'fitRows', 'filter': '*'}">
            <!-- Show All -->
            <li class="nav-item" wire:click.prevent="applyFilter(null)">
                <a class="nav-link {{ is_null($filter) ? 'active' : '' }}" href="#">Show All</a>
            </li>

            <!-- Filter by Kegiatan -->
            <li class="nav-item" wire:click.prevent="applyFilter('kegiatan')">
                <a class="nav-link {{ $filter == 'kegiatan' ? 'active' : '' }}" href="#">Kegiatan</a>
            </li>

            <!-- Filter by Awards -->
            <li class="nav-item" wire:click.prevent="applyFilter('awards')">
                <a class="nav-link {{ $filter == 'awards' ? 'active' : '' }}" href="#">Awards</a>
            </li>

            <!-- Filter by Lainnya -->
            <li class="nav-item" wire:click.prevent="applyFilter('lainnya')">
                <a class="nav-link {{ $filter == 'lainnya' ? 'active' : '' }}" href="#">Lainnya</a>
            </li>
        </ul>
        
    </aside>
</div>
