@extends('layouts.default')

@section('title', 'Quote ' . $quote->quote_number)

@push('styles')
<style>
    .badge-status { font-size: 0.85em; padding: 5px 12px; }
    .quote-header-card .detail-label { font-weight: 600; color: #6c757d; font-size: 0.85em; text-transform: uppercase; }
    .quote-header-card .detail-value { font-size: 1em; color: #333; }
    .action-bar .btn { margin-right: 5px; margin-bottom: 5px; }
    .nav-tabs .nav-link { font-weight: 500; }
    .nav-tabs .nav-link.active { border-bottom: 3px solid #17A2B8; font-weight: 600; }
    .option-selected { background-color: #d4edda !important; border-left: 4px solid #28a745; }
    .total-row td { font-weight: 700; font-size: 1.05em; }
    .notes-section { background: #f8f9fa; border-radius: 8px; padding: 15px; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Quote {{ $quote->quote_number }}</h4>
                <span class="ml-1">TyreDash / Quotes / View</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.quotes.index') }}">Quotes</a></li>
                <li class="breadcrumb-item active">{{ $quote->quote_number }}</li>
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
            {{-- Action Bar --}}
            <div class="card">
                <div class="card-body action-bar d-flex flex-wrap align-items-center">
                    {{-- Edit Button --}}
                    @if($quote->is_editable)
                        <a href="{{ route('cimstyredash.quotes.edit', $quote->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                    @endif

                    {{-- Status Change Buttons --}}
                    @php
                        $transitions = [
                            'draft'    => ['sent' => ['label' => 'Mark as Sent', 'class' => 'btn-info', 'icon' => 'fa-paper-plane'], 'declined' => ['label' => 'Decline', 'class' => 'btn-danger', 'icon' => 'fa-times']],
                            'sent'     => ['accepted' => ['label' => 'Accept', 'class' => 'btn-success', 'icon' => 'fa-check'], 'declined' => ['label' => 'Decline', 'class' => 'btn-danger', 'icon' => 'fa-times'], 'expired' => ['label' => 'Mark Expired', 'class' => 'btn-secondary', 'icon' => 'fa-clock']],
                            'accepted' => ['declined' => ['label' => 'Decline', 'class' => 'btn-danger', 'icon' => 'fa-times']],
                            'declined' => ['draft' => ['label' => 'Revert to Draft', 'class' => 'btn-warning', 'icon' => 'fa-undo']],
                        ];
                        $available = $transitions[$quote->status] ?? [];
                    @endphp

                    @foreach($available as $newStatus => $config)
                        <form action="{{ route('cimstyredash.quotes.status', $quote->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $newStatus }}">
                            <button type="submit" class="btn {{ $config['class'] }}" onclick="return confirm('Change status to {{ ucfirst($newStatus) }}?')">
                                <i class="fas {{ $config['icon'] }} me-1"></i> {{ $config['label'] }}
                            </button>
                        </form>
                    @endforeach

                    {{-- Convert to Job Card --}}
                    @if($quote->can_convert_to_job_card)
                        <form action="{{ route('cimstyredash.quotes.convert-job-card', $quote->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Convert this quote to a Job Card?')">
                                <i class="fas fa-wrench me-1"></i> Convert to Job Card
                            </button>
                        </form>
                    @endif

                    {{-- PDF Download --}}
                    <a href="{{ route('cimstyredash.quotes.pdf', $quote->id) }}" class="btn btn-outline-dark" target="_blank">
                        <i class="fas fa-file-pdf me-1"></i> PDF
                    </a>

                    {{-- Delete --}}
                    @if($quote->status !== 'invoiced')
                        <form action="{{ route('cimstyredash.quotes.destroy', $quote->id) }}" method="POST" class="d-inline ms-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to delete quote {{ $quote->quote_number }}?')">
                                <i class="fas fa-trash me-1"></i> Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Quote Header Info --}}
            <div class="card quote-header-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Quote Details
                    </h4>
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
                    <span class="badge bg-{{ $badgeClass }} badge-status fs-6">
                        {{ ucfirst($quote->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        {{-- Quote Info --}}
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Quote Number</div>
                            <div class="detail-value fw-bold">{{ $quote->quote_number }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Date</div>
                            <div class="detail-value">{{ $quote->quote_date->format('d M Y') }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Valid Until</div>
                            <div class="detail-value">
                                {{ $quote->valid_until ? $quote->valid_until->format('d M Y') : '-' }}
                                @if($quote->is_expired)
                                    <span class="badge bg-danger ms-1">Expired</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Branch</div>
                            <div class="detail-value">{{ $quote->branch->name ?? '-' }}</div>
                        </div>

                        {{-- Customer --}}
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Customer</div>
                            <div class="detail-value">
                                @if($quote->customer)
                                    {{ $quote->customer->full_name }}
                                    @if($quote->customer->company_name)
                                        <br><small class="text-muted">{{ $quote->customer->company_name }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">Walk-in</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value">{{ $quote->customer->phone ?? $quote->customer->cell ?? '-' }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">{{ $quote->customer->email ?? '-' }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Salesman</div>
                            <div class="detail-value">{{ $quote->salesman_name ?? '-' }}</div>
                        </div>

                        {{-- Vehicle --}}
                        @if($quote->vehicle)
                            <div class="col-md-3 mb-3">
                                <div class="detail-label">Vehicle Reg</div>
                                <div class="detail-value fw-bold">{{ strtoupper($quote->vehicle->registration ?? '-') }}</div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="detail-label">Make / Model</div>
                                <div class="detail-value">{{ $quote->vehicle->make }} {{ $quote->vehicle->model }}</div>
                            </div>
                        @endif

                        <div class="col-md-3 mb-3">
                            <div class="detail-label">Customer Order Ref</div>
                            <div class="detail-value">{{ $quote->customer_order_ref ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Option Tabs --}}
            @if($quote->quoteOptions->count())
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="fas fa-tachometer-alt me-2"></i>Tyre Options</h4>
                    </div>
                    <div class="card-body">
                        {{-- Tab Nav --}}
                        <ul class="nav nav-tabs" id="optionTabs" role="tablist">
                            @foreach($quote->quoteOptions as $index => $option)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="option-tab-{{ $option->option_number }}"
                                            data-bs-toggle="tab" data-bs-target="#option-pane-{{ $option->option_number }}"
                                            type="button" role="tab">
                                        Option {{ $option->option_number }}
                                        @if($option->is_selected)
                                            <span class="badge bg-success ms-1">Selected</span>
                                        @endif
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Tab Content --}}
                        <div class="tab-content mt-3" id="optionTabContent">
                            @foreach($quote->quoteOptions as $index => $option)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                                     id="option-pane-{{ $option->option_number }}" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered {{ $option->is_selected ? 'option-selected' : '' }}">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Brand</th>
                                                    <th>Model</th>
                                                    <th>Size</th>
                                                    <th>Load/Speed</th>
                                                    <th class="text-center">Qty</th>
                                                    <th class="text-end">Unit Cost</th>
                                                    <th class="text-end">Unit Price</th>
                                                    <th class="text-end">Markup %</th>
                                                    <th class="text-end">Discount %</th>
                                                    <th class="text-end">Line Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2">
                                                            @if($option->product && $option->product->brand && $option->product->brand->logo_url)
                                                                <span class="brand-logo-box"><img src="{{ asset('modules/cimstyredash/brands/' . $option->product->brand->logo_url) }}" alt="{{ $option->product->brand->name }}" onerror="this.parentElement.style.display='none'"></span>
                                                            @endif
                                                            {{ $option->product->brand->name ?? 'N/A' }}
                                                        </div>
                                                    </td>
                                                    <td>{{ $option->product->model_name ?? '-' }}</td>
                                                    <td>{{ $option->product->size->full_size ?? '-' }}</td>
                                                    <td>{{ $option->product->load_index ?? '-' }} / {{ $option->product->speed_rating ?? '-' }}</td>
                                                    <td class="text-center">{{ $option->quantity }}</td>
                                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($option->unit_cost, 2) }}</td>
                                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($option->unit_price, 2) }}</td>
                                                    <td class="text-end">{{ number_format($option->markup_pct, 1) }}%</td>
                                                    <td class="text-end">{{ number_format($option->discount_pct, 1) }}%</td>
                                                    <td class="text-end fw-bold">{{ $currencySymbol }}{{ number_format($option->line_total, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Services Table --}}
            @if($quote->quoteServices->count())
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="fas fa-cogs me-2"></i>Services</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Service</th>
                                        <th>Code</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Unit Price</th>
                                        <th class="text-end">Line Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quote->quoteServices as $qs)
                                        <tr>
                                            <td>{{ $qs->service->name ?? 'N/A' }}</td>
                                            <td>{{ $qs->service->code ?? '-' }}</td>
                                            <td class="text-center">{{ $qs->quantity }}</td>
                                            <td class="text-end">{{ $currencySymbol }}{{ number_format($qs->unit_price, 2) }}</td>
                                            <td class="text-end fw-bold">{{ $currencySymbol }}{{ number_format($qs->line_total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Totals --}}
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0"><i class="fas fa-calculator me-2"></i>Totals</h4>
                </div>
                <div class="card-body">
                    @php
                        $selectedOption = $quote->quoteOptions->firstWhere('is_selected', true) ?? $quote->quoteOptions->first();
                        $optionTotal = $selectedOption ? (float) $selectedOption->line_total : 0;
                        $servicesTotal = $quote->quoteServices->sum('line_total');
                        $grandTotal = $optionTotal + $servicesTotal;
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-borderless" style="max-width: 400px; margin-left: auto;">
                            <tbody>
                                <tr>
                                    <td>Selected Option Total:</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($optionTotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Services Total:</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($servicesTotal, 2) }}</td>
                                </tr>
                                <tr class="total-row border-top">
                                    <td>Grand Total:</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($grandTotal, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($quote->customer_comment || $quote->internal_notes)
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($quote->customer_comment)
                                <div class="col-md-6 mb-3">
                                    <div class="notes-section">
                                        <h6><i class="fas fa-comment me-1"></i> Customer Comment</h6>
                                        <p class="mb-0">{{ $quote->customer_comment }}</p>
                                    </div>
                                </div>
                            @endif
                            @if($quote->internal_notes)
                                <div class="col-md-6 mb-3">
                                    <div class="notes-section">
                                        <h6><i class="fas fa-lock me-1"></i> Internal Notes</h6>
                                        <p class="mb-0">{{ $quote->internal_notes }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- Linked Job Card --}}
            @if($quote->jobCard)
                <div class="card border-primary">
                    <div class="card-body d-flex align-items-center">
                        <i class="fas fa-wrench fa-2x text-primary me-3"></i>
                        <div>
                            <h6 class="mb-0">Linked Job Card</h6>
                            <a href="{{ route('cimstyredash.jobcards.show', $quote->jobCard->id) }}" class="fw-bold">
                                {{ $quote->jobCard->job_card_number }}
                            </a>
                            <span class="ms-2 text-muted">- {{ ucfirst($quote->jobCard->status) }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
