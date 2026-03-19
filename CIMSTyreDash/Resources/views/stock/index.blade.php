@extends('layouts.default')

@section('title', 'Stock Management')

@push('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 10px;
        color: #fff;
        transition: transform 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-3px);
    }
    .stat-card .card-body {
        padding: 1.25rem;
    }
    .stat-card .stat-icon {
        font-size: 2.5rem;
        opacity: 0.3;
        position: absolute;
        right: 1.25rem;
        top: 1rem;
    }
    .stat-card .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .stat-card .stat-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.85;
    }
    .bg-gradient-info    { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); }
    .bg-gradient-danger  { background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); }
    .filter-bar .form-group {
        margin-bottom: 0;
    }
    .table th {
        white-space: nowrap;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.3em 0.65em;
        border-radius: 0.35rem;
        font-weight: 600;
        text-transform: capitalize;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Stock Management</h4>
                <p class="mb-0">Monitor and manage stock levels across branches</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item active">Stock</li>
            </ol>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 2-Column Layout --}}
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-xl-3 col-lg-4">
            @include('cimstyredash::partials.sidebar', ['activePage' => 'stock'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-lg-8">

            {{-- Stat Cards --}}
            <div class="row">
                <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card bg-gradient-info">
                        <div class="card-body position-relative">
                            <i class="fas fa-coins stat-icon"></i>
                            <div class="stat-value">{{ $currencySymbol }}{{ number_format($totalStockValue, 2) }}</div>
                            <div class="stat-label">Total Stock Value</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card bg-gradient-warning">
                        <div class="card-body position-relative">
                            <i class="fas fa-exclamation-triangle stat-icon"></i>
                            <div class="stat-value">{{ number_format($lowStockCount) }}</div>
                            <div class="stat-label">Low Stock Alerts</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card bg-gradient-danger">
                        <div class="card-body position-relative">
                            <i class="fas fa-times-circle stat-icon"></i>
                            <div class="stat-value">{{ number_format($outOfStockCount) }}</div>
                            <div class="stat-label">Out of Stock</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock Table Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-warehouse me-2"></i>Stock Records
                    </h4>
                    <a href="{{ route('cimstyredash.stock.transfer') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-exchange-alt me-1"></i>Stock Transfer
                    </a>
                </div>
                <div class="card-body">
                    {{-- Filter Bar --}}
                    <form method="GET" action="{{ route('cimstyredash.stock.index') }}" class="mb-4">
                        <div class="row filter-bar g-2">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="branch_id" class="form-control form-select">
                                        <option value="">All Branches</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="size_id" class="form-control form-select">
                                        <option value="">All Sizes</option>
                                        @foreach($sizes as $size)
                                            <option value="{{ $size->id }}" {{ request('size_id') == $size->id ? 'selected' : '' }}>
                                                {{ $size->full_size }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="low_stock" id="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="low_stock">Low Stock</label>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="out_of_stock" id="out_of_stock" value="1" {{ request('out_of_stock') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="out_of_stock">Out of Stock</label>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Stock Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th style="width:60px">Image</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Size</th>
                                    <th>Branch</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Min Qty</th>
                                    <th class="text-center">Reserved</th>
                                    <th class="text-center">Available</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stockRecords as $stock)
                                    @php
                                        $available = $stock->quantity - $stock->reserved_quantity;
                                        if ($stock->quantity <= 0) {
                                            $statusLabel = 'Out';
                                            $statusColor = 'danger';
                                        } elseif ($stock->quantity <= $stock->min_quantity) {
                                            $statusLabel = 'Low';
                                            $statusColor = 'warning';
                                        } else {
                                            $statusLabel = 'OK';
                                            $statusColor = 'success';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-center">
                                            @if($stock->product && $stock->product->image_url)
                                                <span class="product-img-box" onclick="showProductImage(this)"><img src="{{ asset('modules/cimstyredash/products/' . $stock->product->image_url) }}" alt="{{ $stock->product->model_name }}" onerror="this.parentElement.style.display='none'"></span>
                                            @endif
                                        </td>
                                        <td>{{ $stock->product->product_code ?? '-' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($stock->product && $stock->product->brand && $stock->product->brand->logo_url)
                                                    <span class="brand-logo-box"><img src="{{ asset('modules/cimstyredash/brands/' . $stock->product->brand->logo_url) }}" alt="{{ $stock->product->brand->name }}" onerror="this.parentElement.style.display='none'"></span>
                                                @endif
                                                {{ $stock->product->brand->name ?? '-' }}
                                            </div>
                                        </td>
                                        <td>{{ $stock->product->model_name ?? '-' }}</td>
                                        <td>{{ $stock->product->size->full_size ?? '-' }}</td>
                                        <td>{{ $stock->branch->name ?? '-' }}</td>
                                        <td class="text-center">{{ $stock->quantity }}</td>
                                        <td class="text-center">{{ $stock->min_quantity }}</td>
                                        <td class="text-center">{{ $stock->reserved_quantity }}</td>
                                        <td class="text-center">{{ $available }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $statusColor }} status-badge">{{ $statusLabel }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('cimstyredash.stock.adjust', $stock->id) }}" class="btn btn-sm btn-outline-primary" title="Adjust Stock">
                                                <i class="fas fa-sliders-h"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No stock records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pagination --}}
                @if($stockRecords->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $stockRecords->firstItem() }} to {{ $stockRecords->lastItem() }} of {{ $stockRecords->total() }} records
                            </small>
                            {{ $stockRecords->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection

@push('scripts')
<script>
function showProductImage(el) {
    var img = el.querySelector('img');
    if (!img) return;
    var overlay = document.createElement('div');
    overlay.className = 'product-img-modal-overlay';
    overlay.innerHTML = '<img src="' + img.src + '" alt="Product Image">';
    overlay.addEventListener('click', function() { overlay.remove(); });
    document.body.appendChild(overlay);
}
</script>
@endpush
