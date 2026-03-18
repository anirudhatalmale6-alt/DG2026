@extends('layouts.default')

@section('title', 'Job Cards')

@push('styles')
<style>
    .status-badge {
        font-size: 0.75rem;
        padding: 0.3em 0.65em;
        border-radius: 0.35rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Job Cards</h4>
                <p class="mb-0">Manage all job cards</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Job Cards</a></li>
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
            @include('cimstyredash::partials.sidebar', ['activePage' => 'jobcards'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-lg-8">

            {{-- Filter Bar --}}
            <div class="card filter-card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-filter me-2"></i>Filters
                    </h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cimstyredash.jobcards.index') }}">
                        <div class="row g-3">
                            {{-- Status --}}
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('_', ' ', $status)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Branch --}}
                            <div class="col-md-3 col-sm-6">
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

                            {{-- Technician --}}
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Technician</label>
                                <input type="text" name="technician" class="form-control" placeholder="Technician name" value="{{ request('technician') }}">
                            </div>

                            {{-- Date From --}}
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>

                            {{-- Date To --}}
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>

                            {{-- Search --}}
                            <div class="col-md-6 col-sm-6">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Search by JC#, customer, vehicle..." value="{{ request('search') }}">
                            </div>

                            {{-- Filter Button --}}
                            <div class="col-md-3 col-sm-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <a href="{{ route('cimstyredash.jobcards.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Job Cards Table --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-wrench me-2"></i>Job Cards
                    </h4>
                    <a href="{{ route('cimstyredash.jobcards.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Create Job Card
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>JC#</th>
                                    <th>Customer</th>
                                    <th>Vehicle</th>
                                    <th>Branch</th>
                                    <th>Date</th>
                                    <th>Technician</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobCards as $jobCard)
                                    <tr>
                                        <td>
                                            <a href="{{ route('cimstyredash.jobcards.show', $jobCard->id) }}">
                                                {{ $jobCard->job_card_number }}
                                            </a>
                                        </td>
                                        <td>{{ $jobCard->customer->full_name ?? 'N/A' }}</td>
                                        <td>{{ $jobCard->vehicle->registration ?? 'N/A' }}</td>
                                        <td>{{ $jobCard->branch->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($jobCard->job_date)->format('d M Y') }}</td>
                                        <td>{{ $jobCard->technician_name ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'open'           => 'warning',
                                                    'in_progress'    => 'info',
                                                    'awaiting_parts' => 'secondary',
                                                    'complete'       => 'success',
                                                    'invoiced'       => 'primary',
                                                    'cancelled'      => 'danger',
                                                ];
                                                $color = $statusColors[$jobCard->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }} status-badge">
                                                {{ ucwords(str_replace('_', ' ', $jobCard->status)) }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format($jobCard->total_amount, 2) }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('cimstyredash.jobcards.show', $jobCard->id) }}" class="btn btn-info btn-sm me-1" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('cimstyredash.jobcards.edit', $jobCard->id) }}" class="btn btn-warning btn-sm me-1" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('cimstyredash.jobcards.destroy', $jobCard->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this job card?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No job cards found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if($jobCards->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $jobCards->firstItem() }} to {{ $jobCards->lastItem() }} of {{ $jobCards->total() }} results
                            </small>
                            {{ $jobCards->appends(request()->query())->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection
