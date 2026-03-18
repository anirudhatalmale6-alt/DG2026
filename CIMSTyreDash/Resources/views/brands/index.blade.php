@extends('layouts.default')

@section('title', 'Brands')

@push('styles')
<style>
    .badge-active {
        background-color: #28a745;
        color: #fff;
    }
    .badge-inactive {
        background-color: #dc3545;
        color: #fff;
    }
    .btn-toggle {
        min-width: 32px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Brands</h4>
                <p class="mb-0">Manage tyre brands</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Brands</li>
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
            @include('cimstyredash::partials.sidebar', ['activePage' => 'brands'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-xxl-8 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-copyright me-2"></i>Brands
                    </h4>
                    <a href="{{ route('cimstyredash.brands.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Brand
                    </a>
                </div>
                <div class="card-body">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('cimstyredash.brands.index') }}" class="mb-4">
                        <div class="row g-2">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control" placeholder="Search brands by name, code, or country..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Brands Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Country</th>
                                    <th class="text-center">Products</th>
                                    <th class="text-center">Active</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($brand->logo_url)
                                                    <span class="brand-logo-box"><img src="{{ asset('modules/cimstyredash/brands/' . $brand->logo_url) }}" alt="{{ $brand->name }}" onerror="this.parentElement.style.display='none'"></span>
                                                @endif
                                                <strong>{{ $brand->name }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $brand->code }}</td>
                                        <td>{{ $brand->country ?? '-' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $brand->products_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($brand->is_active)
                                                <span class="badge badge-active">Active</span>
                                            @else
                                                <span class="badge badge-inactive">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1">
                                                {{-- Edit --}}
                                                <a href="{{ route('cimstyredash.brands.edit', $brand->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- Activate / Deactivate Toggle --}}
                                                @if($brand->is_active)
                                                    <form action="{{ route('cimstyredash.brands.deactivate', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate this brand?');">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-warning btn-toggle" title="Deactivate">
                                                            <i class="fas fa-toggle-off"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('cimstyredash.brands.activate', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Activate this brand?');">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-outline-success btn-toggle" title="Activate">
                                                            <i class="fas fa-toggle-on"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Delete --}}
                                                <form action="{{ route('cimstyredash.brands.destroy', $brand->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this brand? This cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No brands found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pagination --}}
                @if($brands->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $brands->firstItem() }} to {{ $brands->lastItem() }} of {{ $brands->total() }} brands
                            </small>
                            {{ $brands->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
