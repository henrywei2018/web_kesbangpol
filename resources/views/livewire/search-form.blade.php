<div class="header-nav-feature header-nav-features-search d-inline-flex">
    <a href="#" class="header-nav-features-toggle text-decoration-none" data-focus="headerSearch" aria-label="Search"><i class="fas fa-search header-nav-top-icon text-3"></i></a>
    <div class="header-nav-features-dropdown" id="headerTopSearchDropdown">
        <form wire:submit.prevent="submitSearch">
            <div class="simple-search input-group">
                <input wire:model="searchTerm" class="form-control text-1" type="search" placeholder="Search...">
                <button class="btn" type="submit">
                    <i class="fas fa-search header-nav-top-icon"></i>
                </button>
            </div>
        </form>
    </div>
</div>
