@extends('layouts.default')

@section('title', 'TyreDash Dashboard')

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
    .bg-gradient-primary   { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
    .bg-gradient-success   { background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%); }
    .bg-gradient-info      { background: linear-gradient(135deg, #36b9cc 0%, #258391 100%); }
    .bg-gradient-warning   { background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%); }
    .bg-gradient-danger    { background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%); }
    .bg-gradient-secondary { background: linear-gradient(135deg, #858796 0%, #60616f 100%); }

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
                <h4>TyreDash Dashboard</h4>
                <p class="mb-0">Overview of your tyre business</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">TyreDash</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
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
            @include('cimstyredash::partials.sidebar', ['activePage' => 'dashboard'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-lg-8">

            {{-- Stat Cards --}}
            <div class="row">
                {{-- Total Products --}}
                <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card bg-gradient-primary">
                        <div class="card-body position-relative">
                            <i class="fas fa-box stat-icon"></i>
                            <div class="stat-value">{{ number_format($totalProducts) }}</div>
                            <div class="stat-label">Products</div>
                        </div>
                    </div>
                </div>

                {{-- Total Brands --}}
                <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card bg-gradient-success">
                        <div class="card-body position-relative">
                            <i class="fas fa-tags stat-icon"></i>
                            <div class="stat-value">{{ number_format($totalBrands) }}</div>
                            <div class="stat-label">Brands</div>
                        </div>
                    </div>
                </div>

                {{-- Stock Value --}}
                <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card bg-gradient-info">
                        <div class="card-body position-relative">
                            <i class="fas fa-coins stat-icon"></i>
                            <div class="stat-value">{{ $currencySymbol }}{{ number_format($totalStockValue, 2) }}</div>
                            <div class="stat-label">Stock Value</div>
                        </div>
                    </div>
                </div>

                {{-- Low Stock Alerts --}}
                <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card bg-gradient-danger">
                        <div class="card-body position-relative">
                            <i class="fas fa-exclamation-triangle stat-icon"></i>
                            <div class="stat-value">{{ number_format($lowStockAlerts) }}</div>
                            <div class="stat-label">Low Stock Alerts</div>
                        </div>
                    </div>
                </div>

                {{-- Quotes This Month --}}
                <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card bg-gradient-warning">
                        <div class="card-body position-relative">
                            <i class="fas fa-file-invoice stat-icon"></i>
                            <div class="stat-value">{{ number_format($quotesThisMonth) }}</div>
                            <div class="stat-label">Quotes This Month</div>
                        </div>
                    </div>
                </div>

                {{-- Job Cards Today --}}
                <div class="col-xl-4 col-md-6 col-sm-6 mb-4">
                    <div class="card stat-card bg-gradient-secondary">
                        <div class="card-body position-relative">
                            <i class="fas fa-wrench stat-icon"></i>
                            <div class="stat-value">{{ number_format($jobCardsToday) }}</div>
                            <div class="stat-label">Job Cards Today</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Quotes --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-file-invoice me-2"></i>Recent Quotes
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Quote #</th>
                                    <th>Customer</th>
                                    <th>Branch</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentQuotes as $quote)
                                    <tr>
                                        <td>
                                            <a href="{{ route('cimstyredash.quotes.show', $quote->id) }}">
                                                {{ $quote->quote_number }}
                                            </a>
                                        </td>
                                        <td>{{ $quote->customer->full_name ?? 'N/A' }}</td>
                                        <td>{{ $quote->branch->name ?? 'N/A' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($quote->quote_date)->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $quoteStatusColors = [
                                                    'draft'    => 'warning',
                                                    'sent'     => 'info',
                                                    'accepted' => 'success',
                                                    'declined' => 'danger',
                                                    'expired'  => 'secondary',
                                                    'invoiced' => 'primary',
                                                ];
                                                $color = $quoteStatusColors[$quote->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }} status-badge">{{ $quote->status }}</span>
                                        </td>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($quote->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No quotes found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($recentQuotes->isNotEmpty())
                    <div class="card-footer text-end">
                        <a href="{{ route('cimstyredash.quotes.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-right me-1"></i>View All Quotes
                        </a>
                    </div>
                @endif
            </div>

            {{-- Recent Job Cards --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-wrench me-2"></i>Recent Job Cards
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Job Card #</th>
                                    <th>Customer</th>
                                    <th>Vehicle Reg</th>
                                    <th>Branch</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentJobCards as $jobCard)
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
                                        <td>
                                            @php
                                                $jobStatusColors = [
                                                    'open'           => 'warning',
                                                    'in_progress'    => 'info',
                                                    'awaiting_parts' => 'secondary',
                                                    'complete'       => 'success',
                                                    'invoiced'       => 'primary',
                                                    'cancelled'      => 'danger',
                                                ];
                                                $color = $jobStatusColors[$jobCard->status] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $color }} status-badge">{{ str_replace('_', ' ', $jobCard->status) }}</span>
                                        </td>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($jobCard->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            No job cards found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($recentJobCards->isNotEmpty())
                    <div class="card-footer text-end">
                        <a href="{{ route('cimstyredash.jobcards.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-right me-1"></i>View All Job Cards
                        </a>
                    </div>
                @endif
            </div>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection
