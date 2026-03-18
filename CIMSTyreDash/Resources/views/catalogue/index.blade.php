@extends('layouts.default')

@section('title', 'Tyre Catalogue')

@push('styles')
<style>
    .filter-bar .form-group {
        margin-bottom: 0;
    }
    .table th {
        white-space: nowrap;
    }
    .badge-active {
        background-color: #28a745;
        color: #fff;
    }
    .badge-inactive {
        background-color: #dc3545;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Tyre Catalogue</h4>
                <p class="mb-0">Manage your product catalogue</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Catalogue</li>
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

    <div class="row">
        {{-- Sidebar --}}
        <div class="col-xl-3 col-xxl-4 col-lg-4">
            @include('cimstyredash::partials.sidebar', ['activePage' => 'catalogue'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-xxl-8 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-book me-2"></i>Products
                    </h4>
                    <a href="{{ route('cimstyredash.catalogue.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Product
                    </a>
                </div>
                <div class="card-body">
                    {{-- Filter Bar --}}
                    <form method="GET" action="{{ route('cimstyredash.catalogue.index') }}" class="mb-4">
                        <div class="row filter-bar g-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="brand_id" class="form-control form-select">
                                        <option value="">All Brands</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="category_id" class="form-control form-select">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Products Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Product Code</th>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Size</th>
                                    <th>Category</th>
                                    <th class="text-end">Cost Price</th>
                                    <th class="text-end">Sell Price</th>
                                    <th class="text-end">Markup%</th>
                                    <th class="text-center">Active</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                    <tr>
                                        <td>{{ $product->product_code }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($product->brand && $product->brand->logo_url)
                                                    <span class="brand-logo-box"><img src="{{ asset('modules/cimstyredash/brands/' . $product->brand->logo_url) }}" alt="{{ $product->brand->name }}" onerror="this.parentElement.style.display='none'"></span>
                                                @endif
                                                {{ $product->brand->name ?? '-' }}
                                            </div>
                                        </td>
                                        <td>{{ $product->model_name }}</td>
                                        <td>{{ $product->size->full_size ?? '-' }}</td>
                                        <td>{{ $product->category->name ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($product->cost_price, 2) }}</td>
                                        <td class="text-end">{{ number_format($product->sell_price, 2) }}</td>
                                        <td class="text-end">{{ number_format($product->markup_pct, 1) }}%</td>
                                        <td class="text-center">
                                            @if($product->is_active)
                                                <span class="badge badge-active">Active</span>
                                            @else
                                                <span class="badge badge-inactive">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('cimstyredash.catalogue.edit', $product->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('cimstyredash.catalogue.destroy', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No products found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
                            </small>
                            {{ $products->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
