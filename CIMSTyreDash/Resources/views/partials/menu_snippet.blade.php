{{--
================================================================================
TYREDASH MENU SNIPPET
================================================================================
Add this inside the CIMS Master Menu <ul class="cims-main-menu">
Paste it alongside the other menu items (Entities, Taxes, Communications, etc.)
================================================================================
--}}

{{-- TYREDASH --}}
<li class="cims-menu-item">
    <a href="javascript:void(0);" class="cims-menu-link">
        <i class="fas fa-circle-notch"></i>
        <span>TyreDash</span>
    </a>
    <div class="sd_tooltip_teal sd-mainmenu-tooltip">Tyre management, quotes, job cards & stock</div>
    <ul class="cims-submenu">
        <li>
            <a href="{{ Route::has('cimstyredash.dashboard') ? route('cimstyredash.dashboard') : '#' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <div class="sd_tooltip_teal sd-menu-tooltip">TyreDash overview and statistics</div>
        </li>
        <li>
            <a href="{{ Route::has('cimstyredash.catalogue.index') ? route('cimstyredash.catalogue.index') : '#' }}">
                <i class="fas fa-search"></i> Size Search
            </a>
            <div class="sd_tooltip_teal sd-menu-tooltip">Search tyres by size from the catalogue</div>
        </li>
        <li>
            <a href="{{ Route::has('cimstyredash.quotes.create') ? route('cimstyredash.quotes.create') : '#' }}">
                <i class="fas fa-file-invoice"></i> New Quote
            </a>
            <div class="sd_tooltip_teal sd-menu-tooltip">Create a new tyre quote</div>
        </li>
        <li>
            <a href="{{ Route::has('cimstyredash.quotes.index') ? route('cimstyredash.quotes.index') : '#' }}">
                <i class="fas fa-list"></i> All Quotes
            </a>
            <div class="sd_tooltip_teal sd-menu-tooltip">View and manage all quotes</div>
        </li>
        <li>
            <a href="{{ Route::has('cimstyredash.jobcards.index') ? route('cimstyredash.jobcards.index') : '#' }}">
                <i class="fas fa-wrench"></i> Job Cards
            </a>
            <div class="sd_tooltip_teal sd-menu-tooltip">View and manage job cards</div>
        </li>
        <li>
            <a href="javascript:void(0);"><i class="fas fa-cogs"></i> Manage</a>
            <div class="sd_tooltip_teal sd-menu-tooltip">Catalogue, brands, services, stock & settings</div>
            <ul class="cims-submenu-level3">
                <li><a href="{{ Route::has('cimstyredash.catalogue.index') ? route('cimstyredash.catalogue.index') : '#' }}"><i class="fas fa-book"></i> Catalogue</a></li>
                <li><a href="{{ Route::has('cimstyredash.brands.index') ? route('cimstyredash.brands.index') : '#' }}"><i class="fas fa-tags"></i> Brands</a></li>
                <li><a href="{{ Route::has('cimstyredash.services.index') ? route('cimstyredash.services.index') : '#' }}"><i class="fas fa-cogs"></i> Services</a></li>
                <li><a href="{{ Route::has('cimstyredash.stock.index') ? route('cimstyredash.stock.index') : '#' }}"><i class="fas fa-warehouse"></i> Stock</a></li>
                <li><a href="{{ Route::has('cimstyredash.customers.index') ? route('cimstyredash.customers.index') : '#' }}"><i class="fas fa-users"></i> Customers</a></li>
                <li><a href="{{ Route::has('cimstyredash.branches.index') ? route('cimstyredash.branches.index') : '#' }}"><i class="fas fa-building"></i> Branches</a></li>
                <li><a href="{{ Route::has('cimstyredash.settings.index') ? route('cimstyredash.settings.index') : '#' }}"><i class="fas fa-sliders-h"></i> Settings</a></li>
            </ul>
        </li>
    </ul>
</li>
