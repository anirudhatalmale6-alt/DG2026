{{-- TyreDash Module Sidebar Navigation --}}
@push('styles')
    @include('cimstyredash::partials.brand-logo-styles')
@endpush
<div class="email-left-box email-left-body">
    <div class="generic-width px-0 mb-5 mt-4 mt-sm-0">
        <div class="p-0">
            <a href="{{ route('cimstyredash.quotes.create') }}" class="btn btn-primary btn-block">
                <i class="fas fa-plus me-2"></i>New Quote
            </a>
        </div>

        <div class="mail-list rounded mt-4">
            <a href="{{ route('cimstyredash.dashboard') }}" class="list-group-item {{ ($activePage ?? '') == 'dashboard' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt font-18 align-middle me-2"></i> Dashboard
            </a>
            <a href="{{ route('cimstyredash.catalogue.index') }}" class="list-group-item {{ ($activePage ?? '') == 'size-search' ? 'active' : '' }}">
                <i class="fas fa-search font-18 align-middle me-2"></i> Size Search
            </a>
            <a href="{{ route('cimstyredash.quotes.create') }}" class="list-group-item {{ ($activePage ?? '') == 'new-quote' ? 'active' : '' }}">
                <i class="fas fa-file-invoice font-18 align-middle me-2"></i> New Quote
            </a>
            <a href="{{ route('cimstyredash.quotes.index') }}" class="list-group-item {{ ($activePage ?? '') == 'quotes' ? 'active' : '' }}">
                <i class="fas fa-list font-18 align-middle me-2"></i> All Quotes
            </a>
            <a href="{{ route('cimstyredash.jobcards.index') }}" class="list-group-item {{ ($activePage ?? '') == 'jobcards' ? 'active' : '' }}">
                <i class="fas fa-wrench font-18 align-middle me-2"></i> Job Cards
            </a>
        </div>

        <div class="mail-list rounded overflow-hidden mt-4">
            <div class="intro-title d-flex justify-content-between mt-0">
                <h5>Manage</h5>
            </div>
            <a href="{{ route('cimstyredash.catalogue.index') }}" class="list-group-item {{ ($activePage ?? '') == 'catalogue' ? 'active' : '' }}">
                <span class="icon-warning"><i class="fa fa-circle"></i></span> Catalogue
            </a>
            <a href="{{ route('cimstyredash.brands.index') }}" class="list-group-item {{ ($activePage ?? '') == 'brands' ? 'active' : '' }}">
                <span class="icon-success"><i class="fa fa-circle"></i></span> Brands
            </a>
            <a href="{{ route('cimstyredash.services.index') }}" class="list-group-item {{ ($activePage ?? '') == 'services' ? 'active' : '' }}">
                <span class="icon-info"><i class="fa fa-circle"></i></span> Services
            </a>
            <a href="{{ route('cimstyredash.stock.index') }}" class="list-group-item {{ ($activePage ?? '') == 'stock' ? 'active' : '' }}">
                <span class="icon-primary"><i class="fa fa-circle"></i></span> Stock
            </a>
            <a href="{{ route('cimstyredash.customers.index') }}" class="list-group-item {{ ($activePage ?? '') == 'customers' ? 'active' : '' }}">
                <span class="icon-danger"><i class="fa fa-circle"></i></span> Customers
            </a>
            <a href="{{ route('cimstyredash.branches.index') }}" class="list-group-item {{ ($activePage ?? '') == 'branches' ? 'active' : '' }}">
                <span class="icon-secondary"><i class="fa fa-circle"></i></span> Branches
            </a>
            <a href="{{ route('cimstyredash.settings.index') }}" class="list-group-item {{ ($activePage ?? '') == 'settings' ? 'active' : '' }}">
                <span class="icon-dark"><i class="fa fa-circle"></i></span> Settings
            </a>
        </div>
    </div>
</div>
