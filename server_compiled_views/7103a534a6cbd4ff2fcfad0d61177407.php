<?php $__env->startSection('title', 'EMP201 Compliance'); ?>

<?php $__env->startPush('styles'); ?>
<link href="/public/smartdash/css/smartdash-forms.css" rel="stylesheet">
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<style>
/* ============================================== */
/* STATS CARDS                                    */
/* ============================================== */
.stats-row { margin-bottom: 24px; }
.stat-card {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: none;
    position: relative;
    color: #fff;
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
    width: 54px;
    height: 54px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 14px;
    background: rgba(255,255,255,0.2);
    color: #fff;
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
.stat-card.total { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.stat-card.active-card { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
.stat-card.inactive-card { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stat-card.year-card { background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); }

/* ============================================== */
/* TOOLBAR: Tabs + Search + Add button            */
/* ============================================== */
.toolbar-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 20px;
}
.emp-tabs .nav-tabs {
    border-bottom: 2px solid #e9ecef;
}
.emp-tabs .nav-link {
    color: #6c757d;
    font-weight: 600;
    font-size: 14px;
    border: none;
    padding: 10px 20px;
    border-radius: 8px 8px 0 0;
    margin-bottom: -2px;
    transition: all 0.2s ease;
}
.emp-tabs .nav-link:hover {
    color: #17A2B8;
    background: rgba(23, 162, 184, 0.05);
}
.emp-tabs .nav-link.active {
    color: #17A2B8;
    background: #fff;
    border-bottom: 3px solid #17A2B8;
    font-weight: 700;
}
.emp-tabs .nav-link .badge {
    margin-left: 6px;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 12px;
}
.search-box-wrapper {
    flex: 1;
    max-width: 400px;
}
.search-box-wrapper .input-group {
    border: 2px solid #17A2B8;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 8px rgba(23, 162, 184, 0.1);
}
.search-box-wrapper .form-control {
    border: none;
    padding: 10px 16px;
    font-size: 14px;
}
.search-box-wrapper .form-control:focus {
    box-shadow: none;
}
.search-box-wrapper .input-group-text {
    background: linear-gradient(135deg, #17A2B8, #138496);
    border: none;
    color: #fff;
    padding: 0 16px;
    cursor: pointer;
}
.btn-add-emp201 {
    background: linear-gradient(135deg, #17A2B8 0%, #138496 100%);
    border: none;
    color: #fff;
    padding: 10px 24px;
    font-weight: 700;
    font-size: 14px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.btn-add-emp201:hover {
    background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(23, 162, 184, 0.4);
    color: #fff;
}

/* ============================================== */
/* FILTER CARD                                    */
/* ============================================== */
.filter-card {
    background: #fff;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    border: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}
.filter-card .filter-label {
    font-size: 13px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
}
.filter-card .filter-label i {
    color: #17A2B8;
    margin-right: 6px;
}

/* ============================================== */
/* TABLE CARD                                     */
/* ============================================== */
.table-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #e9ecef;
    overflow: visible;
    position: relative;
}
.table-card .table-responsive {
    overflow-x: auto;
    overflow-y: visible;
}
.table-card-header {
    background: linear-gradient(135deg, #17A2B8 0%, #0d3d56 100%);
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 16px 16px 0 0;
}
.table-card-header h5 {
    color: #fff;
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.table-card-header h5 i {
    font-size: 18px;
    opacity: 0.9;
}
.table-card-header .record-count {
    background: rgba(255,255,255,0.2);
    color: #fff;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* ============================================== */
/* TABLE STYLING                                  */
/* ============================================== */
.emp201-table {
    margin: 0;
}
.emp201-table thead th {
    background: #f8fafc;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #475569;
    border-bottom: 2px solid #e2e8f0;
    border-top: none;
    padding: 14px 12px;
    white-space: nowrap;
}
.emp201-table tbody td {
    padding: 14px 12px;
    font-size: 14px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
}
.emp201-table tbody tr {
    transition: background 0.15s ease;
}
.emp201-table tbody tr:hover {
    background: #f0fdfa;
}
.emp201-table tbody tr:last-child td {
    border-bottom: none;
}

/* Row number */
.row-num {
    width: 28px;
    height: 28px;
    background: #f1f5f9;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
}

/* Client code badge */
.client-code-badge {
    font-size: 13px;
    font-weight: 700;
    color: #0d3d56;
    background: #e0f7fa;
    padding: 4px 10px;
    border-radius: 6px;
    letter-spacing: 0.3px;
}

/* Year badge */
.year-badge {
    font-weight: 700;
    color: #1e293b;
    font-size: 14px;
}

/* Reference text */
.ref-text {
    font-size: 13px;
    color: #64748b;
    font-style: italic;
}

/* Tax Period badge */
.period-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    color: #0369a1;
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}
.period-badge i {
    font-size: 10px;
    opacity: 0.7;
}

/* Currency columns */
.currency-col {
    text-align: right;
    font-size: 14px;
    font-weight: 500;
    color: #475569;
}
.currency-col.zero {
    color: #cbd5e1;
}
.total-due-col {
    text-align: right;
    font-size: 14px;
    font-weight: 700;
    color: #0d3d56;
}

/* Status badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.3px;
}
.status-badge .dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}
.status-badge.active { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.status-badge.active .dot { background: #059669; }
.status-badge.inactive { background: #fefce8; color: #ca8a04; border: 1px solid #fde68a; }
.status-badge.inactive .dot { background: #ca8a04; }
.status-badge.deleted { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
.status-badge.deleted .dot { background: #dc2626; }

/* Date column */
.date-text {
    font-size: 13px;
    color: #64748b;
}

/* Action Dropdown - inline styles for caching */

/* ============================================== */
/* PAGINATION                                     */
/* ============================================== */
.pagination-bar {
    padding: 16px 24px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.pagination-bar .showing-text {
    font-size: 13px;
    color: #64748b;
}

/* ============================================== */
/* EMPTY STATE                                    */
/* ============================================== */
.empty-state {
    text-align: center;
    padding: 80px 20px;
}
.empty-state .empty-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #e0f7fa, #b2ebf2);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
}
.empty-state .empty-icon i {
    font-size: 32px;
    color: #17A2B8;
}
.empty-state h5 {
    color: #1e293b;
    font-weight: 700;
    margin-bottom: 8px;
}
.empty-state p {
    color: #64748b;
    font-size: 14px;
    margin-bottom: 20px;
}

/* ============================================== */
/* CLEAR FILTERS BUTTON                           */
/* ============================================== */
.btn-clear-filters {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #64748b;
    padding: 6px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-clear-filters:hover {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}

@media (max-width: 991px) {
    .toolbar-wrapper { flex-direction: column; align-items: stretch; }
    .search-box-wrapper { max-width: 100%; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

    
    <div class="row page-titles">
        <div class="d-flex align-items-center justify-content-between">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="fs-2" style="color:#000" href="javascript:void(0)">Compliance</a></li>
                <li class="breadcrumb-item active"><a class="fs-2" style="color:#17A2B8" href="javascript:void(0)">EMP201</a></li>
            </ol>
        </div>
    </div>

    
    <?php
        $totalNum = $stats['total'] > 0 ? $stats['total'] : 1;
        $activePercent = round(($stats['active'] / $totalNum) * 100, 1);
        $inactivePercent = round(($stats['inactive'] / $totalNum) * 100, 1);
    ?>
    <div class="row stats-row">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card total">
                <div class="card-body">
                    <div class="stat-icon"><i class="fa fa-file-invoice-dollar"></i></div>
                    <div class="stat-value"><?php echo e($stats['total']); ?></div>
                    <div class="stat-label">Total Declarations</div>
                    <div class="stat-progress"><div class="stat-progress-bar" style="width: 100%"></div></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card active-card">
                <div class="card-body">
                    <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                    <div class="stat-value"><?php echo e($stats['active']); ?></div>
                    <div class="stat-label">Active</div>
                    <div class="stat-progress"><div class="stat-progress-bar" style="width: <?php echo e($activePercent); ?>%"></div></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card inactive-card">
                <div class="card-body">
                    <div class="stat-icon"><i class="fa fa-pause-circle"></i></div>
                    <div class="stat-value"><?php echo e($stats['inactive']); ?></div>
                    <div class="stat-label">Inactive</div>
                    <div class="stat-progress"><div class="stat-progress-bar" style="width: <?php echo e($inactivePercent); ?>%"></div></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card year-card">
                <div class="card-body">
                    <div class="stat-icon"><i class="fa fa-calendar-alt"></i></div>
                    <div class="stat-value"><?php echo e($stats['this_year']); ?></div>
                    <div class="stat-label">This Year (<?php echo e(date('Y')); ?>)</div>
                    <div class="stat-progress"><div class="stat-progress-bar" style="width: 100%"></div></div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="toolbar-wrapper">
        <div class="emp-tabs">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(!request('status') ? 'active' : ''); ?>"
                       href="<?php echo e(route('cimsemp201.index', array_merge(request()->except('status', 'page'), []))); ?>">
                        All <span class="badge bg-secondary"><?php echo e($stats['total']); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request('status') === 'active' ? 'active' : ''); ?>"
                       href="<?php echo e(route('cimsemp201.index', array_merge(request()->except('status', 'page'), ['status' => 'active']))); ?>">
                        Active <span class="badge bg-success"><?php echo e($stats['active']); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request('status') === 'inactive' ? 'active' : ''); ?>"
                       href="<?php echo e(route('cimsemp201.index', array_merge(request()->except('status', 'page'), ['status' => 'inactive']))); ?>">
                        Inactive <span class="badge bg-warning text-dark"><?php echo e($stats['inactive']); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request('status') === 'deleted' ? 'active' : ''); ?>"
                       href="<?php echo e(route('cimsemp201.index', array_merge(request()->except('status', 'page'), ['status' => 'deleted']))); ?>">
                        Deleted <span class="badge bg-danger">0</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="search-box-wrapper">
            <form method="GET" action="<?php echo e(route('cimsemp201.index')); ?>" id="searchForm">
                <?php if(request('status')): ?>
                    <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
                <?php endif; ?>
                <?php if(request('client_id')): ?>
                    <input type="hidden" name="client_id" value="<?php echo e(request('client_id')); ?>">
                <?php endif; ?>
                <?php if(request('financial_year')): ?>
                    <input type="hidden" name="financial_year" value="<?php echo e(request('financial_year')); ?>">
                <?php endif; ?>
                <?php if(request('pay_period')): ?>
                    <input type="hidden" name="pay_period" value="<?php echo e(request('pay_period')); ?>">
                <?php endif; ?>
                <div class="input-group">
                    <input type="text" name="search" class="form-control"
                           placeholder="Search by reference, client..."
                           value="<?php echo e(request('search')); ?>">
                    <span class="input-group-text" onclick="document.getElementById('searchForm').submit();">
                        <i class="fa fa-search"></i>
                    </span>
                </div>
            </form>
        </div>

        <a href="<?php echo e(route('cimsemp201.create')); ?>" class="btn-add-emp201">
            <i class="fa fa-plus"></i> Add EMP201
        </a>
    </div>

    
    <div class="filter-card">
        <div class="filter-label"><i class="fa fa-filter"></i> Filters</div>
        <div style="flex: 1; max-width: 250px;">
            <select name="client_id" id="filter_client" class="default-select sd_drop_class" style="width: 100%">
                <option value="">All Clients</option>
                <?php $__currentLoopData = $clients ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($client->client_id); ?>" <?php echo e(request('client_id') == $client->client_id ? 'selected' : ''); ?>>
                        <?php echo e($client->company_name); ?> (<?php echo e($client->client_code); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div style="flex: 1; max-width: 200px;">
            <select name="financial_year" id="filter_financial_year" class="default-select sd_drop_class" style="width: 100%">
                <option value="">All Financial Years</option>
                <?php $__currentLoopData = $financialYears ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($year); ?>" <?php echo e(request('financial_year') == $year ? 'selected' : ''); ?>>
                        <?php echo e($year); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div style="flex: 1; max-width: 200px;">
            <select name="pay_period" id="filter_pay_period" class="default-select sd_drop_class" style="width: 100%">
                <option value="">All Periods</option>
                <?php $__currentLoopData = $payPeriods ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($period); ?>" <?php echo e(request('pay_period') == $period ? 'selected' : ''); ?>>
                        <?php echo e($period); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php if(request('search') || request('client_id') || request('financial_year') || request('pay_period')): ?>
            <a href="<?php echo e(route('cimsemp201.index', request('status') ? ['status' => request('status')] : [])); ?>"
               class="btn-clear-filters">
                <i class="fa fa-times"></i> Clear
            </a>
        <?php endif; ?>
    </div>

    
    <div class="table-card">
        <div class="table-card-header">
            <h5><i class="fa fa-table"></i> EMP201 Declarations</h5>
            <span class="record-count"><?php echo e($declarations->total()); ?> record<?php echo e($declarations->total() != 1 ? 's' : ''); ?></span>
        </div>

        <?php if($declarations->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 emp201-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Year End</th>
                            <th>Client Code</th>
                            <th>Reference</th>
                            <th>Tax Period</th>
                            <th class="text-end">PAYE</th>
                            <th class="text-end">SDL</th>
                            <th class="text-end">UIF</th>
                            <th class="text-end">Liability</th>
                            <th class="text-end">Penalties</th>
                            <th class="text-end">Total Due</th>
                            <th class="text-center">Status</th>
                            <th>Date</th>
                            <th style="width: 60px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $declarations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $declaration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $liability = ($declaration->paye_payable ?? 0) + ($declaration->sdl_payable ?? 0) + ($declaration->uif_payable ?? 0);
                                $penalties = $declaration->penalty_interest ?? 0;

                                // Format currency with space thousand separator
                                $fmtPaye = number_format($declaration->paye_payable ?? 0, 2, '.', ' ');
                                $fmtSdl = number_format($declaration->sdl_payable ?? 0, 2, '.', ' ');
                                $fmtUif = number_format($declaration->uif_payable ?? 0, 2, '.', ' ');
                                $fmtLiability = number_format($liability, 2, '.', ' ');
                                $fmtPenalties = number_format($penalties, 2, '.', ' ');
                                $fmtTotal = number_format($declaration->tax_payable ?? 0, 2, '.', ' ');

                                $isZeroPaye = ($declaration->paye_payable ?? 0) == 0;
                                $isZeroSdl = ($declaration->sdl_payable ?? 0) == 0;
                                $isZeroUif = ($declaration->uif_payable ?? 0) == 0;
                                $isZeroLiab = $liability == 0;
                                $isZeroPen = $penalties == 0;
                            ?>
                            <tr id="emp201-row-<?php echo e($declaration->id); ?>">
                                
                                <td><span class="row-num"><?php echo e($declarations->firstItem() + $index); ?></span></td>

                                
                                <td><span class="year-badge"><?php echo e($declaration->financial_year); ?></span></td>

                                
                                <td><span class="client-code-badge"><?php echo e($declaration->client_code ?? '--'); ?></span></td>

                                
                                <td>
                                    <?php if($declaration->payment_reference): ?>
                                        <span style="font-size:13px; font-weight:600; color:#334155;"><?php echo e($declaration->payment_reference); ?></span>
                                    <?php else: ?>
                                        <span class="ref-text">--</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td>
                                    <?php if($declaration->pay_period): ?>
                                        <span class="period-badge">
                                            <i class="fa fa-calendar-day"></i>
                                            <?php echo e($declaration->pay_period); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="ref-text">--</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="currency-col <?php echo e($isZeroPaye ? 'zero' : ''); ?>"><?php echo e($fmtPaye); ?></td>

                                
                                <td class="currency-col <?php echo e($isZeroSdl ? 'zero' : ''); ?>"><?php echo e($fmtSdl); ?></td>

                                
                                <td class="currency-col <?php echo e($isZeroUif ? 'zero' : ''); ?>"><?php echo e($fmtUif); ?></td>

                                
                                <td class="currency-col <?php echo e($isZeroLiab ? 'zero' : ''); ?>"><?php echo e($fmtLiability); ?></td>

                                
                                <td class="currency-col <?php echo e($isZeroPen ? 'zero' : ''); ?>"><?php echo e($fmtPenalties); ?></td>

                                
                                <td class="total-due-col"><?php echo e($fmtTotal); ?></td>

                                
                                <td class="text-center">
                                    <?php if($declaration->status === 'active' || $declaration->status == 1 || $declaration->is_active == 1): ?>
                                        <span class="status-badge active"><span class="dot"></span> Active</span>
                                    <?php elseif($declaration->status === 'inactive' || $declaration->status == 0 || $declaration->is_active == 0): ?>
                                        <span class="status-badge inactive"><span class="dot"></span> Inactive</span>
                                    <?php elseif($declaration->status === 'deleted'): ?>
                                        <span class="status-badge deleted"><span class="dot"></span> Deleted</span>
                                    <?php else: ?>
                                        <span class="status-badge inactive"><span class="dot"></span> <?php echo e(ucfirst($declaration->status ?? 'Unknown')); ?></span>
                                    <?php endif; ?>
                                </td>

                                
                                <td>
                                    <?php if($declaration->created_at): ?>
                                        <span class="date-text"><?php echo e($declaration->created_at->format('d M Y')); ?></span>
                                    <?php else: ?>
                                        <span class="ref-text">--</span>
                                    <?php endif; ?>
                                </td>

                                
                                <td class="text-center">
                                    <div class="dropdown d-inline-block">
                                        <button class="dropdown-toggle" type="button" data-bs-toggle="dropdown" data-bs-display="static"
                                            style="background:linear-gradient(135deg,#f1f5f9 0%,#e2e8f0 100%);border:none;padding:8px 12px;font-size:16px;color:#475569;border-radius:8px;transition:all .2s ease;cursor:pointer;line-height:1">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            style="border:none;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,.15);padding:8px;min-width:220px;z-index:1050">
                                            <li>
                                                <a class="dropdown-item" href="<?php echo e(route('cimsemp201.show', $declaration->id)); ?>"
                                                   style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px;transition:all .15s ease">
                                                    <i class="fa fa-eye me-2" style="color:#17A2B8"></i> View
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="<?php echo e(route('cimsemp201.edit', $declaration->id)); ?>"
                                                   style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px;transition:all .15s ease">
                                                    <i class="fa fa-edit me-2" style="color:#3b82f6"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="<?php echo e(route('cimsemp201.show', $declaration->id)); ?>?pdf=1"
                                                   style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px;transition:all .15s ease">
                                                    <i class="fa fa-file-pdf me-2" style="color:#dc2626"></i> View PDF
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btn-email-emp201" href="#"
                                                   data-id="<?php echo e($declaration->id); ?>"
                                                   style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px;transition:all .15s ease">
                                                    <i class="fa fa-envelope me-2" style="color:#8b5cf6"></i> Email
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider" style="margin:4px 8px"></li>
                                            <li>
                                                <a class="dropdown-item btn-restore-emp201" href="#"
                                                   data-id="<?php echo e($declaration->id); ?>"
                                                   data-reference="<?php echo e($declaration->payment_reference ?? $declaration->financial_year); ?>"
                                                   style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px;transition:all .15s ease">
                                                    <i class="fa fa-undo me-2" style="color:#059669"></i> Restore
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger btn-delete-emp201" href="#"
                                                   data-id="<?php echo e($declaration->id); ?>"
                                                   data-reference="<?php echo e($declaration->payment_reference ?? $declaration->financial_year); ?>"
                                                   style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px;transition:all .15s ease">
                                                    <i class="fa fa-trash-alt me-2"></i> Delete
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item btn-delete-permanent-emp201" href="#"
                                                   data-id="<?php echo e($declaration->id); ?>"
                                                   data-reference="<?php echo e($declaration->payment_reference ?? $declaration->financial_year); ?>"
                                                   style="border-radius:8px;padding:10px 14px;font-weight:500;font-size:14px;transition:all .15s ease;color:#991b1b">
                                                    <i class="fa fa-ban me-2"></i> Delete Permanently
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            
            <?php if($declarations->hasPages()): ?>
            <div class="pagination-bar">
                <div class="showing-text">
                    Showing <?php echo e($declarations->firstItem()); ?> to <?php echo e($declarations->lastItem()); ?> of <?php echo e($declarations->total()); ?> declarations
                </div>
                <div>
                    <?php echo e($declarations->appends(request()->query())->links('pagination::bootstrap-5')); ?>

                </div>
            </div>
            <?php endif; ?>
        <?php else: ?>
            
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fa fa-file-invoice-dollar"></i>
                </div>
                <h5>No EMP201 declarations found</h5>
                <p>
                    <?php if(request('search') || request('status') || request('client_id') || request('financial_year') || request('pay_period')): ?>
                        No declarations match your current filters. Try adjusting your search criteria.
                    <?php else: ?>
                        Get started by adding your first EMP201 declaration.
                    <?php endif; ?>
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <?php if(request('search') || request('client_id') || request('financial_year') || request('pay_period')): ?>
                        <a href="<?php echo e(route('cimsemp201.index', request('status') ? ['status' => request('status')] : [])); ?>"
                           class="btn-clear-filters">
                            <i class="fa fa-times"></i> Clear Filters
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('cimsemp201.create')); ?>" class="btn-add-emp201">
                        <i class="fa fa-plus"></i> Add EMP201
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Delete Confirmation with SweetAlert2 ─────────────────────────
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
                    fetch('<?php echo e(url("cims/emp201")); ?>/' + empId, {
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

    // ── Restore Confirmation ─────────────────────────────────────────
    document.querySelectorAll('.btn-restore-emp201').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var empId = this.getAttribute('data-id');
            var empRef = this.getAttribute('data-reference');

            Swal.fire({
                title: 'Restore EMP201?',
                text: 'Restore declaration "' + empRef + '"?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, restore it!'
            }).then(function(result) {
                if (result.isConfirmed) {
                    fetch('<?php echo e(url("cims/emp201")); ?>/' + empId + '/status', {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: 1 })
                    })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Restored!', text: 'Declaration has been restored.', confirmButtonColor: '#17A2B8', timer: 2000, timerProgressBar: true });
                            setTimeout(function() { window.location.reload(); }, 1500);
                        } else {
                            Swal.fire('Error', data.message || 'Failed to restore.', 'error');
                        }
                    })
                    .catch(function () { Swal.fire('Error', 'An error occurred. Please try again.', 'error'); });
                }
            });
        });
    });

    // ── Delete Permanently Confirmation ───────────────────────────────
    document.querySelectorAll('.btn-delete-permanent-emp201').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var empId = this.getAttribute('data-id');
            var empRef = this.getAttribute('data-reference');

            Swal.fire({
                title: 'Delete Permanently?',
                html: '<div style="color:#991b1b;font-weight:600">This will permanently remove declaration "' + empRef + '".<br>This action CANNOT be undone!</div>',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#991b1b',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete forever!'
            }).then(function(result) {
                if (result.isConfirmed) {
                    fetch('<?php echo e(url("cims/emp201")); ?>/' + empId + '/force', {
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
                            Swal.fire({ icon: 'success', title: 'Permanently Deleted!', text: 'Declaration removed permanently.', confirmButtonColor: '#17A2B8', timer: 2000, timerProgressBar: true });
                            var row = document.getElementById('emp201-row-' + empId);
                            if (row) {
                                row.style.transition = 'opacity 0.3s ease';
                                row.style.opacity = '0';
                                setTimeout(function () { row.remove(); }, 300);
                            }
                        } else {
                            Swal.fire('Error', data.message || 'Failed to delete permanently.', 'error');
                        }
                    })
                    .catch(function () { Swal.fire('Error', 'An error occurred. Please try again.', 'error'); });
                }
            });
        });
    });

    // ── Email Handler ─────────────────────────────────────────────────
    document.querySelectorAll('.btn-email-emp201').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var empId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Email EMP201',
                html: '<input type="email" id="swal-email" class="swal2-input" placeholder="Recipient email address">',
                showCancelButton: true,
                confirmButtonColor: '#17A2B8',
                confirmButtonText: 'Send Email',
                preConfirm: function() {
                    var email = document.getElementById('swal-email').value;
                    if (!email) { Swal.showValidationMessage('Please enter an email address'); return false; }
                    return email;
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    fetch('<?php echo e(url("cims/emp201")); ?>/' + empId + '/email', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ email: result.value })
                    })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        if (data.success) {
                            Swal.fire({ icon: 'success', title: 'Email Sent!', text: 'EMP201 has been emailed successfully.', confirmButtonColor: '#17A2B8', timer: 2000, timerProgressBar: true });
                        } else {
                            Swal.fire('Error', data.message || 'Failed to send email.', 'error');
                        }
                    })
                    .catch(function () { Swal.fire('Error', 'An error occurred while sending email.', 'error'); });
                }
            });
        });
    });

    // ── SweetAlert2 Flash Messages ────────────────────────────────────
    <?php if(session('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: '<?php echo e(session('success')); ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#17A2B8',
            timer: 3000,
            timerProgressBar: true
        });
    <?php endif; ?>

    <?php if(session('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            html: '<div style="font-size: 16px;"><?php echo addslashes(session('error')); ?></div>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#dc3545'
        });
    <?php endif; ?>

});

// ── Filter auto-submit on dropdown change ──────────────────────────
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

    // ── Fix action dropdown clipping inside table-responsive ─────────
    // Move dropdown menu to body on show, return on hide
    $(document).on('show.bs.dropdown', '.emp201-table .dropdown', function () {
        var $menu = $(this).find('.dropdown-menu');
        var $btn = $(this).find('.dropdown-toggle');
        var btnRect = $btn[0].getBoundingClientRect();

        $menu.css({
            position: 'fixed',
            top: btnRect.bottom + 4 + 'px',
            left: (btnRect.right - 220) + 'px',
            right: 'auto',
            bottom: 'auto',
            zIndex: 1070,
            display: 'block',
            minWidth: '220px'
        });

        $('body').append($menu.detach());
        $(this).data('dropdown-menu', $menu);
    });

    $(document).on('hide.bs.dropdown', '.emp201-table .dropdown', function () {
        var $menu = $(this).data('dropdown-menu');
        if ($menu) {
            $menu.css({ position: '', top: '', left: '', right: '', bottom: '', zIndex: '', display: '', minWidth: '' });
            $(this).append($menu.detach());
            $(this).removeData('dropdown-menu');
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /usr/www/users/smartucbmh/application/Modules/CIMS_EMP201/Resources/views/emp201/index.blade.php ENDPATH**/ ?>