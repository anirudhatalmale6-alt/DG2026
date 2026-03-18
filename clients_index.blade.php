@extends('cimsclients::layouts.default')

@section('title', 'Clients')

@push('styles')
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
/* Stats Cards */
.stats-row { margin-bottom: 28px; }
.stat-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: none;
    position: relative;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}
.stat-card .card-body {
    padding: 24px;
    position: relative;
    z-index: 1;
}
.stat-card .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 16px;
}
.stat-card .stat-value {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 6px;
}
.stat-card .stat-label {
    font-size: 14px;
    font-weight: 500;
    opacity: 0.9;
}
.stat-card .stat-progress {
    height: 6px;
    border-radius: 3px;
    background: rgba(255,255,255,0.3);
    margin-top: 16px;
    overflow: hidden;
}
.stat-card .stat-progress-bar {
    height: 100%;
    border-radius: 3px;
    background: rgba(255,255,255,0.8);
    transition: width 1s ease;
}

/* Card themes */
.stat-card.total {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}
.stat-card.total .stat-icon { background: rgba(255,255,255,0.2); color: #fff; }

.stat-card.active {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: #fff;
}
.stat-card.active .stat-icon { background: rgba(255,255,255,0.2); color: #fff; }

.stat-card.inactive {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: #fff;
}
.stat-card.inactive .stat-icon { background: rgba(255,255,255,0.2); color: #fff; }

.stat-card.deleted {
    background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
    color: #fff;
}
.stat-card.deleted .stat-icon { background: rgba(255,255,255,0.2); color: #fff; }

/* Page Header */
.page-header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 24px;
}
.search-box-wrapper {
    flex: 1;
    max-width: 400px;
}
.search-box-wrapper .input-group {
    border: 2px solid #17A2B8;
    border-radius: 12px;
    overflow: hidden;
}
.search-box-wrapper .form-control {
    border: none;
    padding: 12px 16px;
    font-size: 14px;
}
.search-box-wrapper .input-group-text {
    background: linear-gradient(135deg, #17A2B8, #138496);
    border: none;
    color: #fff;
    padding: 0 16px;
}

/* Tabs */
.client-tabs { margin-bottom: 24px; }
.client-tabs .nav-tabs {
    border-bottom: 2px solid #e9ecef;
}
.client-tabs .nav-link {
    color: #6c757d;
    font-weight: 600;
    border: none;
    padding: 12px 24px;
    border-radius: 8px 8px 0 0;
    margin-bottom: -2px;
}
.client-tabs .nav-link.active {
    color: #17A2B8;
    background: #fff;
    border-bottom: 2px solid #17A2B8;
}
.client-tabs .nav-link .badge {
    margin-left: 8px;
    font-size: 11px;
}

/* Client Card */
.client-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.06);
    border-left: 4px solid #17A2B8;
    transition: all 0.3s ease;
}
.client-card:hover {
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    transform: translateX(5px);
}
.client-card.inactive { border-left-color: #ffc107; opacity: 0.8; }
.client-card.deleted-card { border-left-color: #dc3545; background: #fdf2f2; }

.client-code {
    font-size: 13px;
    font-weight: 700;
    color: #17A2B8;
    margin-bottom: 4px;
}
.client-name {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
}
.client-trading {
    font-size: 13px;
    color: #64748b;
    font-style: italic;
}
.client-date {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 4px;
}
.client-date i { margin-right: 4px; }

.info-block {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.info-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.info-icon.reg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
.info-icon.tax { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; }
.info-icon.vat { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; }
.info-icon.contact { background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); color: #fff; }

.info-label { font-size: 11px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
.info-value { font-size: 14px; font-weight: 600; color: #1e293b; word-break: break-word; }
.info-sub { font-size: 12px; color: #64748b; }

/* Action Dropdown */
.action-dropdown .dropdown-toggle {
    background: none;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    transition: all 0.2s ease;
}
.action-dropdown .dropdown-toggle:hover {
    background: #f8fafc;
    border-color: #17A2B8;
    color: #17A2B8;
}
.action-dropdown .dropdown-toggle::after { display: none; }

/* Restore/Delete buttons for deleted cards */
.btn-restore {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
}
.btn-delete-forever {
    background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
}

/* Empty state */
.empty-state { text-align: center; padding: 60px 20px; }
.empty-state i { font-size: 60px; color: #17A2B8; margin-bottom: 20px; display: block; }

/* ============================================== */
/* SMARTDASH FOOTER COMPONENT - Teal Theme       */
/* ============================================== */
.smartdash-footer {
    background: linear-gradient(135deg, #0d3d56 0%, #1a5a6e 100%);
    border-radius: 12px;
    padding: 20px 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 -4px 20px rgba(23, 162, 184, 0.15);
}
.smartdash-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #17A2B8 0%, #20c997 50%, #17A2B8 100%);
}
.smartdash-footer .footer-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}
.smartdash-footer .footer-left {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.smartdash-footer .footer-branding {
    display: flex;
    align-items: center;
    gap: 10px;
}
.smartdash-footer .footer-logo {
    font-size: 18px;
    font-weight: 800;
    background: linear-gradient(135deg, #17A2B8 0%, #20c997 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.smartdash-footer .footer-version {
    background: rgba(23, 162, 184, 0.3);
    color: #7dd3e8;
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}
.smartdash-footer .footer-copyright {
    font-size: 12px;
    color: #94a3b8;
}
.smartdash-footer .footer-stats {
    display: flex;
    gap: 24px;
}
.smartdash-footer .footer-stat {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: #cbd5e1;
}
.smartdash-footer .footer-stat i {
    font-size: 14px;
}
.smartdash-footer .footer-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
}
.smartdash-footer .footer-links {
    display: flex;
    gap: 12px;
}
.smartdash-footer .footer-links a {
    color: #94a3b8;
    font-size: 16px;
    transition: all 0.2s ease;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: rgba(255,255,255,0.08);
}
.smartdash-footer .footer-links a:hover {
    color: #17A2B8;
    background: rgba(23, 162, 184, 0.2);
    transform: translateY(-2px);
}
.smartdash-footer .footer-links a.clear-cache-btn {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: #fff;
}
.smartdash-footer .footer-links a.clear-cache-btn:hover {
    background: linear-gradient(135deg, #ee5a24 0%, #d63031 100%);
    color: #fff;
    transform: translateY(-2px) scale(1.1);
    box-shadow: 0 4px 12px rgba(238, 90, 36, 0.4);
}

@media (max-width: 768px) {
    .smartdash-footer .footer-content {
        flex-direction: column;
        text-align: center;
    }
    .smartdash-footer .footer-left,
    .smartdash-footer .footer-right {
        align-items: center;
    }
    .smartdash-footer .footer-stats {
        flex-wrap: wrap;
        justify-content: center;
    }
}

@media (max-width: 991px) {
    .search-box-wrapper { order: 3; max-width: 100%; width: 100%; margin: 0 0 15px 0; }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Stats Cards -->
    @php
        $total = $stats['total'] > 0 ? $stats['total'] : 1;
        $activePercent = round(($stats['active'] / $total) * 100, 1);
        $inactivePercent = round(($stats['inactive'] / $total) * 100, 1);
        $deletedPercent = round(($stats['deleted'] / $total) * 100, 1);
    @endphp

    <div class="row stats-row">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stat-card total">
                <div class="card-body">
                    <div class="stat-icon"><i class="fa fa-building"></i></div>
                    <div class="stat-value">{{ number_format($stats['total']) }}</div>
                    <div class="stat-label">Total Clients</div>
                    <div class="stat-progress"><div class="stat-progress-bar" style="width: 100%"></div></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stat-card active">
                <div class="card-body">
                    <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                    <div class="stat-value">{{ number_format($stats['active']) }}</div>
                    <div class="stat-label">Active</div>
                    <div class="stat-progress"><div class="stat-progress-bar" style="width: {{ $activePercent }}%"></div></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stat-card inactive">
                <div class="card-body">
                    <div class="stat-icon"><i class="fa fa-pause-circle"></i></div>
                    <div class="stat-value">{{ number_format($stats['inactive']) }}</div>
                    <div class="stat-label">Inactive</div>
                    <div class="stat-progress"><div class="stat-progress-bar" style="width: {{ $inactivePercent }}%"></div></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-4">
            <div class="stat-card deleted">
                <div class="card-body">
                    <div class="stat-icon"><i class="fa fa-trash"></i></div>
                    <div class="stat-value">{{ number_format($stats['deleted']) }}</div>
                    <div class="stat-label">Deleted</div>
                    <div class="stat-progress"><div class="stat-progress-bar" style="width: {{ $deletedPercent }}%"></div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="page-header-wrapper">
        <div class="search-box-wrapper">
            <div class="input-group">
                <input type="text" class="form-control" id="searchBox" placeholder="Search clients...">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
        </div>
        <a href="{{ route('cimsclients.create') }}" class="btn btn-primary">
            <i class="fa fa-plus me-2"></i> New Client
        </a>
    </div>

    <!-- Tabs -->
    <div class="client-tabs">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#all-tab">
                    All <span class="badge bg-secondary">{{ $stats['total'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#active-tab">
                    Active <span class="badge bg-success">{{ $stats['active'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#inactive-tab">
                    Inactive <span class="badge bg-warning">{{ $stats['inactive'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#deleted-tab">
                    Deleted <span class="badge bg-danger">{{ $stats['deleted'] }}</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- All Clients Tab -->
        <div class="tab-pane fade show active" id="all-tab">
            @forelse($clients as $c)
                @include('cimsclients::clients._card', ['c' => $c])
            @empty
                <div class="empty-state">
                    <i class="fa fa-building"></i>
                    <h4>No clients found</h4>
                    <p class="text-muted">Get started by adding your first client</p>
                    <a href="{{ route('cimsclients.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus me-2"></i> Add Client
                    </a>
                </div>
            @endforelse

            @if($clients->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $clients->links() }}
            </div>
            @endif
        </div>

        <!-- Active Tab -->
        <div class="tab-pane fade" id="active-tab">
            @php
                $activeClients = collect($clients->items())->where('is_active', 1);
            @endphp
            @forelse($activeClients as $c)
                @include('cimsclients::clients._card', ['c' => $c])
            @empty
                <div class="empty-state">
                    <i class="fa fa-check-circle"></i>
                    <p class="text-muted">No active clients</p>
                </div>
            @endforelse
        </div>

        <!-- Inactive Tab -->
        <div class="tab-pane fade" id="inactive-tab">
            @php
                $inactiveClients = collect($clients->items())->where('is_active', 0);
            @endphp
            @forelse($inactiveClients as $c)
                @include('cimsclients::clients._card', ['c' => $c, 'inactive' => true])
            @empty
                <div class="empty-state">
                    <i class="fa fa-pause-circle"></i>
                    <p class="text-muted">No inactive clients</p>
                </div>
            @endforelse
        </div>

        <!-- Deleted Tab -->
        <div class="tab-pane fade" id="deleted-tab">
            @forelse($deletedClients as $c)
                @include('cimsclients::clients._deleted_card', ['c' => $c])
            @empty
                <div class="empty-state">
                    <i class="fa fa-trash"></i>
                    <p class="text-muted">No deleted clients</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- SMARTDASH FOOTER -->
    <div class="smartdash-footer mt-4">
        <div class="footer-content">
            <div class="footer-left">
                <div class="footer-branding">
                    <span class="footer-logo">CIMS 3000</span>
                    <span class="footer-version">v3.0</span>
                </div>
                <div class="footer-copyright">&copy; {{ date('Y') }} ATP Solutions. All rights reserved.</div>
            </div>
            <div class="footer-stats">
                <div class="footer-stat"><i class="fa fa-building"></i> {{ $stats['total'] }} Clients</div>
                <div class="footer-stat"><i class="fa fa-check-circle"></i> {{ $stats['active'] }} Active</div>
            </div>
            <div class="footer-right">
                <div class="footer-links">
                    <a href="/cims/addresses" title="Addresses"><i class="fa fa-map-marker-alt"></i></a>
                    <a href="/cims/persons" title="Persons"><i class="fa fa-users"></i></a>
                    <a href="/cims/clients" title="Clients"><i class="fa fa-building"></i></a>
                    <a href="/cims/docsgen/sars/representative/preview" title="DocsGen"><i class="fa fa-file-pdf"></i></a>
                    <a href="/cims/addresses/clear-cache" title="Clear Cache" class="clear-cache-btn"><i class="fa fa-sync-alt"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    var searchBox = document.getElementById('searchBox');
    var searchTimer;

    searchBox.addEventListener('input', function() {
        clearTimeout(searchTimer);
        var query = this.value.toLowerCase().trim();

        searchTimer = setTimeout(function() {
            var cards = document.querySelectorAll('.client-card');
            cards.forEach(function(card) {
                var name = (card.dataset.name || '').toLowerCase();
                var code = (card.dataset.code || '').toLowerCase();
                var reg = (card.dataset.reg || '').toLowerCase();
                var tax = (card.dataset.tax || '').toLowerCase();
                var vat = (card.dataset.vat || '').toLowerCase();

                if (query === '' ||
                    name.includes(query) ||
                    code.includes(query) ||
                    reg.includes(query) ||
                    tax.includes(query) ||
                    vat.includes(query)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }, 300);
    });
});

function confirmDelete(id) {
    Swal.fire({
        title: 'Delete Client?',
        text: 'This client will be moved to trash.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delete-form-' + id).submit();
        }
    });
}

function confirmRestore(id) {
    Swal.fire({
        title: 'Restore Client?',
        text: 'This client will be restored.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        confirmButtonText: 'Yes, restore'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('restore-form-' + id).submit();
        }
    });
}

function confirmPermanentDelete(id) {
    Swal.fire({
        title: 'Delete Permanently?',
        text: 'This cannot be undone!',
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete forever'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('force-delete-form-' + id).submit();
        }
    });
}
</script>
@endpush
