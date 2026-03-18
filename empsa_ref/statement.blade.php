@extends('layouts.default')

@section('title', 'EMPSA - Statement of Account')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* ============================================
       EMPSA STATEMENT OF ACCOUNT
       Matching SARS official styling
       ============================================ */

    /* --- PAGE WRAPPER --- */
    .empsa-page {
        font-family: 'Poppins', Arial, sans-serif;
        background: #f5f7fa;
        padding: 40px 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* --- SELECTION CARD (same style as pivot) --- */
    .empsa-selection-card .card-body {
        padding: 24px 28px;
    }
    .empsa-selection-card .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .empsa-selection-card label {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #0d3d56;
        margin-bottom: 6px;
    }
    .empsa-selection-card select {
        min-height: 48px;
        border-radius: 8px;
        font-size: 14px;
    }

    /* --- SARS STATEMENT DOCUMENT --- */
    .sars-statement {
        background: #fff;
        border: 2px solid #003366;
        border-radius: 4px;
        max-width: 1200px;
        margin: 40px auto;
        padding: 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    /* SARS Header */
    .sars-header {
        background: linear-gradient(135deg, #003366 0%, #004488 100%);
        color: #fff;
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .sars-header .sars-logo-area {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .sars-header .sars-logo-area .sars-text {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: 3px;
        color: #fff;
    }
    .sars-header .sars-logo-area .sars-sub {
        font-size: 11px;
        color: #aaccee;
        margin-top: 2px;
    }
    .sars-header .header-right {
        text-align: right;
    }
    .sars-header .header-right .empsa-label {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: 2px;
    }
    .sars-header .header-right .empsa-subtitle {
        font-size: 12px;
        color: #aaccee;
    }

    /* Body content */
    .sars-body {
        padding: 24px 30px;
    }

    /* Section headers (blue banner) */
    .sars-section-header {
        background: linear-gradient(135deg, #003366 0%, #004488 100%);
        color: #fff;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin: 16px 0 0 0;
        border-radius: 2px;
    }

    /* Details boxes */
    .sars-details-box {
        border: 1px solid #c0d0e0;
        padding: 12px 16px;
        margin-bottom: 0;
        font-size: 12px;
        line-height: 1.6;
    }
    .sars-details-box .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 3px 0;
        border-bottom: 1px solid #e8eef4;
    }
    .sars-details-box .detail-row:last-child {
        border-bottom: none;
    }
    .sars-details-box .detail-label {
        color: #003366;
        font-weight: 600;
        min-width: 180px;
    }
    .sars-details-box .detail-value {
        color: #333;
        text-align: right;
    }

    /* Two-column layout */
    .sars-two-col {
        display: flex;
        gap: 0;
    }
    .sars-two-col .col-left {
        flex: 1;
        padding-right: 20px;
    }
    .sars-two-col .col-right {
        flex: 1;
        padding-left: 20px;
    }

    /* Summary table */
    .sars-summary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        margin-top: 0;
    }
    .sars-summary-table th {
        background: #e8eef4;
        color: #003366;
        font-weight: 700;
        padding: 8px 12px;
        text-align: left;
        border: 1px solid #c0d0e0;
    }
    .sars-summary-table td {
        padding: 6px 12px;
        border: 1px solid #c0d0e0;
        color: #333;
    }
    .sars-summary-table td.amount {
        text-align: right;
        font-family: 'Poppins', Arial, sans-serif;
        font-weight: 500;
    }
    .sars-summary-table tr.closing-balance {
        background: #e8eef4;
        font-weight: 700;
    }
    .sars-summary-table tr.closing-balance td {
        color: #003366;
        font-weight: 700;
        border-top: 2px solid #003366;
    }

    /* Status info table */
    .sars-status-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .sars-status-table th {
        background: #e8eef4;
        color: #003366;
        font-weight: 700;
        padding: 6px 10px;
        border: 1px solid #c0d0e0;
        text-align: center;
    }
    .sars-status-table td {
        padding: 6px 10px;
        border: 1px solid #c0d0e0;
        text-align: center;
        color: #333;
    }
    .status-active { color: #28a745; font-weight: 700; }
    .status-not-registered { color: #dc3545; font-weight: 600; font-size: 11px; }

    /* Transaction details table */
    .sars-txn-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
        margin-top: 0;
    }
    .sars-txn-table thead th {
        background: #003366;
        color: #fff;
        font-weight: 700;
        padding: 8px 8px;
        border: 1px solid #002244;
        text-align: center;
        font-size: 10px;
        white-space: nowrap;
    }
    .sars-txn-table thead th.sub-header {
        background: #e8eef4;
        color: #003366;
        font-size: 10px;
    }
    .sars-txn-table tbody td {
        padding: 4px 8px;
        border: 1px solid #dde4ec;
        color: #333;
        vertical-align: top;
    }
    .sars-txn-table tbody td.amount {
        text-align: right;
        font-family: 'Poppins', Arial, sans-serif;
        font-weight: 500;
        font-size: 11px;
        white-space: nowrap;
    }
    .sars-txn-table tbody td.date-col {
        white-space: nowrap;
        font-size: 11px;
    }
    .sars-txn-table tbody td.desc-col {
        font-size: 11px;
    }
    .sars-txn-table tbody tr.period-balance {
        background: #e8eef4;
        font-weight: 700;
    }
    .sars-txn-table tbody tr.period-balance td {
        color: #003366;
        font-weight: 700;
        border-top: 2px solid #003366;
        border-bottom: 2px solid #003366;
    }
    .sars-txn-table tbody tr.cumulative-row {
        background: #d4e5f7;
        font-weight: 700;
    }
    .sars-txn-table tbody tr.cumulative-row td {
        color: #003366;
        font-weight: 700;
        border-top: 3px solid #003366;
        font-size: 12px;
    }
    .sars-txn-table tbody tr.total-liability-row {
        background: #f5f7fa;
    }
    .sars-txn-table tbody tr.total-liability-row td {
        font-weight: 600;
        border-top: 1px solid #99aabb;
    }
    .sars-txn-table tbody tr.financial-movement-row td {
        font-style: italic;
        color: #666;
    }

    /* Aging table */
    .sars-aging-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        margin-top: 0;
    }
    .sars-aging-table th {
        background: #003366;
        color: #fff;
        font-weight: 700;
        padding: 8px 12px;
        border: 1px solid #002244;
        text-align: center;
    }
    .sars-aging-table td {
        padding: 6px 12px;
        border: 1px solid #c0d0e0;
        text-align: right;
        font-family: 'Poppins', Arial, sans-serif;
        font-weight: 500;
    }

    /* Compliance table */
    .sars-compliance-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }
    .sars-compliance-table td {
        padding: 8px 12px;
        border: 1px solid #c0d0e0;
        color: #333;
    }
    .sars-compliance-table td:first-child {
        font-weight: 600;
        color: #003366;
        width: 220px;
    }

    /* Negative amounts */
    .neg-amount { color: #dc3545; }

    /* --- HEADER BAR (matching pivot) --- */
    .empsa-header {
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
    .empsa-header .empsa-title {
        font-family: 'Poppins', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: #0d3d56;
    }
    .empsa-header .empsa-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        color: #666;
        margin-top: 2px;
    }
    .empsa-header .empsa-badge {
        font-family: 'Poppins', sans-serif;
        font-size: 34px;
        font-weight: 800;
        color: #0d3d56;
        letter-spacing: 3px;
    }

    /* Print/PDF button bar */
    .empsa-action-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
        justify-content: flex-end;
    }

    /* Print styles */
    @media print {
        .empsa-header, .empsa-selection-card, .empsa-action-bar,
        .cims-header, .cims-footer, .sidebar, .navbar, .breadcrumb,
        #cims_master_menu, #cims_master_header,
        .cims-menu-wrapper, .cims-nav-container, .cims-main-menu,
        #loadingSpinner,
        header, footer, nav,
        .page-header, .page-wrapper > .container-fluid > .row:first-child { display: none !important; }
        .sars-statement {
            border: 2px solid #003366 !important;
            box-shadow: none !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        .empsa-page { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        body { background: #fff !important; }
        .main-content, .page-wrapper, .container-fluid { padding: 0 !important; margin: 0 !important; }
        #statementContainer { display: block !important; }
    }

    /* Footer */
    .sars-footer {
        background: #f5f7fa;
        padding: 12px 30px;
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #666;
        border-top: 2px solid #003366;
    }

    /* Email Modal */
    .empsa-email-modal .modal-header {
        background: linear-gradient(135deg, #003366 0%, #004488 100%);
        color: #fff;
        border-radius: 12px 12px 0 0;
        padding: 16px 24px;
    }
    .empsa-email-modal .modal-header .modal-title {
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        font-weight: 700;
    }
    .empsa-email-modal .modal-header .close {
        color: #fff;
        opacity: 0.8;
        text-shadow: none;
    }
    .empsa-email-modal .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 40px rgba(0,0,0,0.15);
    }
    .empsa-email-modal .modal-body {
        padding: 24px;
    }
    .empsa-email-modal .modal-body label {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 600;
        color: #0d3d56;
        margin-bottom: 6px;
    }
    .empsa-email-modal .modal-body .form-control {
        min-height: 44px;
        border-radius: 8px;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
    }
    .empsa-email-modal .modal-body textarea.form-control {
        min-height: 100px;
    }
    .empsa-email-modal .modal-footer {
        border-top: 1px solid #eee;
        padding: 16px 24px;
    }
    .empsa-email-modal .custom-checkbox {
        margin-top: 12px;
    }
    .empsa-email-modal .custom-checkbox .custom-control-label {
        font-size: 13px;
        font-weight: 500;
        color: #333;
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="empsa-page">

    <!-- Page header -->
    <div class="empsa-header">
        <div>
            <div class="empsa-title">Employment Taxes - Statement of Account</div>
            <div class="empsa-subtitle">SARS EMPSA Report per Client</div>
        </div>
        <div class="empsa-badge">EMPSA</div>
    </div>

    <!-- Selection filters -->
    <div class="empsa-selection-card mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-end" id="filterRow">
                    <div class="col-md-4">
                        <label>Client</label>
                        <select id="selClient" class="form-control default-select sd_drop_class" data-live-search="true">
                            <option value="">-- Select Client --</option>
                            @foreach ($clients as $c)
                                <option value="{{ $c->client_id }}">{{ $c->client_code }} - {{ $c->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Tax Year</label>
                        <select id="selTaxYear" class="form-control default-select sd_drop_class">
                            <option value="">-- Select Tax Year --</option>
                            @foreach ($taxYears as $y)
                                <option value="{{ $y }}">{{ $y }} (Mar {{ $y - 1 }} - Feb {{ $y }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label>&nbsp;</label>
                        <div style="display:flex; gap:8px; flex-wrap:nowrap;">
                            <button class="btn" id="btnLoad" style="padding:12px 20px; font-size:15px; font-family:Poppins,sans-serif; font-weight:600; background:linear-gradient(135deg,#17A2B8,#138496); color:#fff; border:none; border-radius:8px; min-height:48px;"><i class="fa fa-sync-alt"></i> Generate</button>
                            <button class="btn btn-success" id="btnExportXlsx" style="display:none; padding:12px 20px; font-size:15px; font-family:Poppins,sans-serif; font-weight:600; border-radius:8px; min-height:48px;"><i class="fa fa-file-excel"></i> Excel</button>
                            <button class="btn" id="btnExportPDF" style="display:none; padding:12px 20px; font-size:15px; font-family:Poppins,sans-serif; font-weight:600; border-radius:8px; min-height:48px; background:linear-gradient(135deg,#e91e63,#c2185b); color:#fff; border:none;"><i class="fa fa-file-pdf"></i> PDF</button>
                            <button class="btn" id="btnEmail" style="display:none; padding:12px 20px; font-size:15px; font-family:Poppins,sans-serif; font-weight:600; border-radius:8px; min-height:48px; background:linear-gradient(135deg,#6f42c1,#5a32a3); color:#fff; border:none;"><i class="fa fa-envelope"></i> Email</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statement container (populated by JS) -->
    <div id="statementContainer" style="display:none;">
        <!-- Will be populated via AJAX -->
    </div>

    <!-- Loading spinner -->
    <div id="loadingSpinner" style="display:none; text-align:center; padding:60px;">
        <div class="spinner-border text-info" role="status" style="width:3rem; height:3rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <div style="margin-top:12px; color:#0d3d56; font-weight:600;">Generating Statement of Account...</div>
    </div>
</div>

<!-- Email Statement Modal -->
<div class="modal fade empsa-email-modal" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
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
                    <input type="text" class="form-control" id="emailSubject" placeholder="EMPSA Statement of Account">
                </div>
                <div class="form-group">
                    <label for="emailMessage">Message (optional)</label>
                    <textarea class="form-control" id="emailMessage" placeholder="Please find attached your EMPSA Statement of Account."></textarea>
                </div>
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="emailSendCopy">
                    <label class="custom-control-label" for="emailSendCopy">Send me a copy</label>
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
<script>
(function(){
    'use strict';

    var $container = $('#statementContainer');
    var $spinner   = $('#loadingSpinner');
    var lastData   = null; // store last loaded data for exports

    // Generate statement
    $('#btnLoad').on('click', function(){
        var clientId = $('#selClient').val();
        var taxYear  = $('#selTaxYear').val();

        if (!clientId || !taxYear) {
            Swal.fire({icon:'warning', title:'Selection Required', text:'Please select a client and tax year.'});
            return;
        }

        $container.hide();
        $('#btnExportXlsx, #btnExportPDF, #btnEmail').hide();
        $spinner.show();

        $.ajax({
            url: '{{ route("cimsemp201.api.statement-data") }}',
            data: { client_id: clientId, tax_year: taxYear },
            dataType: 'json',
            success: function(data) {
                $spinner.hide();
                lastData = data;
                renderStatement(data);
                $container.show();
                $('#btnExportXlsx, #btnExportPDF, #btnEmail').show();
            },
            error: function(xhr) {
                $spinner.hide();
                Swal.fire({icon:'error', title:'Error', text: xhr.responseJSON ? xhr.responseJSON.error : 'Failed to load statement data.'});
            }
        });
    });

    // PDF - generate server-side, store in document system, open in viewer
    $('#btnExportPDF').on('click', function(){
        var clientId = $('#selClient').val();
        var taxYear  = $('#selTaxYear').val();
        if (!clientId || !taxYear) return;

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');

        $.ajax({
            url: '{{ route("cimsemp201.statement.generate-pdf") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                client_id: clientId,
                tax_year: taxYear
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

    // Excel export - server-side styled .xlsx
    $('#btnExportXlsx').on('click', function(){
        var clientId = $('#selClient').val();
        var taxYear  = $('#selTaxYear').val();
        if (!clientId || !taxYear) return;
        window.location.href = '{{ route("cimsemp201.statement.export-excel") }}?client_id=' + clientId + '&tax_year=' + taxYear;
    });

    // Email button - open modal with pre-populated client email
    $('#btnEmail').on('click', function(){
        if (!lastData) return;
        var clientEmail = lastData.client.email || '';
        var clientName  = lastData.client.company_name || '';
        var taxYear     = lastData.tax_year || '';
        $('#emailTo').val(clientEmail);
        $('#emailSubject').val('EMPSA Statement of Account - ' + clientName + ' - Tax Year ' + taxYear);
        $('#emailMessage').val('Dear ' + clientName + ',\n\nPlease find attached your Employment Taxes Statement of Account (EMPSA) for Tax Year ' + taxYear + '.\n\nKind regards');
        $('#emailSendCopy').prop('checked', false);
        $('#emailModal').modal('show');
    });

    // Send email
    $('#btnSendEmail').on('click', function(){
        var emailTo   = $('#emailTo').val().trim();
        var subject   = $('#emailSubject').val().trim();
        var message   = $('#emailMessage').val().trim();
        var sendCopy  = $('#emailSendCopy').is(':checked') ? 1 : 0;
        var clientId  = $('#selClient').val();
        var taxYear   = $('#selTaxYear').val();

        if (!emailTo) {
            Swal.fire({icon:'warning', title:'Email Required', text:'Please enter a valid email address.'});
            return;
        }

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            url: '{{ route("cimsemp201.statement.send-email") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                client_id: clientId,
                tax_year: taxYear,
                email_to: emailTo,
                subject: subject,
                message: message,
                send_copy: sendCopy
            },
            dataType: 'json',
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane" style="margin-right:6px;"></i>Send Email');
                $('#emailModal').modal('hide');
                Swal.fire({icon:'success', title:'Email Sent', text: resp.message || 'Statement has been emailed successfully.'});
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane" style="margin-right:6px;"></i>Send Email');
                var errMsg = xhr.responseJSON ? (xhr.responseJSON.error || xhr.responseJSON.message) : 'Failed to send email. Please try again.';
                Swal.fire({icon:'error', title:'Email Error', text: errMsg});
            }
        });
    });

    function fmt(val) {
        var n = parseFloat(val) || 0;
        return n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    }

    function fmtNeg(val) {
        var n = parseFloat(val) || 0;
        var s = Math.abs(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        if (n < 0) return '<span class="neg-amount">-' + s + '</span>';
        return s;
    }

    function renderStatement(data) {
        var client = data.client;
        var periods = data.periods;
        var summary = data.summary;
        var aging = data.aging;
        var today = data.today;
        var prevYear = parseInt(data.tax_year) - 1;

        var html = '';
        html += '<div class="sars-statement">';

        // ========== HEADER ==========
        html += '<div class="sars-header">';
        html += '  <div class="sars-logo-area">';
        html += '    <div>';
        html += '      <div class="sars-text">SARS</div>';
        html += '      <div class="sars-sub">South African Revenue Service</div>';
        html += '    </div>';
        html += '  </div>';
        html += '  <div style="text-align:center;">';
        html += '    <div style="font-size:16px; font-weight:700; letter-spacing:2px;">EMPLOYMENT TAXES</div>';
        html += '    <div style="font-size:12px; color:#aaccee; margin-top:2px;">Statement of Account</div>';
        html += '  </div>';
        html += '  <div class="header-right">';
        html += '    <div class="empsa-label">EMPSA</div>';
        html += '  </div>';
        html += '</div>';

        // ========== BODY ==========
        html += '<div class="sars-body">';

        // Contact + Client address
        html += '<div class="sars-two-col" style="margin-bottom:16px;">';
        // Left: Client name/address
        html += '<div class="col-left">';
        html += '  <div style="font-weight:700; font-size:13px; color:#003366; margin-bottom:8px;">' + esc(client.company_name) + '</div>';
        if (client.address) {
            if (client.address.street_number || client.address.street_name) {
                html += '  <div style="font-size:12px; color:#333;">' + esc((client.address.street_number || '') + ' ' + (client.address.street_name || '')).trim() + '</div>';
            }
            if (client.address.suburb || client.address.postal_code) {
                var suburbLine = esc(client.address.suburb || '');
                if (client.address.postal_code) {
                    suburbLine += (suburbLine ? ', ' : '') + esc(client.address.postal_code);
                }
                html += '  <div style="font-size:12px; color:#333;">' + suburbLine + '</div>';
            }
            if (client.address.city || client.address.province) {
                html += '  <div style="font-size:12px; color:#333;">' + esc((client.address.city || '') + (client.address.province ? ', ' + client.address.province : '')) + '</div>';
            }
        }
        html += '</div>';
        // Right: Details
        html += '<div class="col-right">';
        html += '  <div class="sars-details-box">';
        html += '    <div class="detail-row"><span class="detail-label">Reference number:</span><span class="detail-value">' + esc(client.paye_number || 'N/A') + '</span></div>';
        html += '    <div class="detail-row"><span class="detail-label">Date:</span><span class="detail-value">' + esc(today) + '</span></div>';
        html += '    <div class="detail-row"><span class="detail-label">Statement period:</span><span class="detail-value">' + prevYear + '/03/01 to ' + data.tax_year + '/02/28</span></div>';
        html += '  </div>';
        html += '</div>';
        html += '</div>'; // end two-col

        // ========== SUMMARY ==========
        html += '<div class="sars-section-header">Summary Information: Employer Reconciliation</div>';
        html += '<table class="sars-summary-table">';
        html += '<tr><td>PAYE/SDL/UIF YEAR ' + prevYear + '</td><td class="amount">' + fmt(summary.prev_year_balance) + '</td></tr>';
        html += '<tr><td>PAYE/SDL/UIF YEAR ' + data.tax_year + '</td><td class="amount">' + fmt(summary.current_year_balance) + '</td></tr>';
        html += '<tr><td>UNALLOCATED PAYMENTS</td><td class="amount">' + fmt(0) + '</td></tr>';
        html += '<tr class="closing-balance"><td>CLOSING BALANCE</td><td class="amount">' + fmt(summary.closing_balance) + '</td></tr>';
        html += '</table>';

        // ========== STATUS INFO ==========
        html += '<div class="sars-section-header">Status Information</div>';
        html += '<table class="sars-status-table">';
        html += '<thead><tr>';
        html += '  <th colspan="2">PAYE</th><th colspan="2">SDL</th><th colspan="2">UIF</th>';
        html += '</tr><tr>';
        html += '  <th>Status</th><th>Effective Date</th><th>Status</th><th>Effective Date</th><th>Status</th><th>Effective Date</th>';
        html += '</tr></thead>';
        html += '<tbody><tr>';
        html += '  <td><span class="' + (client.paye_number ? 'status-active' : 'status-not-registered') + '">' + (client.paye_number ? 'ACTIVE' : 'NOT REGISTERED') + '</span></td>';
        html += '  <td>' + esc(client.tax_reg_date || '') + '</td>';
        html += '  <td><span class="' + (client.sdl_number ? 'status-active' : 'status-not-registered') + '">' + (client.sdl_number ? 'ACTIVE' : 'NOT REGISTERED') + '</span></td>';
        html += '  <td>' + esc(client.tax_reg_date || '') + '</td>';
        html += '  <td><span class="' + (client.uif_number ? 'status-active' : 'status-not-registered') + '">' + (client.uif_number ? 'ACTIVE' : 'NOT REGISTERED') + '</span></td>';
        html += '  <td>' + esc(client.tax_reg_date || '') + '</td>';
        html += '</tr></tbody>';
        html += '</table>';

        // ========== TRANSACTION DETAILS ==========
        html += '<div class="sars-section-header">Transaction Details</div>';
        html += '<table class="sars-txn-table">';
        html += '<thead>';
        html += '<tr>';
        html += '  <th rowspan="2" style="width:80px;">Date</th>';
        html += '  <th rowspan="2" style="width:130px;">Transaction<br>Reference</th>';
        html += '  <th rowspan="2">Transaction Description</th>';
        html += '  <th rowspan="2" style="width:100px;">Transaction<br>Value</th>';
        html += '  <th colspan="4" style="border-bottom:2px solid #fff;">Transaction Allocation Information</th>';
        html += '  <th rowspan="2" style="width:100px;">Account<br>Balance</th>';
        html += '</tr>';
        html += '<tr>';
        html += '  <th class="sub-header" style="width:90px;">PAYE</th>';
        html += '  <th class="sub-header" style="width:90px;">SDL</th>';
        html += '  <th class="sub-header" style="width:90px;">UIF</th>';
        html += '  <th class="sub-header" style="width:90px;">OTHER</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        var cumulativePaye = 0, cumulativeSdl = 0, cumulativeUif = 0, cumulativeOther = 0, cumulativeBalance = 0;

        // For each period
        for (var p = 0; p < periods.length; p++) {
            var period = periods[p];
            var txns = period.transactions;
            var periodPaye = 0, periodSdl = 0, periodUif = 0, periodBalance = 0;

            for (var t = 0; t < txns.length; t++) {
                var txn = txns[t];
                var rowClass = '';
                if (txn.type === 'total_liability') rowClass = ' class="total-liability-row"';
                if (txn.type === 'financial_movement') rowClass = ' class="financial-movement-row"';

                html += '<tr' + rowClass + '>';
                html += '<td class="date-col">' + esc(txn.date || '') + '</td>';
                html += '<td style="font-size:10px;">' + esc(txn.reference || '') + '</td>';
                var descStyle = (txn.description && txn.description.toUpperCase().indexOf('PAYMENT') !== -1) ? ' style="color:#d32f2f;font-weight:600;"' : '';
                html += '<td class="desc-col"' + descStyle + '>' + esc(txn.description) + '</td>';
                html += '<td class="amount">' + fmtNeg(txn.value) + '</td>';
                html += '<td class="amount">' + fmtNeg(txn.paye) + '</td>';
                html += '<td class="amount">' + fmtNeg(txn.sdl) + '</td>';
                html += '<td class="amount">' + fmtNeg(txn.uif) + '</td>';
                html += '<td class="amount">' + fmtNeg(txn.other || 0) + '</td>';
                html += '<td class="amount">' + fmtNeg(txn.balance) + '</td>';
                html += '</tr>';

                // Track period totals from the balance row type
                if (txn.type === 'financial_movement') {
                    periodPaye = parseFloat(txn.paye) || 0;
                    periodSdl  = parseFloat(txn.sdl) || 0;
                    periodUif  = parseFloat(txn.uif) || 0;
                    periodBalance = parseFloat(txn.balance) || 0;
                }
            }

            // Period balance row
            html += '<tr class="period-balance">';
            html += '<td colspan="4">BALANCE: TAX PERIOD ' + esc(period.period_label) + '</td>';
            html += '<td class="amount">' + fmtNeg(period.balance_paye) + '</td>';
            html += '<td class="amount">' + fmtNeg(period.balance_sdl) + '</td>';
            html += '<td class="amount">' + fmtNeg(period.balance_uif) + '</td>';
            html += '<td class="amount">' + fmtNeg(period.balance_other || 0) + '</td>';
            html += '<td class="amount">' + fmtNeg(period.balance_total) + '</td>';
            html += '</tr>';

            cumulativePaye += parseFloat(period.balance_paye) || 0;
            cumulativeSdl += parseFloat(period.balance_sdl) || 0;
            cumulativeUif += parseFloat(period.balance_uif) || 0;
            cumulativeOther += parseFloat(period.balance_other) || 0;
            cumulativeBalance += parseFloat(period.balance_total) || 0;
        }

        // Cumulative Balance row
        html += '<tr class="cumulative-row">';
        html += '<td colspan="4"><strong>CUMULATIVE BALANCE</strong></td>';
        html += '<td class="amount">' + fmtNeg(cumulativePaye) + '</td>';
        html += '<td class="amount">' + fmtNeg(cumulativeSdl) + '</td>';
        html += '<td class="amount">' + fmtNeg(cumulativeUif) + '</td>';
        html += '<td class="amount">' + fmtNeg(cumulativeOther) + '</td>';
        html += '<td class="amount">' + fmtNeg(cumulativeBalance) + '</td>';
        html += '</tr>';

        html += '</tbody></table>';

        // ========== AGING ==========
        html += '<div class="sars-section-header">Ageing - Transactions are aged according to the original due date, including all related interest and penalties</div>';
        html += '<table class="sars-aging-table">';
        html += '<thead><tr><th>Current</th><th>30 Days</th><th>60 Days</th><th>90 Days</th><th>120 Days</th><th>Total</th></tr></thead>';
        html += '<tbody><tr>';
        html += '<td>' + fmt(aging.current) + '</td>';
        html += '<td>' + fmt(aging.days30) + '</td>';
        html += '<td>' + fmt(aging.days60) + '</td>';
        html += '<td>' + fmt(aging.days90) + '</td>';
        html += '<td>' + fmt(aging.days120) + '</td>';
        html += '<td><strong>' + fmt(aging.total) + '</strong></td>';
        html += '</tr></tbody></table>';

        // ========== COMPLIANCE ==========
        html += '<div class="sars-section-header">Compliance Information</div>';
        html += '<table class="sars-compliance-table">';
        html += '<tr><td>Active SDL Reference</td><td>Seta Code (SDL)</td><td>' + esc(client.sdl_number || 'N/A') + '</td></tr>';
        html += '<tr><td>Outstanding EMP501<br>Reconciliation/s</td><td>Outstanding EMP201</td><td>' + esc(data.compliance.outstanding_emp201 || 'None') + '</td></tr>';
        html += '<tr><td>Selected For Audit or<br>Verification</td><td colspan="2"></td></tr>';
        html += '</table>';

        html += '</div>'; // sars-body

        // ========== FOOTER ==========
        html += '<div class="sars-footer">';
        html += '  <div>Reference number: ' + esc(client.paye_number || '') + '</div>';
        html += '  <div>EMPSOA_RO</div>';
        html += '  <div>' + esc(today) + '</div>';
        html += '  <div>01/01</div>';
        html += '</div>';

        html += '</div>'; // sars-statement

        $container.html(html);
    }

    function esc(str) {
        if (!str) return '';
        return $('<div/>').text(str).html();
    }

})();
</script>
@endpush
