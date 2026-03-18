@extends('layouts.default')

@section('title', 'EMP201 Compliance')

@push('styles')
<link href="/public/smartdash/css/smartdash-forms.css" rel="stylesheet">
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* ============================================== */
/* PAGE HEADER - Teal Gradient (matches Addresses) */
/* ============================================== */
.smartdash-page-header {
    background: linear-gradient(135deg, #17A2B8 0%, #138496 100%);
    border-radius: 12px;
    padding: 20px 28px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.25);
}
.smartdash-page-header .page-title {
    display: flex;
    align-items: center;
    gap: 15px;
}
.smartdash-page-header .page-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.smartdash-page-header .page-title h1 {
    font-size: 26px;
    font-weight: 800;
    margin: 0;
    letter-spacing: 0.5px;
}
.smartdash-page-header .page-title p {
    font-size: 13px;
    margin: 4px 0 0 0;
    opacity: 0.9;
}
.smartdash-page-header .page-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}
.smartdash-page-header .page-breadcrumb a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: color 0.2s;
}
.smartdash-page-header .page-breadcrumb a:hover {
    color: #fff;
}
.smartdash-page-header .page-breadcrumb .separator {
    opacity: 0.5;
}
.smartdash-page-header .page-breadcrumb .current {
    font-weight: 700;
    color: #fff;
}
.smartdash-page-header .page-actions {
    display: flex;
    gap: 10px;
}
.smartdash-page-header .btn-page-action {
    background: rgba(255,255,255,0.2);
    border: none;
    color: #fff;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s;
}
.smartdash-page-header .btn-page-action:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}
.smartdash-page-header .btn-page-primary {
    background: #fff;
    color: #17A2B8;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.smartdash-page-header .btn-page-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    color: #0d3d56;
}
@media (max-width: 992px) {
    .smartdash-page-header {
        flex-direction: column;
        text-align: center;
    }
    .smartdash-page-header .page-title {
        flex-direction: column;
    }
    .smartdash-page-header .page-title h1 {
        font-size: 22px;
    }
}

/* ============================================== */
/* STATS CARDS - Gradient Style (matches Addresses) */
/* ============================================== */
.stats-row { margin-bottom: 28px; }
.stat-card {
    border-radius: 12px;
    padding: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
    min-height: 140px;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
}
.stat-card.total {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.stat-card.active-card {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.stat-card.inactive-card {
    background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);
}
.stat-card.year-card {
    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
}
.stat-card .stat-label {
    font-size: 13px;
    font-weight: 500;
    opacity: 0.9;
    margin-bottom: 8px;
}
.stat-card .stat-number {
    font-size: 36px;
    font-weight: 700;
    margin: 0;
    line-height: 1.1;
}
.stat-card .stat-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(255,255,255,0.25);
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}
.stat-card .stat-badge i {
    font-size: 10px;
}
.stat-card .stat-icon {
    position: absolute;
    right: 18px;
    bottom: 20px;
    font-size: 60px;
    opacity: 0.4;
    text-shadow: 0 2px 10px rgba(0,0,0,0.15);
}
.stat-card .stat-progress {
    margin-top: 15px;
    height: 6px;
    background: rgba(0,0,0,0.2);
    border-radius: 10px;
    overflow: hidden;
    width: 60%;
}
.stat-card .stat-progress-bar {
    height: 100%;
    background: rgba(255,255,255,0.95);
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(255,255,255,0.6);
}

/* ============================================== */
/* SEARCH BOX - Teal Border (matches Addresses)   */
/* ============================================== */
.search-box-wrapper { flex: 1; max-width: 600px; margin: 0 20px; }
.search-box-wrapper .input-group {
    border: 2px solid #17A2B8;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 10px rgba(23, 162, 184, 0.1);
}
.search-input {
    border: none !important;
    font-size: 14px;
    padding: 10px 16px;
}
.search-input:focus {
    box-shadow: none !important;
}
.btn-search {
    background: linear-gradient(135deg, #17A2B8, #138496);
    border: none;
    color: #fff;
    padding: 10px 16px;
    font-size: 14px;
    transition: all 0.2s;
}
.btn-search:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    color: #fff;
}

/* ============================================== */
/* FILTER DROPDOWNS                               */
/* ============================================== */
.filter-row { margin-bottom: 15px; }
.filter-row .sd_drop_class {
    font-size: 13px;
}

/* ============================================== */
/* NEW EMP201 BUTTON (matches Addresses pattern)  */
/* ============================================== */
.btn-new-emp201 {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
    border: none;
    color: #fff;
    padding: 14px 28px;
    font-weight: 700;
    border-radius: 12px;
    font-size: 15px;
    letter-spacing: 0.3px;
    box-shadow: 0 4px 14px rgba(13, 148, 136, 0.4);
    transition: all 0.3s ease;
    text-decoration: none;
}
.btn-new-emp201:hover {
    background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(13, 148, 136, 0.5);
    color: #fff;
}

/* ============================================== */
/* TABLE STYLING                                  */
/* ============================================== */
.emp201-table thead th {
    background: #f8fafc;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #64748b;
    border-bottom: 2px solid #e2e8f0;
    padding: 12px 10px;
    white-space: nowrap;
}
.emp201-table tbody td {
    padding: 12px 10px;
    font-size: 14px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.emp201-table tbody tr:hover {
    background: #f0fdfa;
}
.emp201-table .currency-col {
    text-align: right;
    font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, 'Liberation Mono', monospace;
    font-size: 13px;
    font-weight: 600;
}
.emp201-table .total-due-col {
    text-align: right;
    font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, 'Liberation Mono', monospace;
    font-size: 13px;
    font-weight: 700;
    color: #0d3d56;
}

/* ============================================== */
/* ACTION DROPDOWN (matches Addresses exactly)    */
/* ============================================== */
.action-dropdown .dropdown-toggle {
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%) !important;
    border: none !important;
    padding: 10px 14px !important;
    font-size: 16px !important;
    color: #475569 !important;
    border-radius: 10px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    line-height: 1 !important;
}
.action-dropdown .dropdown-toggle:hover {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%) !important;
    color: #fff !important;
    transform: scale(1.05) !important;
}
.action-dropdown .dropdown-toggle::after { display: none !important; }
.action-dropdown .dropdown-menu {
    z-index: 1050 !important;
    border: none !important;
    border-radius: 12px !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
    padding: 8px !important;
    min-width: 200px !important;
}
.action-dropdown .dropdown-item {
    border-radius: 8px !important;
    padding: 10px 14px !important;
    font-weight: 500 !important;
    font-size: 14px !important;
    transition: all 0.15s ease !important;
}
.action-dropdown .dropdown-item:hover {
    background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%) !important;
}
.action-dropdown .dropdown-item.text-danger:hover {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%) !important;
}

/* ============================================== */
/* EMPTY STATE                                    */
/* ============================================== */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-state i { font-size: 60px; color: #17A2B8; margin-bottom: 20px; display: block; }

@media (max-width: 991px) {
    .search-box-wrapper { order: 3; max-width: 100%; width: 100%; margin: 0 0 15px 0; }
}
</style>
@endpush

@section('content')
<div class="container-fluid">

    <!-- ============================================== -->
    <!-- PAGE HEADER - Breadcrumb & Actions             -->
    <!-- ============================================== -->
    <div class="smartdash-page-header mb-4">
        <div class="page-title">
            <div class="page-icon">
                <i class="fa fa-file-invoice-dollar"></i>
            </div>
            <div>
                <h1>EMP201 COMPLIANCE</h1>
                <p>Monthly Payroll Return Management - SARS</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/dashboard"><i class="fa fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <span class="current">EMP201 Compliance</span>
        </div>
        <div class="page-actions">
            <a href="#" class="btn-page-action" title="Settings">
                <i class="fa fa-cog"></i>
            </a>
            <a href="{{ route('cimsemp201.create') }}" class="btn-page-primary">
                <i class="fa fa-plus"></i> Add EMP201
            </a>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- STATS CARDS - Gradient Style                   -->
    <!-- ============================================== -->
    @php
        $totalNum = $stats['total'] > 0 ? $stats['total'] : 1;
        $activePercent = round(($stats['active'] / $totalNum) * 100, 1);
        $inactivePercent = round(($stats['inactive'] / $totalNum) * 100, 1);
    @endphp
    <div class="row stats-row">
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="stat-card total">
                <div class="stat-label">Total Declarations</div>
                <div class="stat-number">{{ $stats['total'] }}</div>
                <div class="stat-badge"><i class="fa fa-database"></i> 100%</div>
                <i class="fa fa-file-invoice-dollar stat-icon"></i>
                <div class="stat-progress"><div class="stat-progress-bar" style="width: 100%"></div></div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="stat-card active-card">
                <div class="stat-label">Active</div>
                <div class="stat-number">{{ $stats['active'] }}</div>
                <div class="stat-badge"><i class="fa fa-arrow-up"></i> {{ $activePercent }}%</div>
                <i class="fa fa-check-circle stat-icon"></i>
                <div class="stat-progress"><div class="stat-progress-bar" style="width: {{ $activePercent }}%"></div></div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="stat-card inactive-card">
                <div class="stat-label">Inactive</div>
                <div class="stat-number">{{ $stats['inactive'] }}</div>
                <div class="stat-badge"><i class="fa fa-pause"></i> {{ $inactivePercent }}%</div>
                <i class="fa fa-circle-pause stat-icon"></i>
                <div class="stat-progress"><div class="stat-progress-bar" style="width: {{ $inactivePercent }}%"></div></div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 mb-3">
            <div class="stat-card year-card">
                <div class="stat-label">This Year</div>
                <div class="stat-number">{{ $stats['this_year'] }}</div>
                <div class="stat-badge"><i class="fa fa-calendar"></i> {{ date('Y') }}</div>
                <i class="fa fa-calendar-alt stat-icon"></i>
                <div class="stat-progress"><div class="stat-progress-bar" style="width: 100%"></div></div>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- TABS + SEARCH + BUTTON                         -->
    <!-- ============================================== -->
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        {{-- Status Tabs - URL parameter based --}}
        <div class="card-tabs mb-3">
            <ul class="nav nav-tabs style-1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ !request('status') ? 'active' : '' }}"
                       href="{{ route('cimsemp201.index', array_merge(request()->except('status', 'page'), [])) }}">
                        All Status
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') === 'active' ? 'active' : '' }}"
                       href="{{ route('cimsemp201.index', array_merge(request()->except('status', 'page'), ['status' => 'active'])) }}">
                        Active
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') === 'inactive' ? 'active' : '' }}"
                       href="{{ route('cimsemp201.index', array_merge(request()->except('status', 'page'), ['status' => 'inactive'])) }}">
                        Inactive
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') === 'deleted' ? 'active' : '' }}"
                       href="{{ route('cimsemp201.index', array_merge(request()->except('status', 'page'), ['status' => 'deleted'])) }}">
                        Deleted
                    </a>
                </li>
            </ul>
        </div>

        {{-- Search Box --}}
        <div class="search-box-wrapper mb-3">
            <form method="GET" action="{{ route('cimsemp201.index') }}" id="searchForm">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('client_id'))
                    <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                @endif
                @if(request('financial_year'))
                    <input type="hidden" name="financial_year" value="{{ request('financial_year') }}">
                @endif
                @if(request('pay_period'))
                    <input type="hidden" name="pay_period" value="{{ request('pay_period') }}">
                @endif
                <div class="input-group">
                    <input type="text" name="search" class="form-control search-input"
                           placeholder="Search by reference, client..."
                           value="{{ request('search') }}">
                    <button class="btn btn-search" type="submit"><i class="fa fa-search"></i></button>
                </div>
            </form>
        </div>

        {{-- New EMP201 Button --}}
        <div class="mb-3">
            <a href="{{ route('cimsemp201.create') }}" class="btn btn-new-emp201">+ Add EMP201</a>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- FILTER DROPDOWNS                               -->
    <!-- ============================================== -->
    <div class="row g-2 mb-4 filter-row">
        <div class="col-md-3">
            <select name="client_id" id="filter_client" class="default-select sd_drop_class" style="width: 100%">
                <option value="">All Clients</option>
                @foreach($clients ?? [] as $client)
                    <option value="{{ $client->client_id }}" {{ request('client_id') == $client->client_id ? 'selected' : '' }}>
                        {{ $client->company_name }} ({{ $client->client_code }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="financial_year" id="filter_financial_year" class="default-select sd_drop_class" style="width: 100%">
                <option value="">All Financial Years</option>
                @foreach($financialYears ?? [] as $year)
                    <option value="{{ $year }}" {{ request('financial_year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="pay_period" id="filter_pay_period" class="default-select sd_drop_class" style="width: 100%">
                <option value="">All Periods</option>
                @foreach($payPeriods ?? [] as $period)
                    <option value="{{ $period }}" {{ request('pay_period') == $period ? 'selected' : '' }}>
                        {{ $period }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            @if(request('search') || request('client_id') || request('financial_year') || request('pay_period'))
                <a href="{{ route('cimsemp201.index', request('status') ? ['status' => request('status')] : []) }}"
                   class="btn sd_btn_outline sd_btn_sm" style="margin-top:5px;">
                    <i class="fa fa-times me-1"></i> Clear Filters
                </a>
            @endif
        </div>
    </div>

    <!-- ============================================== -->
    <!-- EMP201 TABLE                                   -->
    <!-- ============================================== -->
    @if($declarations->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 emp201-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Year End</th>
                        <th>Client Code</th>
                        <th>References</th>
                        <th>Tax Period</th>
                        <th class="text-end">PAYE</th>
                        <th class="text-end">SDL</th>
                        <th class="text-end">UIF</th>
                        <th class="text-end">Liability</th>
                        <th class="text-end">Penalties</th>
                        <th class="text-end">Total Due</th>
                        <th class="text-center">Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($declarations as $index => $declaration)
                        @php
                            $liability = ($declaration->pay_liability ?? 0) + ($declaration->sdl_liability ?? 0) + ($declaration->uif_liability ?? 0);
                            $penalties = ($declaration->penalty ?? 0) + ($declaration->interest ?? 0) + ($declaration->other ?? 0);
                        @endphp
                        <tr id="emp201-row-{{ $declaration->id }}">
                            <td class="text-muted">{{ $declarations->firstItem() + $index }}</td>
                            <td>
                                <span class="fw-bold">{{ $declaration->financial_year }}</span>
                            </td>
                            <td>
                                <span style="font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, 'Liberation Mono', monospace; font-size: 0.85rem; font-weight: 600;">
                                    {{ $declaration->client_code ?? '--' }}
                                </span>
                            </td>
                            <td>{{ $declaration->payment_reference ?? '--' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $declaration->pay_period ?? '--' }}</span>
                            </td>
                            <td class="currency-col">{{ number_format($declaration->pay_liability ?? 0, 2) }}</td>
                            <td class="currency-col">{{ number_format($declaration->sdl_liability ?? 0, 2) }}</td>
                            <td class="currency-col">{{ number_format($declaration->uif_liability ?? 0, 2) }}</td>
                            <td class="currency-col">{{ number_format($liability, 2) }}</td>
                            <td class="currency-col">{{ number_format($penalties, 2) }}</td>
                            <td class="total-due-col">{{ number_format($declaration->tax_payable ?? 0, 2) }}</td>
                            <td class="text-center">
                                @if($declaration->status === 'active' || $declaration->is_active == 1)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                        <i class="fa fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Active
                                    </span>
                                @elseif($declaration->status === 'inactive' || $declaration->is_active == 0)
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                        <i class="fa fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Inactive
                                    </span>
                                @elseif($declaration->status === 'deleted')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">
                                        <i class="fa fa-circle me-1" style="font-size: 0.5rem; vertical-align: middle;"></i> Deleted
                                    </span>
                                @else
                                    <span class="badge bg-info bg-opacity-10 text-info">{{ ucfirst($declaration->status ?? 'Unknown') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($declaration->created_at)
                                    {{ $declaration->created_at->format('d M Y') }}
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="dropdown action-dropdown d-inline-block">
                                    <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                        style="background:linear-gradient(135deg,#f1f5f9 0%,#e2e8f0 100%);border:none;padding:10px 14px;font-size:16px;color:#475569;border-radius:10px;transition:all .2s ease;cursor:pointer;line-height:1">
                                        <i class="fa fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end"
                                        style="border:none;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.15);padding:8px;min-width:200px">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('cimsemp201.show', $declaration->id) }}"
                                               style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px">
                                                <i class="fa fa-eye me-2 text-info"></i> View
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('cimsemp201.edit', $declaration->id) }}"
                                               style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px">
                                                <i class="fa fa-edit me-2 text-primary"></i> Edit
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider" style="margin:4px 8px"></li>
                                        <li>
                                            <a class="dropdown-item text-danger btn-delete-emp201" href="#"
                                               data-id="{{ $declaration->id }}"
                                               data-reference="{{ $declaration->payment_reference ?? $declaration->financial_year }}"
                                               style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px">
                                                <i class="fa fa-trash-alt me-2"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <div class="text-muted small">
                Showing {{ $declarations->firstItem() }} to {{ $declarations->lastItem() }} of {{ $declarations->total() }} declarations
            </div>
            <div>
                {{ $declarations->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fa fa-file-invoice-dollar fa-3x text-muted opacity-50"></i>
            </div>
            <h5>No EMP201 declarations found</h5>
            <p class="text-muted">
                @if(request('search') || request('status') || request('client_id') || request('financial_year') || request('pay_period'))
                    No declarations match your current filters. Try adjusting your search criteria.
                @else
                    Get started by adding your first EMP201 declaration.
                @endif
            </p>
            <div class="d-flex gap-2 justify-content-center">
                @if(request('search') || request('client_id') || request('financial_year') || request('pay_period'))
                    <a href="{{ route('cimsemp201.index', request('status') ? ['status' => request('status')] : []) }}"
                       class="btn sd_btn_outline sd_btn_sm">
                        <i class="fa fa-times me-1"></i> Clear Filters
                    </a>
                @endif
                <a href="{{ route('cimsemp201.create') }}" class="btn btn-new-emp201">
                    <i class="fa fa-plus me-1"></i> Add EMP201
                </a>
            </div>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── SweetAlert2 Delete Confirmation with AJAX ─────────────────────
    document.querySelectorAll('.btn-delete-emp201').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var empId = this.getAttribute('data-id');
            var empRef = this.getAttribute('data-reference');

            Swal.fire({
                title: 'Delete EMP201?',
                text: 'Are you sure you want to delete declaration "' + empRef + '"? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
                if (result.isConfirmed) {
                    fetch('{{ url("cims/emp201") }}/' + empId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'The EMP201 declaration has been deleted.',
                                confirmButtonColor: '#17A2B8',
                                timer: 2000,
                                timerProgressBar: true
                            });
                            var row = document.getElementById('emp201-row-' + empId);
                            if (row) {
                                row.style.transition = 'opacity 0.3s ease';
                                row.style.opacity = '0';
                                setTimeout(function () {
                                    row.remove();
                                    var tbody = document.querySelector('table tbody');
                                    if (tbody && tbody.children.length === 0) {
                                        window.location.reload();
                                    }
                                }, 300);
                            }
                        } else {
                            Swal.fire('Error', data.message || 'Failed to delete the declaration.', 'error');
                        }
                    })
                    .catch(function (error) {
                        Swal.fire('Error', 'An error occurred while deleting. Please try again.', 'error');
                        console.error('Delete error:', error);
                    });
                }
            });
        });
    });

    // ── SweetAlert2 Flash Messages ────────────────────────────────────
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '{{ session('success') }}',
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: '<div style="font-size: 16px;">{!! addslashes(session('error')) !!}</div>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545',
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    @endif

});

// ── Filter helper for dropdown auto-submit ──────────────────────────
function applyFilter(name, value) {
    var url = new URL(window.location.href);
    if (value) {
        url.searchParams.set(name, value);
    } else {
        url.searchParams.delete(name);
    }
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

// ── Bootstrap-Select change handlers for filter dropdowns ───────────
jQuery(function($) {
    $('#filter_client').on('changed.bs.select', function() {
        applyFilter('client_id', $(this).val());
    });
    $('#filter_financial_year').on('changed.bs.select', function() {
        applyFilter('financial_year', $(this).val());
    });
    $('#filter_pay_period').on('changed.bs.select', function() {
        applyFilter('pay_period', $(this).val());
    });
});
</script>
@endpush
