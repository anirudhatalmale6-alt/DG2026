@extends('layouts.default')

@section('title', 'Customer Aged Analysis')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Flatpickr overrides */
    .flatpickr-input { background: #fff !important; cursor: pointer; }
    .flatpickr-calendar { font-family: 'Poppins', sans-serif; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
    .flatpickr-day.selected { background: #17A2B8 !important; border-color: #17A2B8 !important; }
    .flatpickr-day:hover { background: #e8f4f8 !important; }

    /* ============================================
       CUSTOMER AGED ANALYSIS
       ============================================ */

    .aged-page {
        font-family: 'Poppins', Arial, sans-serif;
        background: #f5f7fa;
        padding: 40px 40px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header bar */
    .aged-header {
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
    .aged-header .aged-title {
        font-size: 22px; font-weight: 700; color: #0d3d56;
    }
    .aged-header .aged-subtitle {
        font-size: 13px; color: #666; margin-top: 2px;
    }
    .aged-header .aged-badge {
        font-size: 28px; font-weight: 800; color: #0d3d56; letter-spacing: 2px;
    }

    /* Filter card */
    .aged-filter-card .card {
        border: none; border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .aged-filter-card .card-body { padding: 24px 28px; }
    .aged-filter-card label {
        font-size: 13px; font-weight: 600; color: #0d3d56; margin-bottom: 6px;
    }
    .aged-filter-card input[type="text"] {
        min-height: 48px; border-radius: 8px; font-size: 14px;
        font-family: 'Poppins', sans-serif; border: 1px solid #ced4da;
        padding: 8px 12px; width: 100%;
    }
    .aged-filter-card input[type="text"]:focus {
        border-color: #17A2B8; box-shadow: 0 0 0 0.2rem rgba(23,162,184,0.25); outline: none;
    }

    /* Toggle switch */
    .toggle-container {
        display: flex; align-items: center; gap: 12px;
    }
    .toggle-label {
        font-size: 14px; font-weight: 600; color: #0d3d56; cursor: pointer;
    }
    .toggle-label.active { color: #17A2B8; }
    .toggle-switch {
        position: relative; display: inline-block; width: 52px; height: 28px;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
        background-color: #17A2B8; transition: 0.3s; border-radius: 28px;
    }
    .toggle-slider:before {
        position: absolute; content: ""; height: 22px; width: 22px;
        left: 3px; bottom: 3px; background: white;
        transition: 0.3s; border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .toggle-switch input:checked + .toggle-slider { background-color: #e91e63; }
    .toggle-switch input:checked + .toggle-slider:before { transform: translateX(24px); }

    /* Report document */
    .aged-document {
        background: #fff;
        border: 2px solid #17A2B8;
        border-radius: 8px;
        max-width: 1400px;
        margin: 30px auto;
        padding: 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    /* Document header */
    .aged-doc-header {
        background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%);
        color: #fff;
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .aged-doc-header .logo-area img {
        max-height: 60px; max-width: 200px; filter: brightness(0) invert(1);
    }
    .aged-doc-header .header-center { text-align: center; }
    .aged-doc-header .header-center .title {
        font-size: 18px; font-weight: 700; letter-spacing: 2px;
    }
    .aged-doc-header .header-center .sub {
        font-size: 12px; color: #b0dfe8; margin-top: 2px;
    }
    .aged-doc-header .header-right {
        text-align: right; font-size: 11px; color: #b0dfe8; line-height: 1.6;
    }

    /* Document body */
    .aged-doc-body { padding: 24px 30px; }

    /* Info row */
    .aged-info-row {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; padding: 12px 16px;
        background: #f0fafb; border-radius: 8px; border-left: 4px solid #17A2B8;
    }
    .aged-info-row .info-item {
        font-size: 13px; color: #0d3d56;
    }
    .aged-info-row .info-item strong { font-weight: 700; }

    /* Section header */
    .aged-section-header {
        background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%);
        color: #fff; padding: 8px 16px;
        font-size: 13px; font-weight: 700; letter-spacing: 0.5px;
        margin: 16px 0 0 0; border-radius: 2px;
    }

    /* Main aged table */
    .aged-table {
        width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 0;
    }
    .aged-table thead th {
        background: #0d3d56; color: #fff; font-weight: 700;
        padding: 10px 12px; border: 1px solid #0a2e40;
        text-align: left; font-size: 11px;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .aged-table thead th.text-right { text-align: right; }
    .aged-table tbody td {
        padding: 8px 12px; border: 1px solid #dde4ec;
        color: #333; vertical-align: middle;
    }
    .aged-table tbody td.amount {
        text-align: right; font-family: 'Poppins', monospace;
        font-weight: 500; white-space: nowrap;
    }
    .aged-table tbody tr:nth-child(even) { background: #fafbfc; }
    .aged-table tbody tr:hover { background: #f0f9ff; }
    .aged-table tbody tr.client-row { cursor: pointer; }
    .aged-table tbody tr.client-row:hover { background: #e8f4f8; }

    /* Detail rows (hidden by default in summary mode) */
    .aged-table tbody tr.detail-row { background: #f8fbff; }
    .aged-table tbody tr.detail-row td {
        font-size: 11px; color: #555; padding: 6px 12px 6px 40px;
        border-bottom: 1px solid #eef2f7;
    }
    .aged-table tbody tr.detail-row td:first-child { padding-left: 40px; }

    /* Detail header row */
    .aged-table tbody tr.detail-header td {
        background: #e8f0f8; font-weight: 700; font-size: 10px;
        text-transform: uppercase; letter-spacing: 0.5px; color: #0d3d56;
        padding: 6px 12px 6px 40px;
    }

    /* Grand totals */
    .aged-table tbody tr.grand-total-row { background: #0d3d56; }
    .aged-table tbody tr.grand-total-row td {
        color: #fff; font-weight: 700; font-size: 13px;
        padding: 12px; border-color: #0a2e40;
    }

    /* Aging color classes for amounts */
    .amt-current { color: #155724; }
    .amt-30 { color: #856404; }
    .amt-60 { color: #c0392b; }
    .amt-90 { color: #721c24; font-weight: 700; }

    /* Expand indicator */
    .expand-icon {
        display: inline-block; width: 18px; height: 18px;
        text-align: center; line-height: 18px;
        border-radius: 4px; font-size: 11px; font-weight: 700;
        background: #17A2B8; color: #fff; margin-right: 8px;
        transition: transform 0.2s;
    }
    .expand-icon.open { transform: rotate(90deg); }

    /* Client count badge */
    .client-count-badge {
        display: inline-block; padding: 4px 12px;
        background: #17A2B8; color: #fff; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }

    /* Document footer */
    .aged-doc-footer {
        background: #f5f7fa; padding: 12px 30px;
        display: flex; justify-content: space-between;
        font-size: 10px; color: #666;
        border-top: 2px solid #17A2B8;
    }

    /* Action buttons row */
    .action-btn {
        padding: 12px 20px; font-size: 15px; font-family: Poppins, sans-serif;
        font-weight: 600; border-radius: 8px; min-height: 48px;
        color: #fff; border: none; white-space: nowrap; cursor: pointer;
    }
    .btn-generate { background: linear-gradient(135deg,#17A2B8,#138496); }
    .btn-pdf { background: linear-gradient(135deg,#e91e63,#c2185b); }
    .btn-email { background: linear-gradient(135deg,#6f42c1,#5a32a3); }
    .btn-print { background: linear-gradient(135deg,#28a745,#1e7e34); }

    /* Email Modal */
    .aged-email-modal .modal-header {
        background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%);
        color: #fff; border-radius: 12px 12px 0 0; padding: 16px 24px;
    }
    .aged-email-modal .modal-header .modal-title {
        font-family: 'Poppins', sans-serif; font-size: 16px; font-weight: 700; color: #fff !important;
    }
    .aged-email-modal .modal-header .close,
    .aged-email-modal .modal-header .btn-close-modal {
        color: #fff !important; opacity: 1; text-shadow: none;
        background: none; border: none; font-size: 20px; cursor: pointer; padding: 0; line-height: 1;
    }
    .aged-email-modal .modal-content {
        border: none; border-radius: 12px; box-shadow: 0 8px 40px rgba(0,0,0,0.15);
    }
    .aged-email-modal .modal-body { padding: 24px; }
    .aged-email-modal .modal-body label {
        font-family: 'Poppins', sans-serif; font-size: 13px; font-weight: 600;
        color: #0d3d56; margin-bottom: 6px;
    }
    .aged-email-modal .modal-body .form-control {
        min-height: 44px; border-radius: 8px; font-size: 14px; font-family: 'Poppins', sans-serif;
    }
    .aged-email-modal .modal-footer {
        border-top: 1px solid #eee; padding: 16px 24px;
    }

    /* Print styles - LANDSCAPE */
    @media print {
        @page { size: landscape; margin: 10mm; }
        .aged-header, .aged-filter-card,
        .cims-header, .cims-footer, .sidebar, .navbar, .breadcrumb,
        #cims_master_menu, #cims_master_header,
        .cims-menu-wrapper, .cims-nav-container, .cims-main-menu,
        #loadingSpinner, .no-print,
        header, footer, nav,
        .page-header, .page-wrapper > .container-fluid > .row:first-child { display: none !important; }
        .aged-document {
            border: 2px solid #17A2B8 !important;
            box-shadow: none !important; margin: 0 !important; max-width: 100% !important;
        }
        .aged-page { padding: 0 !important; margin: 0 !important; max-width: 100% !important; }
        body { background: #fff !important; }
        .main-content, .page-wrapper, .container-fluid { padding: 0 !important; margin: 0 !important; }
        .detail-row { display: table-row !important; }
    }
</style>
@endpush

@section('content')
<div class="aged-page">

    <!-- Page header -->
    <div class="aged-header">
        <div>
            <div class="aged-title">Customer Aged Analysis</div>
            <div class="aged-subtitle">Outstanding balances aged by due date</div>
        </div>
        <div class="aged-badge">AGED ANALYSIS</div>
    </div>

    <!-- Filter bar -->
    <div class="aged-filter-card mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label>As of Date</label>
                        <input type="text" id="asOfDate" class="form-control" placeholder="dd/mm/yyyy" readonly>
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <div style="display:flex; gap:8px; flex-wrap:nowrap;">
                            <button class="btn action-btn btn-generate" id="btnGenerate"><i class="fa fa-sync-alt"></i> Generate</button>
                            <button class="btn action-btn btn-pdf no-print" id="btnPDF" style="display:none;"><i class="fa fa-file-pdf"></i> PDF</button>
                            <button class="btn action-btn btn-email no-print" id="btnEmail" style="display:none;"><i class="fa fa-envelope"></i> Email</button>
                            <button class="btn action-btn btn-print no-print" id="btnPrint" style="display:none;"><i class="fa fa-print"></i> Print</button>
                        </div>
                    </div>
                    <div class="col-md-3" style="display:flex; justify-content:flex-end; align-items:flex-end;">
                        <div class="toggle-container no-print">
                            <span class="toggle-label active" id="lblSummary">Summary</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="toggleDetailed">
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label" id="lblDetailed">Detailed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading spinner -->
    <div id="loadingSpinner" style="display:none; text-align:center; padding:60px;">
        <div class="spinner-border text-info" role="status" style="width:3rem; height:3rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <div style="margin-top:12px; color:#0d3d56; font-weight:600; font-family:Poppins,sans-serif;">Generating Aged Analysis...</div>
    </div>

    <!-- Report container -->
    <div id="reportContainer" style="display:none;"></div>

</div>

<!-- Email Modal -->
<div class="modal fade aged-email-modal" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailModalLabel"><i class="fa fa-envelope" style="margin-right:8px;"></i>Email Aged Analysis Report</h5>
                <button type="button" class="btn-close-modal" onclick="$('#emailModal').modal('hide');" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="emailTo">To (Email Address)</label>
                    <input type="email" class="form-control" id="emailTo" placeholder="recipient@example.com">
                </div>
                <div class="form-group">
                    <label for="emailSubject">Subject</label>
                    <input type="text" class="form-control" id="emailSubject" placeholder="Customer Aged Analysis Report">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onclick="$('#emailModal').modal('hide');" style="padding:10px 20px; font-family:Poppins,sans-serif; font-weight:600; border-radius:8px; background:#dc3545; color:#fff; border:none;">Cancel</button>
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

    var $container = $('#reportContainer');
    var $spinner   = $('#loadingSpinner');
    var lastData   = null;
    var isDetailed = false;

    // ========== DATE PICKER ==========
    var fpDate = flatpickr('#asOfDate', {
        dateFormat: 'd/m/Y',
        defaultDate: '{{ \Carbon\Carbon::parse($defaultDate)->format("d/m/Y") }}',
        allowInput: false
    });

    function getDateISO() {
        var d = fpDate.selectedDates[0];
        if (!d) return '';
        var y = d.getFullYear();
        var m = ('0' + (d.getMonth() + 1)).slice(-2);
        var day = ('0' + d.getDate()).slice(-2);
        return y + '-' + m + '-' + day;
    }

    // ========== TOGGLE ==========
    $('#toggleDetailed').on('change', function(){
        isDetailed = $(this).is(':checked');
        $('#lblSummary').toggleClass('active', !isDetailed);
        $('#lblDetailed').toggleClass('active', isDetailed);
        if (lastData) {
            renderReport(lastData);
        }
    });

    // ========== GENERATE ==========
    $('#btnGenerate').on('click', function(){
        var asOfDate = getDateISO();
        if (!asOfDate) {
            Swal.fire({icon:'warning', title:'Date Required', text:'Please select an As of Date.'});
            return;
        }

        $container.hide();
        $('#btnPDF, #btnEmail, #btnPrint').hide();
        $spinner.show();

        $.ajax({
            url: '{{ route("aged-analysis.generate") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                as_of_date: asOfDate
            },
            dataType: 'json',
            success: function(data) {
                $spinner.hide();
                lastData = data;
                renderReport(data);
                $container.show();
                $('#btnPDF, #btnEmail, #btnPrint').show();
            },
            error: function(xhr) {
                $spinner.hide();
                var errMsg = 'Failed to generate report.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire({icon:'error', title:'Error', text: errMsg});
            }
        });
    });

    // ========== PRINT (landscape) ==========
    $('#btnPrint').on('click', function(){ window.print(); });

    // ========== PDF DOWNLOAD ==========
    $('#btnPDF').on('click', function(){
        var asOfDate = getDateISO();
        if (!asOfDate) return;

        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Generating...');

        // Create a hidden form to POST and get PDF stream
        var $form = $('<form>', {
            method: 'POST',
            action: '{{ route("aged-analysis.pdf") }}',
            target: '_blank'
        }).append(
            $('<input>', { type: 'hidden', name: '_token', value: $('meta[name="csrf-token"]').attr('content') }),
            $('<input>', { type: 'hidden', name: 'as_of_date', value: asOfDate }),
            $('<input>', { type: 'hidden', name: 'mode', value: isDetailed ? 'detailed' : 'summary' })
        );

        $('body').append($form);
        $form.submit();
        $form.remove();

        setTimeout(function(){
            $btn.prop('disabled', false).html('<i class="fa fa-file-pdf"></i> PDF');
        }, 2000);
    });

    // ========== EMAIL BUTTON ==========
    $('#btnEmail').on('click', function(){
        var asOfDate = getDateISO();
        if (!asOfDate) return;

        var displayDate = formatDisplayDate(asOfDate);
        $('#emailTo').val('');
        $('#emailSubject').val('Customer Aged Analysis Report - As of ' + displayDate);
        $('#emailModal').modal('show');
    });

    // ========== SEND EMAIL ==========
    $('#btnSendEmail').on('click', function(){
        var emailTo = $('#emailTo').val().trim();
        if (!emailTo) {
            Swal.fire({icon:'warning', title:'Email Required', text:'Please enter a valid email address.'});
            return;
        }

        var asOfDate = getDateISO();
        var subject = $('#emailSubject').val().trim() || 'Customer Aged Analysis Report';
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            url: '{{ route("aged-analysis.email") }}',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                email_to: emailTo,
                subject: subject,
                as_of_date: asOfDate,
                mode: isDetailed ? 'detailed' : 'summary'
            },
            dataType: 'json',
            success: function(resp) {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane" style="margin-right:6px;"></i>Send Email');
                $('#emailModal').modal('hide');
                Swal.fire({icon:'success', title:'Email Sent', text: resp.message || 'Report emailed successfully.'});
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-paper-plane" style="margin-right:6px;"></i>Send Email');
                var errMsg = xhr.responseJSON ? (xhr.responseJSON.error || xhr.responseJSON.message) : 'Failed to send email.';
                Swal.fire({icon:'error', title:'Email Error', text: errMsg});
            }
        });
    });

    // ========== RENDER REPORT ==========
    function renderReport(data) {
        var clients = data.clients || [];
        var totals  = data.grand_totals || {};
        var settings = data.settings || {};
        var currency = settings.settings_system_currency_symbol || 'R ';
        var prefix   = settings.settings_invoices_prefix || 'INV-';

        var html = '';
        html += '<div class="aged-document">';

        // === DOCUMENT HEADER ===
        html += '<div class="aged-doc-header">';
        html += '  <div class="logo-area"><img src="/storage/logos/app/cims_inv_logo.png" alt="Logo" onerror="this.style.display=\'none\'"></div>';
        html += '  <div class="header-center">';
        html += '    <div class="title">CUSTOMER AGED ANALYSIS</div>';
        html += '    <div class="sub">Outstanding Balances by Aging Bucket</div>';
        html += '  </div>';
        html += '  <div class="header-right">';
        html += '    <div style="font-weight:700;font-size:13px;color:#fff;">' + esc(settings.settings_company_name) + '</div>';
        html += '  </div>';
        html += '</div>';

        // === DOCUMENT BODY ===
        html += '<div class="aged-doc-body">';

        // Info row
        html += '<div class="aged-info-row">';
        html += '  <div class="info-item"><strong>As of Date:</strong> ' + formatDisplayDate(data.as_of_date) + '</div>';
        html += '  <div class="info-item"><strong>Clients with Balances:</strong> <span class="client-count-badge">' + clients.length + '</span></div>';
        html += '  <div class="info-item"><strong>Total Outstanding:</strong> <span style="font-weight:700;font-size:15px;color:#0d3d56;">' + fmtCur(totals.total, currency) + '</span></div>';
        html += '</div>';

        // === AGING TABLE ===
        html += '<div class="aged-section-header">Aged Balances</div>';
        html += '<table class="aged-table">';
        html += '<thead><tr>';
        if (isDetailed) {
            html += '  <th style="width:30px;"></th>';
        }
        html += '  <th style="width:120px;">Client Code</th>';
        html += '  <th>Client Name</th>';
        html += '  <th class="text-right" style="width:130px;">Current</th>';
        html += '  <th class="text-right" style="width:130px;">30 Days</th>';
        html += '  <th class="text-right" style="width:130px;">60 Days</th>';
        html += '  <th class="text-right" style="width:130px;">90+ Days</th>';
        html += '  <th class="text-right" style="width:140px;">Total</th>';
        html += '</tr></thead>';
        html += '<tbody>';

        if (clients.length === 0) {
            var colSpan = isDetailed ? 8 : 7;
            html += '<tr><td colspan="' + colSpan + '" style="text-align:center;padding:40px;color:#999;font-size:14px;">No clients with outstanding balances found.</td></tr>';
        } else {
            for (var i = 0; i < clients.length; i++) {
                var c = clients[i];
                var rowId = 'client_' + c.client_id;

                // Client summary row
                html += '<tr class="client-row" data-client-id="' + c.client_id + '" data-row-id="' + rowId + '">';
                if (isDetailed) {
                    html += '  <td><span class="expand-icon" id="icon_' + rowId + '">&#9654;</span></td>';
                }
                html += '  <td style="font-weight:600;color:#17A2B8;">' + esc(c.client_code || '-') + '</td>';
                html += '  <td style="font-weight:600;">' + esc(c.client_name) + '</td>';
                html += '  <td class="amount ' + (c.current > 0 ? 'amt-current' : '') + '">' + (c.current > 0 ? fmtCur(c.current, currency) : '-') + '</td>';
                html += '  <td class="amount ' + (c['30_days'] > 0 ? 'amt-30' : '') + '">' + (c['30_days'] > 0 ? fmtCur(c['30_days'], currency) : '-') + '</td>';
                html += '  <td class="amount ' + (c['60_days'] > 0 ? 'amt-60' : '') + '">' + (c['60_days'] > 0 ? fmtCur(c['60_days'], currency) : '-') + '</td>';
                html += '  <td class="amount ' + (c['90_plus'] > 0 ? 'amt-90' : '') + '">' + (c['90_plus'] > 0 ? fmtCur(c['90_plus'], currency) : '-') + '</td>';
                html += '  <td class="amount" style="font-weight:700;">' + fmtCur(c.total, currency) + '</td>';
                html += '</tr>';

                // Detail rows (hidden by default in detailed mode)
                if (isDetailed && c.invoices && c.invoices.length > 0) {
                    var detailColSpan = 8;

                    // Detail header
                    html += '<tr class="detail-header detail-rows-' + rowId + '" style="display:none;">';
                    html += '  <td></td>';
                    html += '  <td>Invoice #</td>';
                    html += '  <td>Invoice Date</td>';
                    html += '  <td>Due Date</td>';
                    html += '  <td>Amount</td>';
                    html += '  <td>Paid</td>';
                    html += '  <td>Outstanding</td>';
                    html += '  <td>Days O/D</td>';
                    html += '</tr>';

                    for (var j = 0; j < c.invoices.length; j++) {
                        var inv = c.invoices[j];
                        var invRef = inv.invoice_reference;
                        if (typeof invRef === 'number' || (typeof invRef === 'string' && /^\d+$/.test(invRef))) {
                            invRef = prefix + padLeft(invRef, 6);
                        }

                        var bucketClass = '';
                        if (inv.bucket === 'current') bucketClass = 'amt-current';
                        else if (inv.bucket === '30_days') bucketClass = 'amt-30';
                        else if (inv.bucket === '60_days') bucketClass = 'amt-60';
                        else if (inv.bucket === '90_plus') bucketClass = 'amt-90';

                        html += '<tr class="detail-row detail-rows-' + rowId + '" style="display:none;">';
                        html += '  <td></td>';
                        html += '  <td style="color:#17A2B8;font-weight:500;">' + esc(invRef) + '</td>';
                        html += '  <td>' + formatDisplayDate(inv.invoice_date) + '</td>';
                        html += '  <td>' + formatDisplayDate(inv.due_date) + '</td>';
                        html += '  <td class="amount">' + fmtCur(inv.amount, currency) + '</td>';
                        html += '  <td class="amount" style="color:#28a745;">' + fmtCur(inv.payments, currency) + '</td>';
                        html += '  <td class="amount ' + bucketClass + '">' + fmtCur(inv.outstanding, currency) + '</td>';
                        html += '  <td style="text-align:right;">' + inv.days_overdue + '</td>';
                        html += '</tr>';
                    }
                }
            }
        }

        // Grand totals row
        html += '<tr class="grand-total-row">';
        if (isDetailed) {
            html += '  <td></td>';
        }
        html += '  <td colspan="2" style="text-align:right;">Grand Total (' + clients.length + ' clients):</td>';
        html += '  <td class="amount">' + fmtCur(totals.current, currency) + '</td>';
        html += '  <td class="amount">' + fmtCur(totals['30_days'], currency) + '</td>';
        html += '  <td class="amount">' + fmtCur(totals['60_days'], currency) + '</td>';
        html += '  <td class="amount">' + fmtCur(totals['90_plus'], currency) + '</td>';
        html += '  <td class="amount">' + fmtCur(totals.total, currency) + '</td>';
        html += '</tr>';

        html += '</tbody></table>';
        html += '</div>'; // aged-doc-body

        // === FOOTER ===
        html += '<div class="aged-doc-footer">';
        html += '  <div>' + esc(settings.settings_company_name) + '</div>';
        html += '  <div>Customer Aged Analysis</div>';
        html += '  <div>Generated: ' + formatDisplayDate(new Date().toISOString().split('T')[0]) + '</div>';
        html += '</div>';

        html += '</div>'; // aged-document

        $container.html(html);

        // Bind click for detail expand/collapse in detailed mode
        if (isDetailed) {
            $('.client-row').on('click', function(){
                var rowId = $(this).data('row-id');
                var $details = $('.detail-rows-' + rowId);
                var $icon = $('#icon_' + rowId);

                if ($details.first().is(':visible')) {
                    $details.hide();
                    $icon.removeClass('open');
                } else {
                    $details.show();
                    $icon.addClass('open');
                }
            });
        }
    }

    // ========== HELPERS ==========
    function fmtCur(val, symbol) {
        var n = parseFloat(val) || 0;
        var formatted = Math.abs(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        if (n < 0) return '<span style="color:#dc3545;">-' + symbol + formatted + '</span>';
        return symbol + formatted;
    }

    function formatDisplayDate(dateStr) {
        if (!dateStr) return '';
        var parts = dateStr.split('-');
        if (parts.length === 3) return parts[2] + '/' + parts[1] + '/' + parts[0];
        return dateStr;
    }

    function esc(str) {
        if (!str) return '';
        return $('<div/>').text(str).html();
    }

    function padLeft(val, len) {
        var s = String(val);
        while (s.length < len) s = '0' + s;
        return s;
    }

    // Auto-generate on page load
    $('#btnGenerate').trigger('click');

})();
</script>
@endpush
