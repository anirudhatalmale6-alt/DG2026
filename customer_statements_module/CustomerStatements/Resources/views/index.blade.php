@extends('layouts.default')

@section('title', 'Customer Statement of Account')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Flatpickr overrides to match CIMS styling */
    .flatpickr-input {
        background: #fff !important;
        cursor: pointer;
    }
    .flatpickr-calendar {
        font-family: 'Poppins', sans-serif;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .flatpickr-day.selected {
        background: #17A2B8 !important;
        border-color: #17A2B8 !important;
    }
    .flatpickr-day:hover {
        background: #e8f4f8 !important;
    }
    /* ============================================
       CUSTOMER STATEMENT OF ACCOUNT
       Matching EMPSA styling pattern
       ============================================ */

    /* --- PAGE WRAPPER --- */
    .stmt-page {
        font-family: 'Poppins', Arial, sans-serif;
        background: #f5f7fa;
        padding: 40px 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* --- HEADER BAR (matching EMPSA) --- */
    .stmt-header {
        background: linear-gradient(135deg, #ffffff 0%, #f0fafb 100%);
        padding: 20px 28px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border-left: 5px solid #17A2B8;
    }
    .stmt-header .stmt-title {
        font-family: 'Poppins', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: #0d3d56;
    }
    .stmt-header .stmt-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        color: #666;
        margin-top: 2px;
    }
    .stmt-header .stmt-badge {
        font-family: 'Poppins', sans-serif;
        font-size: 28px;
        font-weight: 800;
        color: #0d3d56;
        letter-spacing: 2px;
    }

    /* --- SELECTION CARD (matching EMPSA) --- */
    .stmt-selection-card .card-body {
        padding: 24px 28px;
    }
    .stmt-selection-card .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .stmt-selection-card label {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #0d3d56;
        margin-bottom: 6px;
    }
    .stmt-selection-card select,
    .stmt-selection-card input[type="date"] {
        min-height: 48px;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
        border: 1px solid #ced4da;
        padding: 8px 12px;
        width: 100%;
    }
    .stmt-selection-card select:focus,
    .stmt-selection-card input[type="date"]:focus {
        border-color: #17A2B8;
        box-shadow: 0 0 0 0.2rem rgba(23,162,184,0.25);
        outline: none;
    }

    /* Quick select buttons */
    .quick-select-row {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #eef2f7;
    }
    .quick-select-row label {
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        font-weight: 600;
        color: #888;
        margin-right: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .quick-btn {
        display: inline-block;
        padding: 5px 14px;
        margin: 3px 4px;
        border-radius: 20px;
        font-size: 12px;
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid #d0d5dd;
        background: #fff;
        color: #344054;
        transition: all 0.2s;
    }
    .quick-btn:hover {
        background: #17A2B8;
        color: #fff;
        border-color: #17A2B8;
    }
    .quick-btn.active {
        background: #17A2B8;
        color: #fff;
        border-color: #17A2B8;
    }

    /* --- STATEMENT DOCUMENT --- */
    .stmt-document {
        background: #fff;
        border: 2px solid #17A2B8;
        border-radius: 8px;
        max-width: 1200px;
        margin: 30px auto;
        padding: 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    /* Statement Header */
    .stmt-doc-header {
        background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%);
        color: #fff;
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .stmt-doc-header .logo-area img {
        max-height: 60px;
        max-width: 200px;
        filter: brightness(0) invert(1);
    }
    .stmt-doc-header .header-center {
        text-align: center;
    }
    .stmt-doc-header .header-center .title {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 2px;
    }
    .stmt-doc-header .header-center .sub {
        font-size: 12px;
        color: #b0dfe8;
        margin-top: 2px;
    }
    .stmt-doc-header .header-right {
        text-align: right;
        font-size: 11px;
        color: #b0dfe8;
        line-height: 1.6;
    }

    /* Statement body */
    .stmt-doc-body {
        padding: 24px 30px;
    }

    /* Two-column layout */
    .stmt-two-col {
        display: flex;
        gap: 0;
        margin-bottom: 20px;
    }
    .stmt-two-col .col-left {
        flex: 1;
        padding-right: 20px;
    }
    .stmt-two-col .col-right {
        flex: 1;
        padding-left: 20px;
    }
    .stmt-details-box {
        border: 1px solid #c0d0e0;
        padding: 12px 16px;
        font-size: 12px;
        line-height: 1.6;
    }
    .stmt-details-box .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        border-bottom: 1px solid #e8eef4;
    }
    .stmt-details-box .detail-row:last-child {
        border-bottom: none;
    }
    .stmt-details-box .detail-label {
        color: #0d3d56;
        font-weight: 600;
        min-width: 140px;
    }
    .stmt-details-box .detail-value {
        color: #333;
        text-align: right;
    }

    /* Section headers */
    .stmt-section-header {
        background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%);
        color: #fff;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin: 16px 0 0 0;
        border-radius: 2px;
    }

    /* Transaction table */
    .stmt-txn-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        margin-top: 0;
    }
    .stmt-txn-table thead th {
        background: #0d3d56;
        color: #fff;
        font-weight: 700;
        padding: 10px 12px;
        border: 1px solid #0a2e40;
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stmt-txn-table thead th.text-right {
        text-align: right;
    }
    .stmt-txn-table tbody td {
        padding: 8px 12px;
        border: 1px solid #dde4ec;
        color: #333;
        vertical-align: middle;
    }
    .stmt-txn-table tbody td.amount {
        text-align: right;
        font-family: 'Poppins', monospace;
        font-weight: 500;
        white-space: nowrap;
    }
    .stmt-txn-table tbody tr:nth-child(even) {
        background: #fafbfc;
    }
    .stmt-txn-table tbody tr:hover {
        background: #f0f9ff;
    }
    .stmt-txn-table tbody tr.opening-row {
        background: #e8f4f8;
        font-weight: 600;
    }
    .stmt-txn-table tbody tr.opening-row td {
        border-bottom: 2px solid #17A2B8;
        color: #0d3d56;
    }
    .stmt-txn-table tbody tr.totals-row {
        background: #f0f0f0;
        font-weight: 700;
    }
    .stmt-txn-table tbody tr.totals-row td {
        border-top: 2px solid #333;
        color: #333;
    }
    .stmt-txn-table tbody tr.closing-row {
        background: #0d3d56;
    }
    .stmt-txn-table tbody tr.closing-row td {
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        padding: 12px;
        border-color: #0a2e40;
    }

    /* Type badges */
    .type-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .badge-invoice {
        background: #e8f4f8;
        color: #17A2B8;
    }
    .badge-payment {
        background: #d4edda;
        color: #28a745;
    }

    /* Aging table */
    .stmt-aging-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        margin-top: 0;
    }
    .stmt-aging-table th {
        background: #17A2B8;
        color: #fff;
        font-weight: 700;
        padding: 10px 16px;
        border: 1px solid #138496;
        text-align: center;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stmt-aging-table td {
        padding: 12px 16px;
        border: 1px solid #c0d0e0;
        text-align: center;
        font-family: 'Poppins', monospace;
        font-weight: 600;
        font-size: 14px;
    }
    .aging-current { background: #d4edda; color: #155724; }
    .aging-30 { background: #fff3cd; color: #856404; }
    .aging-60 { background: #fce4d6; color: #c0392b; }
    .aging-90 { background: #f8d7da; color: #721c24; }
    .aging-total { background: #0d3d56; color: #fff; font-size: 15px; }

    /* Statement footer */
    .stmt-doc-footer {
        background: #f5f7fa;
        padding: 12px 30px;
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #666;
        border-top: 2px solid #17A2B8;
    }

    /* Email Modal */
    .stmt-email-modal .modal-header {
        background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%);
        color: #fff;
        border-radius: 12px 12px 0 0;
        padding: 16px 24px;
    }
    .stmt-email-modal .modal-header .modal-title {
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        font-weight: 700;
    }
    .stmt-email-modal .modal-header .close {
        color: #fff;
        opacity: 0.8;
        text-shadow: none;
    }
    .stmt-email-modal .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.15);
    }
    .stmt-email-modal .modal-body {
        padding: 24px;
    }
    .stmt-email-modal .modal-body label {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #0d3d56;
        margin-bottom: 6px;
    }
    .stmt-email-modal .modal-body .form-control {
        min-height: 44px;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
    }
    .stmt-email-modal .modal-body textarea.form-control {
        min-height: 100px;
    }
    .stmt-email-modal .modal-footer {
        border-top: 1px solid #eee;
        padding: 16px 24px;
    }

    /* Negative amounts */
    .neg-amount { color: #dc3545; }

    /* Print styles */
    @media print {
        .stmt-header, .stmt-selection-card,
        .cims-header, .cims-footer, .sidebar, .navbar, .breadcrumb,
        #cims_master_menu, #cims_master_header,
        .cims-menu-wrapper, .cims-nav-container, .cims-main-menu,
        #loadingSpinner,
        header, footer, nav,
        .page-header, .page-wrapper > .container-fluid > .row:first-child { display: none !important; }
        .stmt-document {
            border: 2px solid #17A2B8 !important;
            box-shadow: none !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        .stmt-page { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        body { background: #fff !important; }
        .main-content, .page-wrapper, .container-fluid { padding: 0 !important; margin: 0 !important; }
    }
</style>
@endpush

@section('content')
<div class="stmt-page">

    <!-- Page header -->
    <div class="stmt-header">
        <div>
            <div class="stmt-title">Customer Statement of Account</div>
            <div class="stmt-subtitle">Client Invoices &amp; Payments Statement</div>
        </div>
        <div class="stmt-badge">STATEMENT</div>
    </div>

    <!-- Selection filters -->
    <div class="stmt-selection-card mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-end" id="filterRow">
                    <div class="col-md-4">
                        <label>Client</label>
                        <select id="selClient" class="form-control default-select sd_drop_class" data-live-search="true">
                            <option value="">-- Select Client --</option>
                            @foreach ($clients as $c)
                                <option value="{{ $c->client_id }}">{{ $c->client_code ? $c->client_code . ' - ' : '' }}{{ $c->client_company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>From Date</label>
                        <input type="text" id="fromDate" class="form-control" placeholder="dd/mm/yyyy" readonly>
                    </div>
                    <div class="col-md-2">
                        <label>To Date</label>
                        <input type="text" id="toDate" class="form-control" placeholder="dd/mm/yyyy" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <div style="display:flex; gap:8px; flex-wrap:nowrap;">
                            <button class="btn" id="btnGenerate" style="padding:12px 20px; font-size:15px; font-family:Poppins,sans-serif; font-weight:600; background:linear-gradient(135deg,#17A2B8,#138496); color:#fff; border:none; border-radius:8px; min-height:48px; white-space:nowrap;"><i class="fa fa-sync-alt"></i> Generate</button>
                            <button class="btn" id="btnExportPDF" style="display:none; padding:12px 20px; font-size:15px; font-family:Poppins,sans-serif; font-weight:600; border-radius:8px; min-height:48px; background:linear-gradient(135deg,#e91e63,#c2185b); color:#fff; border:none; white-space:nowrap;"><i class="fa fa-file-pdf"></i> PDF</button>
                            <button class="btn" id="btnEmail" style="display:none; padding:12px 20px; font-size:15px; font-family:Poppins,sans-serif; font-weight:600; border-radius:8px; min-height:48px; background:linear-gradient(135deg,#6f42c1,#5a32a3); color:#fff; border:none; white-space:nowrap;"><i class="fa fa-envelope"></i> Email</button>
                        </div>
                    </div>
                </div>
                <!-- Quick-select period buttons -->
                <div class="quick-select-row">
                    <label style="display:inline; vertical-align:middle;">Quick Select:</label>
                    <button type="button" class="quick-btn" data-period="this_month">This Month</button>
                    <button type="button" class="quick-btn" data-period="last_month">Last Month</button>
                    <button type="button" class="quick-btn" data-period="this_quarter">This Quarter</button>
                    <button type="button" class="quick-btn" data-period="last_quarter">Last Quarter</button>
                    <button type="button" class="quick-btn" data-period="last_3_months">Last 3 Months</button>
                    <button type="button" class="quick-btn" data-period="last_6_months">Last 6 Months</button>
                    <button type="button" class="quick-btn" data-period="this_year">This Year</button>
                    <button type="button" class="quick-btn" data-period="last_year">Last Year</button>
                    <button type="button" class="quick-btn" data-period="all">All Transactions</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading spinner -->
    <div id="loadingSpinner" style="display:none; text-align:center; padding:60px;">
        <div class="spinner-border text-info" role="status" style="width:3rem; height:3rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <div style="margin-top:12px; color:#0d3d56; font-weight:600; font-family:Poppins,sans-serif;">Generating Statement of Account...</div>
    </div>

    <!-- Statement container (populated by JS) -->
    <div id="statementContainer" style="display:none;">
        <!-- Will be populated via AJAX -->
    </div>

</div>

<!-- Email Statement Modal -->
<div class="modal fade stmt-email-modal" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel"><i class="fa fa-envelope" style="margin-right:8px;"></i>Email Statement of Account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="emailTo">To (Client Email)</label>
                    <input type="email" class="form-control" id="emailTo" placeholder="client@example.com">
                </div>
                <div class="form-group">
                    <label for="emailSubject">Subject</label>
                    <input type="text" class="form-control" id="emailSubject" placeholder="Statement of Account">
                </div>
                <div class="form-group">
                    <label for="emailMessage">Message (optional)</label>
                    <textarea class="form-control" id="emailMessage" placeholder="Please find attached your Statement of Account."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="padding:10px 20px; font-family:Poppins,sans-serif; font-weight:600; border-radius:8px;">Cancel</button>
                <button type="button" class="btn" id="btnSendEmail" style="padding:10px 20px; font-family:Poppins,sans-serif; font-weight:600; border-radius:8px; background:linear-gradient(135deg,#6f42c1,#5a32a3); color:#fff; border:none;"><i class="fa fa-paper-plane" style="margin-right:6px;"></i>Send Email</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
(function(){
    'use strict';

    var $container = $('#statementContainer');
    var $spinner   = $('#loadingSpinner');
    var lastData   = null;

    // ========== FLATPICKR DATE PICKERS ==========
    var fpFrom = flatpickr('#fromDate', {
        dateFormat: 'd/m/Y',
        defaultDate: '{{ \Carbon\Carbon::parse($defaultFrom)->format("d/m/Y") }}',
        allowInput: false
    });
    var fpTo = flatpickr('#toDate', {
        dateFormat: 'd/m/Y',
        defaultDate: '{{ \Carbon\Carbon::parse($defaultTo)->format("d/m/Y") }}',
        allowInput: false
    });

    // Helper: get date from flatpickr in YYYY-MM-DD for server
    function getFromISO() {
        var d = fpFrom.selectedDates[0];
        return d ? formatDateISO(d) : '';
    }
    function getToISO() {
        var d = fpTo.selectedDates[0];
        return d ? formatDateISO(d) : '';
    }

    function formatDateISO(d) {
        var y = d.getFullYear();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return y + '-' + m + '-' + day;
    }

    // ========== QUICK SELECT BUTTONS ==========
    $('.quick-btn').on('click', function(){
        var period = $(this).data('period');
        var now = new Date();
        var from, to;

        switch(period) {
            case 'this_month':
                from = new Date(now.getFullYear(), now.getMonth(), 1);
                to = now;
                break;
            case 'last_month':
                from = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                to = new Date(now.getFullYear(), now.getMonth(), 0);
                break;
            case 'this_quarter':
                var qm = Math.floor(now.getMonth() / 3) * 3;
                from = new Date(now.getFullYear(), qm, 1);
                to = now;
                break;
            case 'last_quarter':
                var lqm = Math.floor(now.getMonth() / 3) * 3 - 3;
                var lqy = now.getFullYear();
                if (lqm < 0) { lqm += 12; lqy--; }
                from = new Date(lqy, lqm, 1);
                to = new Date(lqy, lqm + 3, 0);
                break;
            case 'last_3_months':
                from = new Date(now.getFullYear(), now.getMonth() - 3, 1);
                to = now;
                break;
            case 'last_6_months':
                from = new Date(now.getFullYear(), now.getMonth() - 6, 1);
                to = now;
                break;
            case 'this_year':
                from = new Date(now.getFullYear(), 0, 1);
                to = now;
                break;
            case 'last_year':
                from = new Date(now.getFullYear() - 1, 0, 1);
                to = new Date(now.getFullYear() - 1, 11, 31);
                break;
            case 'all':
                from = new Date(2000, 0, 1);
                to = now;
                break;
        }

        if (from && to) {
            fpFrom.setDate(from);
            fpTo.setDate(to);

            // Highlight active button
            $('.quick-btn').removeClass('active');
            $(this).addClass('active');
        }
    });

    // ========== GENERATE STATEMENT ==========
    $('#btnGenerate').on('click', function(){
        var clientId = $('#selClient').val();
        var fromDate = getFromISO();
        var toDate   = getToISO();

        if (!clientId) {
            Swal.fire({icon:'warning', title:'Client Required', text:'Please select a client.'});
            return;
        }
        if (!fromDate || !toDate) {
            Swal.fire({icon:'warning', title:'Dates Required', text:'Please select both From and To dates.'});
            return;
        }

        $container.hide();
        $('#btnExportPDF, #btnEmail').hide();
        $spinner.show();

        $.ajax({
            url: '{{ route("statements.generate") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                client_id: clientId,
                from_date: fromDate,
                to_date: toDate
            },
            dataType: 'json',
            success: function(data) {
                $spinner.hide();
                lastData = data;
                renderStatement(data);
                $container.show();
                $('#btnExportPDF, #btnEmail').show();
            },
            error: function(xhr) {
                $spinner.hide();
                var errMsg = 'Failed to generate statement.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        var msgs = [];
                        $.each(xhr.responseJSON.errors, function(k, v) { msgs.push(v.join(', ')); });
                        errMsg = msgs.join('\n');
                    } else if (xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                }
                Swal.fire({icon:'error', title:'Error', text: errMsg});
            }
        });
    });

    // ========== PDF - generate server-side, store in document system, open in viewer ==========
    $('#btnExportPDF').on('click', function(){
        var clientId = $('#selClient').val();
        var fromDate = getFromISO();
        var toDate   = getToISO();
        if (!clientId || !fromDate || !toDate) return;

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');

        $.ajax({
            url: '{{ route("statements.generate-pdf") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                client_id: clientId,
                from_date: fromDate,
                to_date: toDate
            },
            dataType: 'json',
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fa fa-file-pdf"></i> PDF');
                if (resp.view_url) {
                    window.open(resp.view_url, '_blank');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-file-pdf"></i> PDF');
                var errMsg = xhr.responseJSON ? (xhr.responseJSON.error || xhr.responseJSON.message) : 'Failed to generate PDF.';
                Swal.fire({icon:'error', title:'PDF Error', text: errMsg});
            }
        });
    });

    // ========== EMAIL BUTTON ==========
    $('#btnEmail').on('click', function(){
        if (!lastData) return;
        var clientEmail = lastData.client_email || '';
        var clientName  = lastData.client ? lastData.client.client_company_name : '';
        var fromDate = getFromISO();
        var toDate   = getToISO();

        $('#emailTo').val(clientEmail);
        $('#emailSubject').val('Statement of Account - ' + clientName + ' (' + formatDisplayDate(fromDate) + ' to ' + formatDisplayDate(toDate) + ')');
        $('#emailMessage').val('Dear ' + clientName + ',\n\nPlease find attached your Statement of Account for the period ' + formatDisplayDate(fromDate) + ' to ' + formatDisplayDate(toDate) + '.\n\nKind regards');
        $('#emailModal').modal('show');
    });

    // ========== SEND EMAIL ==========
    $('#btnSendEmail').on('click', function(){
        var emailTo = $('#emailTo').val().trim();
        if (!emailTo) {
            Swal.fire({icon:'warning', title:'Email Required', text:'Please enter a valid email address.'});
            return;
        }

        var clientId = $('#selClient').val();
        var fromDate = getFromISO();
        var toDate   = getToISO();
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            url: '/statements/' + clientId + '/email',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                from_date: fromDate,
                to_date: toDate
            },
            dataType: 'json',
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane" style="margin-right:6px;"></i>Send Email');
                $('#emailModal').modal('hide');
                Swal.fire({icon:'success', title:'Email Sent', text: resp.message || 'Statement has been emailed successfully.'});
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane" style="margin-right:6px;"></i>Send Email');
                var errMsg = xhr.responseJSON ? (xhr.responseJSON.error || xhr.responseJSON.message) : 'Failed to send email.';
                Swal.fire({icon:'error', title:'Email Error', text: errMsg});
            }
        });
    });

    // ========== RENDER STATEMENT ==========
    function renderStatement(data) {
        var client = data.client;
        var settings = data.settings;
        var transactions = data.transactions;
        var aging = data.aging;
        var currency = settings.settings_system_currency_symbol || 'R ';

        var html = '';
        html += '<div class="stmt-document">';

        // === DOCUMENT HEADER ===
        html += '<div class="stmt-doc-header">';
        html += '  <div class="logo-area"><img src="/storage/logos/app/cims_inv_logo.png" alt="Logo" onerror="this.style.display=\'none\'"></div>';
        html += '  <div class="header-center">';
        html += '    <div class="title">STATEMENT OF ACCOUNT</div>';
        html += '    <div class="sub">Client Invoice &amp; Payment Statement</div>';
        html += '  </div>';
        html += '  <div class="header-right">';
        html += '    <div style="font-weight:700;font-size:13px;color:#fff;">' + esc(settings.settings_company_name) + '</div>';
        html += '    <div>' + esc(settings.settings_company_address_line_1) + '</div>';
        html += '    <div>' + esc(settings.settings_company_city) + ', ' + esc(settings.settings_company_state) + '</div>';
        html += '    <div>' + esc(settings.settings_company_customfield_1) + '</div>';
        html += '  </div>';
        html += '</div>';

        // === DOCUMENT BODY ===
        html += '<div class="stmt-doc-body">';

        // Client details + statement info
        html += '<div class="stmt-two-col">';
        // Left: Client
        html += '<div class="col-left">';
        html += '  <div style="font-weight:700;font-size:14px;color:#0d3d56;margin-bottom:6px;">Bill To:</div>';
        html += '  <div style="font-weight:700;font-size:13px;color:#333;">' + esc(client.client_company_name) + '</div>';
        if (data.client_code) {
            html += '  <div style="font-size:12px;color:#666;">Client Code: ' + esc(data.client_code) + '</div>';
        }
        // Line 1: Address (street + suburb), Zip Code
        var addrLine1 = '';
        if (client.client_billing_street) addrLine1 += esc(client.client_billing_street);
        if (client.client_billing_zip) addrLine1 += (addrLine1 ? ', ' : '') + esc(client.client_billing_zip);
        if (addrLine1) html += '  <div style="font-size:12px;color:#333;">' + addrLine1 + '</div>';
        // Line 2: City, State, Country
        var addrLine2 = '';
        if (client.client_billing_city) addrLine2 += esc(client.client_billing_city);
        if (client.client_billing_state) addrLine2 += (addrLine2 ? ', ' : '') + esc(client.client_billing_state);
        if (client.client_billing_country) addrLine2 += (addrLine2 ? ', ' : '') + esc(client.client_billing_country);
        if (addrLine2) html += '  <div style="font-size:12px;color:#333;">' + addrLine2 + '</div>';
        if (client.client_vat) {
            html += '  <div style="font-size:12px;color:#666;margin-top:4px;">VAT: ' + esc(client.client_vat) + '</div>';
        }
        html += '</div>';
        // Right: Statement details
        html += '<div class="col-right">';
        html += '  <div class="stmt-details-box">';
        html += '    <div class="detail-row"><span class="detail-label">Statement Date:</span><span class="detail-value">' + formatDisplayDate(new Date().toISOString().split('T')[0]) + '</span></div>';
        html += '    <div class="detail-row"><span class="detail-label">Period:</span><span class="detail-value">' + formatDisplayDate(data.from_date) + ' to ' + formatDisplayDate(data.to_date) + '</span></div>';
        if (data.client_email) {
            html += '    <div class="detail-row"><span class="detail-label">Contact Email:</span><span class="detail-value">' + esc(data.client_email) + '</span></div>';
        }
        if (data.contact_name) {
            html += '    <div class="detail-row"><span class="detail-label">Contact:</span><span class="detail-value">' + esc(data.contact_name) + '</span></div>';
        }
        html += '  </div>';
        html += '</div>';
        html += '</div>'; // end two-col

        // === TRANSACTIONS ===
        html += '<div class="stmt-section-header">Transaction Details</div>';
        html += '<table class="stmt-txn-table">';
        html += '<thead><tr>';
        html += '  <th style="width:100px;">Date</th>';
        html += '  <th style="width:90px;">Type</th>';
        html += '  <th style="width:130px;">Reference</th>';
        html += '  <th>Description</th>';
        html += '  <th class="text-right" style="width:120px;">Debit</th>';
        html += '  <th class="text-right" style="width:120px;">Credit</th>';
        html += '  <th class="text-right" style="width:130px;">Balance</th>';
        html += '</tr></thead>';
        html += '<tbody>';

        // Opening balance
        html += '<tr class="opening-row">';
        html += '  <td>' + formatDisplayDate(data.from_date) + '</td>';
        html += '  <td></td>';
        html += '  <td></td>';
        html += '  <td><strong>Opening Balance</strong></td>';
        html += '  <td></td>';
        html += '  <td></td>';
        html += '  <td class="amount"><strong>' + fmtCur(data.opening_balance, currency) + '</strong></td>';
        html += '</tr>';

        // Transaction rows
        if (transactions.length === 0) {
            html += '<tr><td colspan="7" style="text-align:center;padding:30px;color:#999;">No transactions found for this period.</td></tr>';
        } else {
            for (var i = 0; i < transactions.length; i++) {
                var txn = transactions[i];
                var badgeClass = txn.type === 'Invoice' ? 'badge-invoice' : 'badge-payment';
                var refStyle = txn.type === 'Invoice' ? 'color:#17A2B8;font-weight:500;' : 'color:#28a745;font-weight:500;';

                html += '<tr>';
                html += '  <td>' + formatDisplayDate(txn.date) + '</td>';
                html += '  <td><span class="type-badge ' + badgeClass + '">' + esc(txn.type) + '</span></td>';
                html += '  <td style="' + refStyle + '">' + esc(txn.reference) + '</td>';
                html += '  <td>' + esc(txn.description) + '</td>';
                html += '  <td class="amount">' + (txn.debit > 0 ? fmtCur(txn.debit, currency) : '') + '</td>';
                html += '  <td class="amount">' + (txn.credit > 0 ? fmtCur(txn.credit, currency) : '') + '</td>';
                html += '  <td class="amount">' + fmtCur(txn.balance, currency) + '</td>';
                html += '</tr>';
            }
        }

        // Totals row
        html += '<tr class="totals-row">';
        html += '  <td colspan="4" style="text-align:right;"><strong>Totals:</strong></td>';
        html += '  <td class="amount"><strong>' + fmtCur(data.total_debits, currency) + '</strong></td>';
        html += '  <td class="amount"><strong>' + fmtCur(data.total_credits, currency) + '</strong></td>';
        html += '  <td></td>';
        html += '</tr>';

        // Closing balance
        html += '<tr class="closing-row">';
        html += '  <td colspan="6" style="text-align:right;">Closing Balance:</td>';
        html += '  <td class="amount">' + fmtCur(data.closing_balance, currency) + '</td>';
        html += '</tr>';

        html += '</tbody></table>';

        // === AGING ===
        if (aging) {
            html += '<div class="stmt-section-header">Aging Summary - Outstanding amounts aged by due date</div>';
            html += '<table class="stmt-aging-table">';
            html += '<thead><tr>';
            html += '  <th>90+ Days</th>';
            html += '  <th>60 Days (61-90)</th>';
            html += '  <th>30 Days (31-60)</th>';
            html += '  <th>Current (0-30 days)</th>';
            html += '  <th>Total Outstanding</th>';
            html += '</tr></thead>';
            html += '<tbody><tr>';
            html += '  <td class="aging-90">' + fmtCur(aging.buckets['90_plus'], currency) + '</td>';
            html += '  <td class="aging-60">' + fmtCur(aging.buckets['60_days'], currency) + '</td>';
            html += '  <td class="aging-30">' + fmtCur(aging.buckets['30_days'], currency) + '</td>';
            html += '  <td class="aging-current">' + fmtCur(aging.buckets.current, currency) + '</td>';
            html += '  <td class="aging-total">' + fmtCur(aging.total, currency) + '</td>';
            html += '</tr></tbody></table>';
        }

        html += '</div>'; // stmt-doc-body

        // === FOOTER ===
        html += '<div class="stmt-doc-footer">';
        html += '  <div>' + esc(settings.settings_company_name) + '</div>';
        html += '  <div>Statement of Account</div>';
        html += '  <div>Generated: ' + formatDisplayDate(new Date().toISOString().split('T')[0]) + '</div>';
        html += '</div>';

        html += '</div>'; // stmt-document

        $container.html(html);
    }

    // ========== HELPERS ==========
    function fmtCur(val, symbol) {
        var n = parseFloat(val) || 0;
        var formatted = Math.abs(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        if (n < 0) return '<span class="neg-amount">-' + symbol + formatted + '</span>';
        return symbol + formatted;
    }

    function formatDisplayDate(dateStr) {
        if (!dateStr) return '';
        var parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0]; // DD/MM/YYYY
        }
        return dateStr;
    }

    function esc(str) {
        if (!str) return '';
        return $('<div/>').text(str).html();
    }

})();
</script>
@endpush
