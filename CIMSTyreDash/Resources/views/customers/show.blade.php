@extends('layouts.default')

@section('title', 'Customer Details')

@push('styles')
<style>
    .customer-info dt {
        font-weight: 600;
        color: #5a5c69;
    }
    .customer-info dd {
        margin-bottom: 0.75rem;
    }
    .status-badge {
        font-size: 0.75rem;
        padding: 0.3em 0.65em;
        border-radius: 0.35rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .badge-active {
        background-color: #28a745;
        color: #fff;
    }
    .badge-inactive {
        background-color: #dc3545;
        color: #fff;
    }
    .table th {
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Customer Details</h4>
                <p class="mb-0">{{ $customer->first_name }} {{ $customer->last_name }}</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item active">{{ $customer->first_name }} {{ $customer->last_name }}</li>
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

            {{-- Action Bar --}}
            <div class="mb-3 d-flex gap-2">
                <a href="{{ route('cimstyredash.customers.edit', $customer->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <form action="{{ route('cimstyredash.customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Delete
                    </button>
                </form>
                <a href="{{ route('cimstyredash.customers.index') }}" class="btn btn-outline-secondary ms-auto">
                    <i class="fas fa-arrow-left me-1"></i>Back to Customers
                </a>
            </div>

            {{-- Customer Info Card --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Customer Information
                    </h4>
                </div>
                <div class="card-body">
                    <dl class="row customer-info mb-0">
                        <dt class="col-sm-3">Name</dt>
                        <dd class="col-sm-9">{{ $customer->first_name }} {{ $customer->last_name }}</dd>

                        <dt class="col-sm-3">Company</dt>
                        <dd class="col-sm-9">{{ $customer->company_name ?? '-' }}</dd>

                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">
                            @if($customer->email)
                                <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                            @else
                                -
                            @endif
                        </dd>

                        <dt class="col-sm-3">Phone</dt>
                        <dd class="col-sm-9">{{ $customer->phone ?? '-' }}</dd>

                        <dt class="col-sm-3">Cell</dt>
                        <dd class="col-sm-9">{{ $customer->cell ?? '-' }}</dd>

                        <dt class="col-sm-3">Type</dt>
                        <dd class="col-sm-9">
                            <span class="badge bg-info">{{ ucfirst($customer->customer_type ?? '-') }}</span>
                        </dd>

                        <dt class="col-sm-3">Debtor Account</dt>
                        <dd class="col-sm-9">{{ $customer->debtor_account ?? '-' }}</dd>

                        <dt class="col-sm-3">VAT Number</dt>
                        <dd class="col-sm-9">{{ $customer->vat_number ?? '-' }}</dd>

                        <dt class="col-sm-3">Credit Limit</dt>
                        <dd class="col-sm-9">{{ $customer->credit_limit !== null ? number_format($customer->credit_limit, 2) : '-' }}</dd>

                        <dt class="col-sm-3">Balance</dt>
                        <dd class="col-sm-9">{{ $customer->balance !== null ? number_format($customer->balance, 2) : '-' }}</dd>

                        <dt class="col-sm-3">Address</dt>
                        <dd class="col-sm-9">
                            @if($customer->address || $customer->city || $customer->province || $customer->postal_code)
                                {{ $customer->address }}<br>
                                {{ implode(', ', array_filter([$customer->city, $customer->province, $customer->postal_code])) }}
                            @else
                                -
                            @endif
                        </dd>

                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9">{{ $customer->notes ?? '-' }}</dd>

                        <dt class="col-sm-3">Status</dt>
                        <dd class="col-sm-9">
                            @if($customer->is_active)
                                <span class="badge badge-active">Active</span>
                            @else
                                <span class="badge badge-inactive">Inactive</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            {{-- Vehicles Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-car me-2"></i>Vehicles
                    </h4>
                    <a href="{{ route('cimstyredash.customers.show', $customer->id) }}?add_vehicle=1" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add Vehicle
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Registration</th>
                                    <th>Make</th>
                                    <th>Model</th>
                                    <th>Year</th>
                                    <th>Current Tyre Size</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->vehicles as $vehicle)
                                    <tr>
                                        <td>{{ $vehicle->registration }}</td>
                                        <td>{{ $vehicle->make ?? '-' }}</td>
                                        <td>{{ $vehicle->model ?? '-' }}</td>
                                        <td>{{ $vehicle->year ?? '-' }}</td>
                                        <td>{{ $vehicle->current_tyre_size ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-car fa-2x mb-2 d-block opacity-50"></i>
                                            No vehicles registered for this customer.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Quotes History Card --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Quotes History
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Quote #</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Branch</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotes as $quote)
                                    @php
                                        $quoteStatusColors = [
                                            'draft'    => 'warning',
                                            'sent'     => 'info',
                                            'accepted' => 'success',
                                            'declined' => 'danger',
                                            'expired'  => 'secondary',
                                            'invoiced' => 'primary',
                                        ];
                                        $qColor = $quoteStatusColors[$quote->status] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('cimstyredash.quotes.show', $quote->id) }}">
                                                {{ $quote->quote_number }}
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($quote->quote_date)->format('d M Y') }}</td>
                                        <td><span class="badge bg-{{ $qColor }} status-badge">{{ $quote->status }}</span></td>
                                        <td>{{ $quote->branch->name ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($quote->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-file-invoice fa-2x mb-2 d-block opacity-50"></i>
                                            No quotes found for this customer.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($quotes->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $quotes->firstItem() }} to {{ $quotes->lastItem() }} of {{ $quotes->total() }} quotes
                            </small>
                            {{ $quotes->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- Job Cards History Card --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-wrench me-2"></i>Job Cards History
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th>Job Card #</th>
                                    <th>Date</th>
                                    <th>Vehicle Reg</th>
                                    <th>Status</th>
                                    <th>Branch</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobCards as $jobCard)
                                    @php
                                        $jobStatusColors = [
                                            'open'           => 'warning',
                                            'in_progress'    => 'info',
                                            'awaiting_parts' => 'secondary',
                                            'complete'       => 'success',
                                            'invoiced'       => 'primary',
                                            'cancelled'      => 'danger',
                                        ];
                                        $jColor = $jobStatusColors[$jobCard->status] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ route('cimstyredash.jobcards.show', $jobCard->id) }}">
                                                {{ $jobCard->job_card_number }}
                                            </a>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($jobCard->job_date)->format('d M Y') }}</td>
                                        <td>{{ $jobCard->vehicle->registration ?? '-' }}</td>
                                        <td><span class="badge bg-{{ $jColor }} status-badge">{{ str_replace('_', ' ', $jobCard->status) }}</span></td>
                                        <td>{{ $jobCard->branch->name ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($jobCard->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-wrench fa-2x mb-2 d-block opacity-50"></i>
                                            No job cards found for this customer.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($jobCards->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Showing {{ $jobCards->firstItem() }} to {{ $jobCards->lastItem() }} of {{ $jobCards->total() }} job cards
                            </small>
                            {{ $jobCards->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection
