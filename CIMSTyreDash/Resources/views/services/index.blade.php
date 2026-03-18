@extends('layouts.default')

@section('title', 'Services')

@push('styles')
<style>
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
    .badge-yes {
        background-color: #17a2b8;
        color: #fff;
    }
    .badge-no {
        background-color: #6c757d;
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
                <h4>Services</h4>
                <p class="mb-0">Manage service offerings</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item active">Services</li>
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
            @include('cimstyredash::partials.sidebar', ['activePage' => 'services'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-lg-8">

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>Services
                    </h4>
                    <a href="{{ route('cimstyredash.services.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Service
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-center">Per Tyre</th>
                                    <th class="text-center">Active</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                    <tr>
                                        <td>{{ $service->name }}</td>
                                        <td>{{ $service->code }}</td>
                                        <td class="text-end">{{ number_format($service->price, 2) }}</td>
                                        <td class="text-center">
                                            @if($service->price_per_tyre)
                                                <span class="badge badge-yes">Yes</span>
                                            @else
                                                <span class="badge badge-no">No</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($service->is_active)
                                                <span class="badge badge-active">Active</span>
                                            @else
                                                <span class="badge badge-inactive">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('cimstyredash.services.edit', $service->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($service->is_active)
                                                <form action="{{ route('cimstyredash.services.deactivate', $service->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate this service?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Deactivate">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('cimstyredash.services.activate', $service->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Activate this service?');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Activate">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('cimstyredash.services.destroy', $service->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this service?');">
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
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No services found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pagination --}}
                @if($services->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $services->firstItem() }} to {{ $services->lastItem() }} of {{ $services->total() }} services
                            </small>
                            {{ $services->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection
