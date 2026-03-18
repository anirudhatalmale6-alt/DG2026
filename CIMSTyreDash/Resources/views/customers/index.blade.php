@extends('layouts.default')

@section('title', 'Customers')

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
                <h4>Customers</h4>
                <p class="mb-0">Manage your customer database</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item active">Customers</li>
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
            @include('cimstyredash::partials.sidebar', ['activePage' => 'customers'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-lg-8">

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>Customers
                    </h4>
                    <a href="{{ route('cimstyredash.customers.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Customer
                    </a>
                </div>
                <div class="card-body">
                    {{-- Filter Bar --}}
                    <form method="GET" action="{{ route('cimstyredash.customers.index') }}" class="mb-4">
                        <div class="row filter-bar g-2">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="customer_type" class="form-control form-select">
                                        <option value="">All Types</option>
                                        @foreach($customerTypes as $type)
                                            <option value="{{ $type }}" {{ request('customer_type') == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search name, company, phone, email..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="active_only" id="active_only" value="1" {{ request('active_only') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="active_only">Active Only</label>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Customers Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Company</th>
                                    <th>Phone / Cell</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th class="text-center">Vehicles</th>
                                    <th class="text-center">Quotes</th>
                                    <th class="text-center">JC</th>
                                    <th class="text-center">Active</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                    <tr>
                                        <td>
                                            <a href="{{ route('cimstyredash.customers.show', $customer->id) }}">
                                                {{ $customer->first_name }} {{ $customer->last_name }}
                                            </a>
                                        </td>
                                        <td>{{ $customer->company_name ?? '-' }}</td>
                                        <td>
                                            {{ $customer->phone ?? '-' }}
                                            @if($customer->cell)
                                                <br><small class="text-muted">{{ $customer->cell }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $customer->email ?? '-' }}</td>
                                        <td><span class="badge bg-info">{{ ucfirst($customer->customer_type ?? '-') }}</span></td>
                                        <td class="text-center">{{ $customer->vehicles_count }}</td>
                                        <td class="text-center">{{ $customer->quotes_count }}</td>
                                        <td class="text-center">{{ $customer->job_cards_count }}</td>
                                        <td class="text-center">
                                            @if($customer->is_active)
                                                <span class="badge badge-active">Active</span>
                                            @else
                                                <span class="badge badge-inactive">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('cimstyredash.customers.show', $customer->id) }}" class="btn btn-sm btn-outline-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('cimstyredash.customers.edit', $customer->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('cimstyredash.customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
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
                                            No customers found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Pagination --}}
                @if($customers->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} customers
                            </small>
                            {{ $customers->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection
