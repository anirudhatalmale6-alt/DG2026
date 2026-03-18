@extends('layouts.default')

@section('title', 'Job Card - ' . $jobCard->job_card_number)

@push('styles')
<style>
    .status-badge {
        font-size: 0.75rem;
        padding: 0.3em 0.65em;
        border-radius: 0.35rem;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-badge-lg {
        font-size: 0.9rem;
        padding: 0.4em 0.8em;
    }
    .info-label {
        font-weight: 600;
        color: #6c757d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-value {
        font-size: 1rem;
        color: #333;
    }
    .action-bar .btn {
        margin-right: 0.35rem;
        margin-bottom: 0.35rem;
    }
    .totals-row td {
        font-weight: 700;
        font-size: 1.05rem;
    }
    .completed-badge {
        font-size: 0.7rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Job Card: {{ $jobCard->job_card_number }}</h4>
                <p class="mb-0">View job card details</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.jobcards.index') }}">Job Cards</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $jobCard->job_card_number }}</a></li>
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

            {{-- Action Bar --}}
            <div class="card mb-4">
                <div class="card-body action-bar d-flex flex-wrap align-items-center">
                    <a href="{{ route('cimstyredash.jobcards.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Back to List
                    </a>

                    {{-- Edit --}}
                    @if($jobCard->is_editable)
                        <a href="{{ route('cimstyredash.jobcards.edit', $jobCard->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                    @endif

                    {{-- Status Transitions --}}
                    @if($jobCard->status === 'open')
                        <form action="{{ route('cimstyredash.jobcards.status', $jobCard->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="fas fa-play me-1"></i>Start (In Progress)
                            </button>
                        </form>
                    @endif

                    @if($jobCard->status === 'in_progress')
                        <form action="{{ route('cimstyredash.jobcards.status', $jobCard->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="awaiting_parts">
                            <button type="submit" class="btn btn-secondary btn-sm">
                                <i class="fas fa-pause me-1"></i>Awaiting Parts
                            </button>
                        </form>
                        <form action="{{ route('cimstyredash.jobcards.status', $jobCard->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="complete">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check me-1"></i>Complete
                            </button>
                        </form>
                    @endif

                    @if($jobCard->status === 'awaiting_parts')
                        <form action="{{ route('cimstyredash.jobcards.status', $jobCard->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="in_progress">
                            <button type="submit" class="btn btn-info btn-sm">
                                <i class="fas fa-play me-1"></i>Resume (In Progress)
                            </button>
                        </form>
                        <form action="{{ route('cimstyredash.jobcards.status', $jobCard->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="complete">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-check me-1"></i>Complete
                            </button>
                        </form>
                    @endif

                    {{-- Close to Invoice --}}
                    @if($jobCard->can_invoice)
                        <form action="{{ route('cimstyredash.jobcards.close-invoice', $jobCard->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" onclick="return confirm('Close this job card and create an invoice?');">
                                <i class="fas fa-file-invoice-dollar me-1"></i>Close to Invoice
                            </button>
                        </form>
                    @endif

                    {{-- Delete --}}
                    <form action="{{ route('cimstyredash.jobcards.destroy', $jobCard->id) }}" method="POST" class="d-inline ms-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this job card?');">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </form>
                </div>
            </div>

            {{-- Header Info Card --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-wrench me-2"></i>{{ $jobCard->job_card_number }}
                    </h4>
                    <span class="badge bg-{{ $color }} status-badge status-badge-lg">
                        {{ ucwords(str_replace('_', ' ', $jobCard->status)) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4 col-sm-6">
                            <div class="info-label">Job Date</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($jobCard->job_date)->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="info-label">Customer</div>
                            <div class="info-value">
                                @if($jobCard->customer)
                                    <a href="{{ route('cimstyredash.customers.show', $jobCard->customer->id) }}">
                                        {{ $jobCard->customer->full_name }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="info-label">Vehicle</div>
                            <div class="info-value">
                                @if($jobCard->vehicle)
                                    {{ $jobCard->vehicle->registration }}
                                    <small class="text-muted d-block">{{ $jobCard->vehicle->make }} {{ $jobCard->vehicle->model }}</small>
                                @else
                                    N/A
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="info-label">Branch</div>
                            <div class="info-value">{{ $jobCard->branch->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="info-label">Technician</div>
                            <div class="info-value">{{ $jobCard->technician_name ?? '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="info-label">Linked Quote</div>
                            <div class="info-value">
                                @if($jobCard->quote)
                                    <a href="{{ route('cimstyredash.quotes.show', $jobCard->quote->id) }}">
                                        {{ $jobCard->quote->quote_number }}
                                    </a>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="info-label">Odometer In</div>
                            <div class="info-value">{{ $jobCard->odometer_in ? number_format($jobCard->odometer_in) . ' km' : '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="info-label">Odometer Out</div>
                            <div class="info-value">{{ $jobCard->odometer_out ? number_format($jobCard->odometer_out) . ' km' : '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tyres Table --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-circle-notch me-2"></i>Tyres
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Brand</th>
                                    <th>Model</th>
                                    <th>Size</th>
                                    <th class="text-center">Qty</th>
                                    <th>Position</th>
                                    <th>Serial New</th>
                                    <th>Serial Old</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Line Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobCard->jobCardTyres as $tyre)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($tyre->product && $tyre->product->brand && $tyre->product->brand->logo_url)
                                                    <span class="brand-logo-box"><img src="{{ asset('modules/cimstyredash/brands/' . $tyre->product->brand->logo_url) }}" alt="{{ $tyre->product->brand->name }}" onerror="this.parentElement.style.display='none'"></span>
                                                @endif
                                                {{ $tyre->product->brand->name ?? '-' }}
                                            </div>
                                        </td>
                                        <td>{{ $tyre->product->model ?? '-' }}</td>
                                        <td>{{ $tyre->product->size->full_size ?? '-' }}</td>
                                        <td class="text-center">{{ $tyre->quantity }}</td>
                                        <td>{{ $tyre->position ?? '-' }}</td>
                                        <td>{{ $tyre->serial_new ?? '-' }}</td>
                                        <td>{{ $tyre->serial_old ?? '-' }}</td>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($tyre->unit_price, 2) }}</td>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($tyre->line_total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-3">No tyres recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($jobCard->jobCardTyres->isNotEmpty())
                                <tfoot>
                                    <tr class="totals-row">
                                        <td colspan="8" class="text-end">Tyres Total:</td>
                                        <td class="text-end">
                                            {{ $currencySymbol }}{{ number_format($jobCard->jobCardTyres->sum('line_total'), 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- Services Table --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>Services
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Code</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Line Total</th>
                                    <th class="text-center">Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jobCard->jobCardServices as $svc)
                                    <tr>
                                        <td>{{ $svc->service->name ?? '-' }}</td>
                                        <td>{{ $svc->service->code ?? '-' }}</td>
                                        <td class="text-center">{{ $svc->quantity }}</td>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($svc->unit_price, 2) }}</td>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($svc->line_total, 2) }}</td>
                                        <td class="text-center">
                                            @if($svc->is_completed)
                                                <span class="badge bg-success completed-badge"><i class="fas fa-check"></i> Yes</span>
                                            @else
                                                <span class="badge bg-secondary completed-badge"><i class="fas fa-times"></i> No</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No services recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($jobCard->jobCardServices->isNotEmpty())
                                <tfoot>
                                    <tr class="totals-row">
                                        <td colspan="4" class="text-end">Services Total:</td>
                                        <td class="text-end">
                                            {{ $currencySymbol }}{{ number_format($jobCard->jobCardServices->sum('line_total'), 2) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            {{-- Grand Total --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="text-end fw-semibold">Tyres Total:</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($jobCard->jobCardTyres->sum('line_total'), 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-semibold">Services Total:</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($jobCard->jobCardServices->sum('line_total'), 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-end fw-bold fs-5">Grand Total:</td>
                                    <td class="text-end fw-bold fs-5">{{ $currencySymbol }}{{ number_format($jobCard->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-sticky-note me-2"></i>Notes
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-label">Vehicle Condition Notes</div>
                            <div class="info-value">
                                {{ $jobCard->vehicle_condition_notes ?: 'No notes recorded.' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Work Notes</div>
                            <div class="info-value">
                                {{ $jobCard->work_notes ?: 'No notes recorded.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timestamps --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>Timestamps
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <div class="info-label">Started At</div>
                            <div class="info-value">
                                {{ $jobCard->started_at ? \Carbon\Carbon::parse($jobCard->started_at)->format('d M Y H:i') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-label">Completed At</div>
                            <div class="info-value">
                                {{ $jobCard->completed_at ? \Carbon\Carbon::parse($jobCard->completed_at)->format('d M Y H:i') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-label">Created</div>
                            <div class="info-value">
                                {{ $jobCard->created_at ? $jobCard->created_at->format('d M Y H:i') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="info-label">Last Updated</div>
                            <div class="info-value">
                                {{ $jobCard->updated_at ? $jobCard->updated_at->format('d M Y H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection
