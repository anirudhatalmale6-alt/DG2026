@extends('layouts.default')

@section('title', 'EMP201 Pivot Table')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* ============================================
       PIVOT PAGE - PREMIUM STYLING
       SmartDash Design System (#17A2B8 / #0d3d56)
       ============================================ */

    /* --- SARS HEADER BANNER --- */
    .pivot-header {
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
    .pivot-header .sars-title {
        font-family: 'Poppins', sans-serif;
        font-size: 22px;
        font-weight: 700;
        color: #0d3d56;
        letter-spacing: 0.3px;
    }
    .pivot-header .sars-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 400;
        color: #666;
        margin-top: 2px;
    }
    .pivot-header .emp501-badge {
        font-family: 'Poppins', sans-serif;
        font-size: 34px;
        font-weight: 800;
        color: #0d3d56;
        letter-spacing: 3px;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.08);
    }

    /* --- SELECTION CARD --- */
    .pivot-selection-card .card-body {
        padding: 24px 28px;
        background: #fff;
    }
    .pivot-selection-card label {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: #0d3d56;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    /* --- EXPORT BUTTONS --- */
    .export-btns .btn { margin-right: 10px; font-family: 'Poppins', sans-serif; font-weight: 600; font-size: 14px; }
    .export-btns .btn-success { background: linear-gradient(135deg, #28a745, #218838); border: none; box-shadow: 0 3px 10px rgba(40,167,69,0.25); }
    .export-btns .btn-success:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(40,167,69,0.35); }
    .export-btns .btn-danger { background: linear-gradient(135deg, #dc3545, #c82333); border: none; box-shadow: 0 3px 10px rgba(220,53,69,0.25); }
    .export-btns .btn-danger:hover { transform: translateY(-1px); box-shadow: 0 5px 15px rgba(220,53,69,0.35); }

    /* --- LOADING & EMPTY STATES --- */
    #pivotLoading { display: none; text-align: center; padding: 60px 20px; color: #666; }
    #pivotLoading i { color: #17A2B8; }
    #pivotLoading p { font-family: 'Poppins', sans-serif; font-size: 15px; font-weight: 500; }
    #pivotEmpty { display: none; text-align: center; padding: 60px 20px; color: #999; }
    #pivotEmpty i { color: #ccc; }
    #pivotEmpty p { font-family: 'Poppins', sans-serif; font-size: 15px; }
    #pivotContainer { display: none; }

    /* --- PIVOT TABLE CARD --- */
    .pivot-table-card .card-header {
        background: linear-gradient(135deg, #0d3d56 0%, #1496bb 100%);
        color: #fff;
        padding: 14px 24px;
        border-radius: 12px 12px 0 0;
    }
    .pivot-table-card .card-header h4 {
        font-family: 'Poppins', sans-serif;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
        color: #fff;
    }
    .pivot-table-card .card-header h4 i { margin-right: 8px; }

    /* --- PIVOT TABLE --- */
    .pivot-table-wrapper { overflow-x: auto; background: #fff; }
    .pivot-table {
        width: 100%;
        border-collapse: collapse;
        font-family: 'Poppins', sans-serif;
        white-space: nowrap;
    }

    /* Table header */
    .pivot-table th {
        background: #0d3d56;
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        padding: 10px 12px;
        border: 1px solid #0a3048;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .pivot-table th.row-header {
        text-align: left;
        min-width: 180px;
        position: sticky;
        left: 0;
        z-index: 2;
        background: #0d3d56;
        font-size: 12px;
        letter-spacing: 0.5px;
    }

    /* Table cells */
    .pivot-table td {
        padding: 8px 14px;
        border: 1px solid #e0e5e9;
        text-align: right;
        font-size: 12px;
        font-weight: 400;
        color: #333;
        background: #fff;
        font-variant-numeric: tabular-nums;
    }

    /* Row label (sticky left) */
    .pivot-table td.row-label {
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        color: #0d3d56;
        background: #f4f8fa;
        position: sticky;
        left: 0;
        z-index: 1;
        min-width: 180px;
        border-right: 2px solid #17A2B8;
    }

    /* Alternating row backgrounds */
    .pivot-table tbody tr:nth-child(even) td { background: #fafcfd; }
    .pivot-table tbody tr:nth-child(even) td.row-label { background: #eef3f6; }

    /* Hover */
    .pivot-table tbody tr:hover td { background: #e8f7fa; }
    .pivot-table tbody tr:hover td.row-label { background: #d6eff4; }
    .pivot-table td.has-value { cursor: pointer; }
    .pivot-table td.has-value:hover { background: #c9eef5 !important; text-decoration: underline; }

    /* --- SEPARATOR COLUMNS --- */
    .pivot-table .separator-col { width: 3px; padding: 0; background: #0d3d56 !important; border: none; min-width: 3px; max-width: 3px; }
    .pivot-table th.separator-col { background: #0d3d56 !important; }

    /* --- EMP501 PERIOD TOTAL COLUMNS --- */
    .pivot-table .p1-total-col {
        background: #e8f0fe !important;
        font-weight: 700;
        color: #0d3d56;
        border-left: 2px solid #1565c0;
        border-right: 2px solid #1565c0;
    }
    .pivot-table .p2-total-col {
        background: #e8f0fe !important;
        font-weight: 700;
        color: #0d3d56;
        border-left: 2px solid #1565c0;
        border-right: 2px solid #1565c0;
    }
    .pivot-table th.p1-total-col {
        background: #1565c0 !important;
        color: #fff;
        font-size: 10px;
        letter-spacing: 0.5px;
    }
    .pivot-table th.p2-total-col {
        background: #1565c0 !important;
        color: #fff;
        font-size: 10px;
        letter-spacing: 0.5px;
    }

    /* --- ANNUAL TOTAL COLUMN --- */
    .pivot-table .annual-total-col {
        background: #e6f4ea !important;
        font-weight: 800;
        color: #1b5e20;
        border-left: 2px solid #2e7d32;
        border-right: 2px solid #2e7d32;
    }
    .pivot-table th.annual-total-col {
        background: #2e7d32 !important;
        color: #fff;
        font-size: 10px;
        letter-spacing: 0.5px;
    }

    /* --- SUBTOTAL ROWS (Payroll Liability, ETI groupings, Payable groupings) --- */
    .pivot-table tr.subtotal-row td {
        border-top: 2px solid #0d3d56;
        border-bottom: 2px solid #0d3d56;
        background: #edf5f7 !important;
        font-weight: 700;
        color: #0d3d56;
    }
    .pivot-table tr.subtotal-row td.row-label {
        background: #dceef2 !important;
        color: #0a3048;
        font-weight: 700;
        font-size: 12px;
    }
    .pivot-table tr.subtotal-row .p1-total-col { background: #d4e5f7 !important; }
    .pivot-table tr.subtotal-row .p2-total-col { background: #d4e5f7 !important; }
    .pivot-table tr.subtotal-row .annual-total-col { background: #c8e6c9 !important; }

    /* --- TOTAL PAYABLE ROW (Grand Total) --- */
    .pivot-table tr.total-row td {
        background: #fff8e1 !important;
        font-weight: 800;
        font-size: 13px;
        color: #0d3d56;
        border-top: 3px solid #0d3d56;
        border-bottom: 3px solid #0d3d56;
    }
    .pivot-table tr.total-row td.row-label {
        background: #fff0c2 !important;
        font-size: 13px;
        font-weight: 800;
        color: #0a3048;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .pivot-table tr.total-row .p1-total-col { background: #bbdefb !important; font-size: 13px; }
    .pivot-table tr.total-row .p2-total-col { background: #bbdefb !important; font-size: 13px; }
    .pivot-table tr.total-row .annual-total-col { background: #a5d6a7 !important; font-size: 14px; color: #1b5e20; }

    /* --- SECTION DIVIDER ROWS (visual grouping between sections) --- */
    .pivot-table tr.section-start td {
        border-top: 2px solid #17A2B8;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- BREADCRUMB --}}
    <div class="row page-titles">
        <div class="d-flex align-items-center justify-content-between">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="fs-2" style="color:#000" href="javascript:void(0)">Compliance</a></li>
                <li class="breadcrumb-item"><a class="fs-2" style="color:#000" href="{{ route('cimsemp201.index') }}">EMP201</a></li>
                <li class="breadcrumb-item active"><a class="fs-2" style="color:#17A2B8" href="javascript:void(0)">Pivot Table</a></li>
            </ol>
            <a href="{{ route('cimsemp201.index') }}" class="btn sd_btn">
                <i class="fa fa-list"></i> All EMP201
            </a>
        </div>
    </div>

    {{-- HEADER --}}
    <div class="pivot-header">
        <div style="display: flex; align-items: center;">
            <img src="{{ asset('public/images/sars_logo.png') }}" alt="SARS Logo" style="height: 55px; margin-right: 18px;">
            <div>
                <div class="sars-title">EMP201 / EMP501 Pivot Summary</div>
                <div class="sars-subtitle">South African Revenue Service &mdash; Payroll Tax Year (March to February)</div>
            </div>
        </div>
        <div class="emp501-badge">EMP 501</div>
    </div>

    {{-- CLIENT & YEAR SELECTION --}}
    <div class="card smartdash-form-card pivot-selection-card">
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Select Client</label>
                    <select id="pivot_client" class="sd_drop_class" data-live-search="true" data-size="10" title="-- Select Client --" style="width:100%;">
                        <option value="">-- Select Client --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->client_id }}">{{ $client->client_code }} - {{ $client->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tax Year</label>
                    <select id="pivot_year" class="sd_drop_class" data-size="10" title="-- Select Year --" style="width:100%;">
                        <option value="">-- Select Year --</option>
                        @foreach($taxYears as $year)
                            <option value="{{ $year }}">{{ $year }} (Mar {{ $year - 1 }} - Feb {{ $year }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="export-btns d-flex align-items-end flex-wrap" style="padding-bottom: 4px; gap: 8px;">
                        <button class="btn" id="btnLoadPivot" style="padding: 12px 20px; font-size: 15px; font-family: Poppins, sans-serif; font-weight: 600; background: linear-gradient(135deg, #17A2B8, #138496); color: #fff; border: none; border-radius: 8px; min-height: 48px;"><i class="fa fa-sync-alt"></i> Load</button>
                        <button class="btn btn-success" id="btnExportXlsx" style="display:none; padding: 12px 20px; font-size: 15px; font-family: Poppins, sans-serif; font-weight: 600; border-radius: 8px; min-height: 48px;"><i class="fa fa-file-excel"></i> Excel</button>
                        <button class="btn btn-primary" id="btnExportCsv" style="display:none; padding: 12px 20px; font-size: 15px; font-family: Poppins, sans-serif; font-weight: 600; border-radius: 8px; min-height: 48px;"><i class="fa fa-file-csv"></i> CSV</button>
                        <button class="btn btn-danger" id="btnExportPDF" style="display:none; padding: 12px 20px; font-size: 15px; font-family: Poppins, sans-serif; font-weight: 600; border-radius: 8px; min-height: 48px;"><i class="fa fa-file-pdf"></i> PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LOADING --}}
    <div id="pivotLoading">
        <i class="fa fa-spinner fa-spin fa-2x"></i>
        <p class="mt-3">Loading pivot data...</p>
    </div>

    {{-- EMPTY STATE --}}
    <div id="pivotEmpty">
        <i class="fa fa-table fa-3x text-muted"></i>
        <p class="mt-3">No EMP201 declarations found for the selected client and tax year.</p>
    </div>

    {{-- PIVOT TABLE --}}
    <div id="pivotContainer">
        <div class="card smartdash-form-card pivot-table-card">
            <div class="card-header">
                <h4><i class="fa fa-table"></i> EMP201 PIVOT &mdash; <span id="pivotClientName"></span> &mdash; Tax Year <span id="pivotYearLabel"></span></h4>
            </div>
            <div class="card-body p-0">
                <div class="pivot-table-wrapper">
                    <table class="pivot-table" id="pivotTable">
                        <thead>
                            <tr id="pivotHeaderRow"></tr>
                        </thead>
                        <tbody id="pivotBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    $('select.sd_drop_class').selectpicker({ liveSearch: true, size: 10 });

    var declarationIds = [];

    // Subtotal row keys - these get darker top/bottom borders
    var subtotalKeys = ['payroll_liability'];
    // Section start keys - these get a teal top border to separate sections
    var sectionStartKeys = ['eti_brought_forward', 'paye_payable', 'penalty_interest'];

    function formatCurrency(val) {
        var num = parseFloat(val) || 0;
        var parts = num.toFixed(2).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        return parts.join('.');
    }

    function loadPivot() {
        var clientId = $('#pivot_client').val();
        var taxYear = $('#pivot_year').val();

        if (!clientId || !taxYear) {
            Swal.fire({ icon: 'warning', title: 'Selection Required', text: 'Please select both a Client and a Tax Year.', confirmButtonColor: '#17A2B8' });
            return;
        }

        $('#pivotContainer').hide();
        $('#pivotEmpty').hide();
        $('#pivotLoading').show();
        $('#btnExportXlsx, #btnExportCsv, #btnExportPDF').hide();

        $.ajax({
            url: '{{ route("cimsemp201.api.pivot-data") }}',
            data: { client_id: clientId, tax_year: taxYear },
            dataType: 'json',
            success: function(data) {
                $('#pivotLoading').hide();

                if (!data.lineItems || data.lineItems.length === 0) {
                    $('#pivotEmpty').show();
                    return;
                }

                declarationIds = data.declarationIds || [];
                var months = data.months;

                // Update labels
                var clientText = $('#pivot_client option:selected').text();
                $('#pivotClientName').text(clientText);
                $('#pivotYearLabel').text(taxYear + ' (Mar ' + (taxYear - 1) + ' - Feb ' + taxYear + ')');

                // Build header - show actual calendar year for each month
                var hdr = '<th class="row-header">Line Item</th>';
                for (var i = 0; i < 6; i++) {
                    hdr += '<th>' + months[i].label.substring(0, 3) + '<br>' + months[i].year + '</th>';
                }
                hdr += '<th class="separator-col"></th>';
                hdr += '<th class="p1-total-col">EMP501<br>Period 1</th>';
                hdr += '<th class="separator-col"></th>';
                for (var i = 6; i < 12; i++) {
                    hdr += '<th>' + months[i].label.substring(0, 3) + '<br>' + months[i].year + '</th>';
                }
                hdr += '<th class="separator-col"></th>';
                hdr += '<th class="p2-total-col">EMP501<br>Period 2</th>';
                hdr += '<th class="separator-col"></th>';
                hdr += '<th class="annual-total-col">Annual<br>EMP501</th>';
                $('#pivotHeaderRow').html(hdr);

                // Build body
                var body = '';
                $.each(data.lineItems, function(idx, row) {
                    var isTotalRow = (row.key === 'tax_payable');
                    var isSubtotal = subtotalKeys.indexOf(row.key) !== -1;
                    var isSectionStart = sectionStartKeys.indexOf(row.key) !== -1;

                    var rowClasses = [];
                    if (isTotalRow) rowClasses.push('total-row');
                    if (isSubtotal) rowClasses.push('subtotal-row');
                    if (isSectionStart) rowClasses.push('section-start');

                    var rowAttr = rowClasses.length ? ' class="' + rowClasses.join(' ') + '"' : '';
                    body += '<tr' + rowAttr + '>';
                    body += '<td class="row-label">' + row.label + '</td>';

                    // First 6 months (P1)
                    for (var i = 0; i < 6; i++) {
                        var val = row.values[i];
                        var cls = (val !== 0 && declarationIds[i]) ? ' has-value' : '';
                        var did = declarationIds[i] ? ' data-decl-id="' + declarationIds[i] + '"' : '';
                        body += '<td class="' + cls + '"' + did + '>' + formatCurrency(val) + '</td>';
                    }

                    body += '<td class="separator-col"></td>';
                    body += '<td class="p1-total-col">' + formatCurrency(row.p1_total) + '</td>';
                    body += '<td class="separator-col"></td>';

                    // Last 6 months (P2)
                    for (var i = 6; i < 12; i++) {
                        var val = row.values[i];
                        var cls = (val !== 0 && declarationIds[i]) ? ' has-value' : '';
                        var did = declarationIds[i] ? ' data-decl-id="' + declarationIds[i] + '"' : '';
                        body += '<td class="' + cls + '"' + did + '>' + formatCurrency(val) + '</td>';
                    }

                    body += '<td class="separator-col"></td>';
                    body += '<td class="p2-total-col">' + formatCurrency(row.p2_total) + '</td>';
                    body += '<td class="separator-col"></td>';
                    body += '<td class="annual-total-col">' + formatCurrency(row.annual_total) + '</td>';
                    body += '</tr>';
                });
                $('#pivotBody').html(body);

                // Show table and export buttons
                $('#pivotContainer').show();
                $('#btnExportXlsx, #btnExportCsv, #btnExportPDF').show();

                // Click on cell to edit declaration
                $('.has-value').on('click', function() {
                    var declId = $(this).data('decl-id');
                    if (declId) {
                        window.open('{{ url("/cims/emp201") }}/' + declId + '/edit', '_blank');
                    }
                });
            },
            error: function() {
                $('#pivotLoading').hide();
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load pivot data.', confirmButtonColor: '#dc3545' });
            }
        });
    }

    $('#btnLoadPivot').on('click', loadPivot);

    // Also load when both selections change
    $('#pivot_client, #pivot_year').on('changed.bs.select', function() {
        if ($('#pivot_client').val() && $('#pivot_year').val()) {
            loadPivot();
        }
    });

    // ============================================
    // EXPORT TO EXCEL (.xlsx with full styling)
    // ============================================
    $('#btnExportXlsx').on('click', function() {
        var clientId = $('#pivot_client').val();
        var taxYear = $('#pivot_year').val();
        if (!clientId || !taxYear) return;
        window.location.href = '{{ route("cimsemp201.pivot.export-excel") }}?client_id=' + clientId + '&tax_year=' + taxYear;
    });

    // ============================================
    // EXPORT TO CSV
    // ============================================
    $('#btnExportCsv').on('click', function() {
        var table = document.getElementById('pivotTable');
        var csv = [];

        for (var i = 0; i < table.rows.length; i++) {
            var row = [];
            for (var j = 0; j < table.rows[i].cells.length; j++) {
                var cell = table.rows[i].cells[j];
                if (cell.classList.contains('separator-col')) continue;
                var text = cell.innerText.replace(/"/g, '""').replace(/\n/g, ' ');
                row.push('"' + text + '"');
            }
            csv.push(row.join(','));
        }

        var csvContent = csv.join('\n');
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var clientText = $('#pivot_client option:selected').text().replace(/[^a-zA-Z0-9]/g, '_');
        var year = $('#pivot_year').val();
        link.href = URL.createObjectURL(blob);
        link.download = 'EMP201_Pivot_' + clientText + '_' + year + '.csv';
        link.click();
    });

    // ============================================
    // EXPORT TO PDF (print-friendly)
    // ============================================
    $('#btnExportPDF').on('click', function() {
        var clientText = $('#pivot_client option:selected').text();
        var year = $('#pivot_year').val();
        var tableHtml = document.getElementById('pivotTable').outerHTML;

        var printWin = window.open('', '_blank');
        printWin.document.write('<html><head><title>EMP201 Pivot - ' + clientText + ' - ' + year + '</title>');
        printWin.document.write('<style>');
        printWin.document.write('body { font-family: Poppins, Arial, sans-serif; margin: 20px; color: #333; }');
        printWin.document.write('h2 { color: #0d3d56; margin-bottom: 4px; font-size: 18px; }');
        printWin.document.write('h4 { color: #666; margin-top: 0; font-size: 13px; font-weight: 400; }');
        printWin.document.write('table { width: 100%; border-collapse: collapse; font-size: 9px; font-family: Poppins, Arial, sans-serif; }');
        printWin.document.write('th { background: #0d3d56; color: #fff; text-align: center; padding: 6px 8px; border: 1px solid #0a3048; font-size: 9px; font-weight: 600; text-transform: uppercase; }');
        printWin.document.write('td { padding: 5px 10px; border: 1px solid #ddd; text-align: right; }');
        printWin.document.write('td:first-child, th:first-child { text-align: left; font-weight: 600; }');
        printWin.document.write('.p1-total-col, .p2-total-col { background: #e8f0fe !important; font-weight: 700; border-left: 2px solid #1565c0; border-right: 2px solid #1565c0; }');
        printWin.document.write('.annual-total-col { background: #e6f4ea !important; font-weight: 800; border-left: 2px solid #2e7d32; border-right: 2px solid #2e7d32; }');
        printWin.document.write('.subtotal-row td { border-top: 2px solid #0d3d56; border-bottom: 2px solid #0d3d56; background: #edf5f7 !important; font-weight: 700; }');
        printWin.document.write('.total-row td { font-weight: 800; border-top: 3px solid #0d3d56; border-bottom: 3px solid #0d3d56; background: #fff8e1 !important; font-size: 10px; }');
        printWin.document.write('.section-start td { border-top: 2px solid #17A2B8; }');
        printWin.document.write('.separator-col { width: 2px; padding: 0; background: #0d3d56 !important; }');
        printWin.document.write('th.p1-total-col, th.p2-total-col { background: #1565c0 !important; color: #fff; }');
        printWin.document.write('th.annual-total-col { background: #2e7d32 !important; color: #fff; }');
        printWin.document.write('@media print { body { margin: 10px; } @page { size: landscape; margin: 8mm; } }');
        printWin.document.write('</style></head><body>');
        printWin.document.write('<h2>EMP201 / EMP501 Pivot Summary</h2>');
        printWin.document.write('<h4>' + clientText + ' &mdash; Tax Year ' + year + ' (Mar ' + (year - 1) + ' - Feb ' + year + ')</h4>');
        printWin.document.write(tableHtml);
        printWin.document.write('<p style="font-size:9px; color:#999; margin-top:15px;">Generated: ' + new Date().toLocaleString() + '</p>');
        printWin.document.write('</body></html>');
        printWin.document.close();
        setTimeout(function() { printWin.print(); }, 500);
    });

});
</script>
@endpush
