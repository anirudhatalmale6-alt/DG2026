@extends('layouts.default')

@section('title', 'All Quotes')

@push('styles')
<style>
    .badge-status { font-size: 0.8em; padding: 5px 10px; }
    .filter-bar .form-control,
    .filter-bar .form-select { font-size: 0.9em; }
    .table th { white-space: nowrap; font-size: 0.85em; text-transform: uppercase; color: #6c757d; }
    .table td { vertical-align: middle; }
    .quote-number { font-weight: 600; color: #17A2B8; }
    .btn-action { padding: 4px 8px; font-size: 0.8em; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>All Quotes</h4>
                <span class="ml-1">TyreDash / Quotes</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item active">Quotes</li>
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
        <div class="col-xl-3">
            @include('cimstyredash::partials.sidebar', ['activePage' => 'quotes'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9">
            {{-- Filter Bar --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><i class="fas fa-filter me-2"></i>Filters</h4>
                </div>
                <div class="card-body filter-bar">
                    <form method="GET" action="{{ route('cimstyredash.quotes.index') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Branch</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">All Branches</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Quote #, customer..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                        @if(request()->hasAny(['status', 'branch_id', 'date_from', 'date_to', 'search']))
                            <div class="mt-2">
                                <a href="{{ route('cimstyredash.quotes.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Clear Filters
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Quotes Table --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title"><i class="fas fa-list me-2"></i>Quotes ({{ $quotes->total() }})</h4>
                    <a href="{{ route('cimstyredash.quotes.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> New Quote
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Quote #</th>
                                    <th>Customer</th>
                                    <th>Branch</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotes as $quote)
                                    <tr>
                                        <td>
                                            <a href="{{ route('cimstyredash.quotes.show', $quote->id) }}" class="quote-number">
                                                {{ $quote->quote_number }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($quote->customer)
                                                {{ $quote->customer->full_name ?: $quote->customer->company_name }}
                                            @else
                                                <span class="text-muted">Walk-in</span>
                                            @endif
                                        </td>
                                        <td>{{ $quote->branch->name ?? '-' }}</td>
                                        <td>{{ $quote->quote_date->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $badgeMap = [
                                                    'draft' => 'warning',
                                                    'sent' => 'info',
                                                    'accepted' => 'success',
                                                    'declined' => 'danger',
                                                    'expired' => 'secondary',
                                                    'invoiced' => 'primary',
                                                ];
                                                $badgeClass = $badgeMap[$quote->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }} badge-status">
                                                {{ ucfirst($quote->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold">{{ number_format($quote->total_amount, 2) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('cimstyredash.quotes.show', $quote->id) }}" class="btn btn-info btn-action" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($quote->is_editable)
                                                <a href="{{ route('cimstyredash.quotes.edit', $quote->id) }}" class="btn btn-warning btn-action" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($quote->status !== 'invoiced')
                                                <form action="{{ route('cimstyredash.quotes.destroy', $quote->id) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-action" title="Delete" onclick="return confirm('Are you sure you want to delete quote {{ $quote->quote_number }}? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-file-invoice fa-3x mb-3 d-block"></i>
                                            No quotes found. <a href="{{ route('cimstyredash.quotes.create') }}">Create your first quote</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($quotes->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $quotes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
