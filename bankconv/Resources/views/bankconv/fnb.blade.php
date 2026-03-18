@extends('layouts.default')

@section('title', 'FNB to QuickBooks')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .bankconv-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

    /* Upload zone */
    .upload-drop-zone {
        border: 2px dashed #17A2B8; border-radius: 12px; padding: 40px 20px;
        text-align: center; background: #fafafa; transition: all 0.3s;
        cursor: pointer; min-height: 180px;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
    }
    .upload-drop-zone:hover, .upload-drop-zone.dragover {
        background: #e8f7fa; border-color: #0d3d56; transform: scale(1.01);
    }
    .upload-drop-zone .upload-icon { font-size: 48px; color: #17A2B8; margin-bottom: 12px; }
    .upload-drop-zone .upload-text { font-size: 16px; font-weight: 600; color: #333; }
    .upload-drop-zone .upload-subtext { font-size: 13px; color: #888; margin-top: 6px; }
    .upload-drop-zone .file-selected { color: #0d3d56; font-weight: 700; font-size: 15px; margin-top: 10px; }

    /* Summary bar */
    .summary-bar {
        display: flex; flex-wrap: wrap; gap: 12px; padding: 16px 20px;
        background: linear-gradient(135deg, #0d3d56 0%, #1496bb 100%);
        border-radius: 10px; margin-bottom: 20px;
    }
    .summary-item {
        flex: 1; min-width: 140px; text-align: center; padding: 10px;
        background: rgba(255,255,255,0.12); border-radius: 8px;
    }
    .summary-item .label { font-size: 11px; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 0.5px; }
    .summary-item .value { font-size: 18px; font-weight: 700; color: #fff; margin-top: 4px; }
    .summary-item .value.credit { color: #6ee7b7; }
    .summary-item .value.debit { color: #fca5a5; }
    .summary-item .value.match { color: #6ee7b7; }
    .summary-item .value.mismatch { color: #fca5a5; }

    /* Transaction table */
    .txn-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .txn-table thead th {
        background: #0d3d56; color: #fff; padding: 10px 12px;
        font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;
        position: sticky; top: 0; z-index: 10;
    }
    .txn-table tbody tr { border-bottom: 1px solid #e5e7eb; }
    .txn-table tbody tr:nth-child(even) { background: #f9fafb; }
    .txn-table tbody tr:hover { background: #e8f7fa; }
    .txn-table td { padding: 8px 12px; vertical-align: middle; }
    .txn-table .amt-credit { color: #059669; font-weight: 600; }
    .txn-table .amt-debit { color: #dc2626; font-weight: 600; }
    .txn-table .col-num { width: 50px; text-align: center; color: #999; }
    .txn-table .col-date { width: 100px; }
    .txn-table .col-amount { width: 130px; text-align: right; }
    .txn-table .col-balance { width: 130px; text-align: right; }

    .txn-table-wrap { max-height: 500px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; }

    /* Action buttons */
    .action-bar { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }

    /* FNB branding */
    .fnb-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: #009b4d; color: #fff; padding: 6px 16px;
        border-radius: 20px; font-weight: 700; font-size: 14px;
    }

    /* Processing overlay */
    .processing-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 9999;
        display: flex; align-items: center; justify-content: center;
    }
    .processing-box {
        background: #fff; border-radius: 16px; padding: 40px 60px;
        text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .processing-box .spinner { font-size: 48px; color: #17A2B8; margin-bottom: 16px; }
    .processing-box .proc-text { font-size: 18px; font-weight: 600; color: #333; }
    .processing-box .proc-sub { font-size: 13px; color: #888; margin-top: 8px; }
</style>
@endpush

@section('content')
<div class="container-fluid bankconv-wrapper">

    {{-- Page Header --}}
    <div class="smartdash-page-header" style="margin-bottom: 20px;">
        <div class="page-title">
            <div class="page-icon"><i class="fas fa-exchange-alt"></i></div>
            <div>
                <h1>FNB to QuickBooks</h1>
                <p>Bank Statement Converter</p>
            </div>
        </div>
        <div class="page-breadcrumb">
            <a href="/cims/pm/system-settings"><i class="fas fa-home"></i> CIMS</a>
            <span class="separator">/</span>
            <a href="{{ route('cimsbankconv.index') }}">Conversions</a>
            <span class="separator">/</span>
            <span class="current">FNB</span>
        </div>
        <a href="{{ route('cimsbankconv.index') }}" class="btn button_master_close" style="color:#fff; text-decoration:none;">
            <i class="fa-solid fa-circle-left"></i> Close
        </a>
    </div>

    {{-- Breadcrumb White --}}
    <div class="breadcrumb_white">
        <div class="bw_title_area">
            <div class="bw_icon"><i class="fas fa-university"></i></div>
            <div>
                <div class="bw_title">FNB Bank Statement Converter</div>
                <div class="bw_subtitle">Convert FNB PDF statements to QuickBooks CSV format</div>
            </div>
        </div>
        <div class="bw_badge"><span class="fnb-badge"><i class="fas fa-file-csv"></i> FNB</span></div>
    </div>

    {{-- CARD 1: UPLOAD --}}
    <div class="card smartdash-form-card" id="uploadCard">
        <div class="card-header">
            <h4><i class="fa fa-cloud-upload-alt"></i> UPLOAD STATEMENT</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label" for="conv_client_id">Client</label>
                        <select id="conv_client_id" class="sd_drop_class" data-live-search="true" data-size="10" title="-- Select Client (Optional) --" style="width: 100%;">
                            <option value="">-- Select Client (Optional) --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->client_id }}"
                                    data-client_code="{{ $client->client_code ?? '' }}"
                                    data-company_name="{{ $client->company_name ?? '' }}"
                                >{{ $client->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="upload-drop-zone" id="dropZone">
                            <i class="fas fa-file-pdf upload-icon"></i>
                            <div class="upload-text">Drop FNB PDF Statement Here</div>
                            <div class="upload-subtext">or click to browse</div>
                            <div class="file-selected" id="fileName" style="display:none;"></div>
                            <input type="file" id="pdfFile" accept=".pdf" style="display:none;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center" style="margin-top: 10px;">
                <button class="button_master_save" type="button" id="btnConvert" disabled>
                    <i class="fas fa-cogs"></i> Convert Statement
                </button>
            </div>
        </div>
    </div>

    {{-- CARD 2: PREVIEW (hidden until conversion) --}}
    <div class="card smartdash-form-card" id="previewCard" style="display: none;">
        <div class="card-header">
            <h4><i class="fa fa-table"></i> CONVERSION PREVIEW</h4>
        </div>
        <div class="card-body">
            {{-- Summary bar --}}
            <div class="summary-bar" id="summaryBar"></div>

            {{-- Action bar --}}
            <div class="action-bar" style="margin-bottom: 16px;">
                <button class="button_master_save" type="button" id="btnDownloadCsv">
                    <i class="fas fa-file-csv"></i> Download CSV
                </button>
                <button class="button_master_close" type="button" id="btnSaveRecord" style="background: #17A2B8; border-color: #17A2B8;">
                    <i class="fas fa-save"></i> Save to History
                </button>
                <span id="txnCount" style="font-size: 13px; color: #666;"></span>
            </div>

            {{-- Transaction table --}}
            <div class="txn-table-wrap">
                <table class="txn-table">
                    <thead>
                        <tr>
                            <th class="col-num">#</th>
                            <th class="col-date">Date</th>
                            <th>Description</th>
                            <th class="col-amount">Amount</th>
                            <th class="col-balance">Balance</th>
                        </tr>
                    </thead>
                    <tbody id="txnBody"></tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Processing overlay --}}
<div class="processing-overlay" id="processingOverlay" style="display: none;">
    <div class="processing-box">
        <div class="spinner"><i class="fas fa-spinner fa-spin"></i></div>
        <div class="proc-text">Converting Statement...</div>
        <div class="proc-sub" id="procStatus">Extracting text from PDF...</div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Set PDF.js worker
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    var pdfFile = null;
    var parsedData = null;

    // Bootstrap-Select init
    $('select.sd_drop_class').selectpicker({ liveSearch: true, size: 10 });

    // =============================================
    // FILE UPLOAD HANDLING
    // =============================================
    var dropZone = document.getElementById('dropZone');
    var fileInput = document.getElementById('pdfFile');
    var fileNameEl = document.getElementById('fileName');

    dropZone.addEventListener('click', function() { fileInput.click(); });

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });
    dropZone.addEventListener('dragleave', function() {
        dropZone.classList.remove('dragover');
    });
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            handleFile(e.dataTransfer.files[0]);
        }
    });

    fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            handleFile(fileInput.files[0]);
        }
    });

    function handleFile(file) {
        if (file.type !== 'application/pdf') {
            Swal.fire({ icon: 'error', title: 'Invalid File', text: 'Please upload a PDF file.', confirmButtonColor: '#17A2B8' });
            return;
        }
        pdfFile = file;
        fileNameEl.textContent = file.name;
        fileNameEl.style.display = 'block';
        document.getElementById('btnConvert').disabled = false;
    }

    // =============================================
    // CONVERT BUTTON
    // =============================================
    document.getElementById('btnConvert').addEventListener('click', function() {
        if (!pdfFile) return;
        convertStatement();
    });

    async function convertStatement() {
        var overlay = document.getElementById('processingOverlay');
        var procStatus = document.getElementById('procStatus');
        overlay.style.display = 'flex';
        procStatus.textContent = 'Extracting text from PDF...';

        try {
            // Step 1: Extract text from PDF using PDF.js
            var arrayBuffer = await pdfFile.arrayBuffer();
            var pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            var pages = [];

            for (var i = 1; i <= pdf.numPages; i++) {
                procStatus.textContent = 'Reading page ' + i + ' of ' + pdf.numPages + '...';
                var page = await pdf.getPage(i);
                var textContent = await page.getTextContent();

                // Group text items by Y coordinate to form lines
                var items = textContent.items;
                var lineMap = {};

                items.forEach(function(item) {
                    // Round Y to nearest 2px to group items on same line
                    var y = Math.round(item.transform[5] / 2) * 2;
                    if (!lineMap[y]) lineMap[y] = [];
                    lineMap[y].push({
                        text: item.str,
                        x: item.transform[4],
                        width: item.width
                    });
                });

                // Sort by Y (descending — PDF coordinates are bottom-up)
                var sortedYs = Object.keys(lineMap).sort(function(a, b) { return b - a; });
                var pageText = '';

                sortedYs.forEach(function(y) {
                    // Sort items on same line by X coordinate
                    var lineItems = lineMap[y].sort(function(a, b) { return a.x - b.x; });

                    // Join items with spacing based on x-position gaps
                    var lineStr = '';
                    var lastX = 0;
                    lineItems.forEach(function(item, idx) {
                        if (idx > 0) {
                            var gap = item.x - lastX;
                            if (gap > 15) lineStr += '  '; // large gap = column separator
                            else if (gap > 3) lineStr += ' ';
                        }
                        lineStr += item.text;
                        lastX = item.x + (item.width || 0);
                    });

                    pageText += lineStr + '\n';
                });

                pages.push(pageText);
            }

            // Step 2: Send text to server for parsing
            procStatus.textContent = 'Parsing FNB statement format...';

            var response = await $.ajax({
                url: '{{ route("cimsbankconv.api.parse-fnb") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    pages: pages
                },
                dataType: 'json'
            });

            overlay.style.display = 'none';

            if (response.error) {
                Swal.fire({ icon: 'error', title: 'Parse Error', text: response.error, confirmButtonColor: '#17A2B8' });
                return;
            }

            parsedData = response;
            displayPreview(response);

        } catch (err) {
            overlay.style.display = 'none';
            console.error('Conversion error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Conversion Failed',
                text: 'Error: ' + (err.message || err.statusText || 'Unknown error'),
                confirmButtonColor: '#17A2B8'
            });
        }
    }

    // =============================================
    // DISPLAY PREVIEW
    // =============================================
    function displayPreview(data) {
        var header = data.header;
        var txns = data.transactions;
        var summary = data.summary;

        // Show preview card
        document.getElementById('previewCard').style.display = 'block';

        // Summary bar
        var balanceClass = summary.balance_match ? 'match' : 'mismatch';
        var balanceIcon = summary.balance_match ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-triangle"></i>';

        document.getElementById('summaryBar').innerHTML =
            '<div class="summary-item"><div class="label">Account</div><div class="value">' + (header.account_number || '-') + '</div></div>' +
            '<div class="summary-item"><div class="label">Period</div><div class="value" style="font-size:13px;">' + (header.statement_period || '-') + '</div></div>' +
            '<div class="summary-item"><div class="label">Opening</div><div class="value">' + formatNum(header.opening_balance) + '</div></div>' +
            '<div class="summary-item"><div class="label">Credits (' + summary.credit_count + ')</div><div class="value credit">+' + formatNum(summary.total_credits) + '</div></div>' +
            '<div class="summary-item"><div class="label">Debits (' + summary.debit_count + ')</div><div class="value debit">-' + formatNum(summary.total_debits) + '</div></div>' +
            '<div class="summary-item"><div class="label">Closing</div><div class="value">' + formatNum(header.closing_balance) + '</div></div>' +
            '<div class="summary-item"><div class="label">Balance Check</div><div class="value ' + balanceClass + '">' + balanceIcon + ' ' + (summary.balance_match ? 'MATCHED' : 'MISMATCH') + '</div></div>';

        // Transaction count
        document.getElementById('txnCount').textContent = summary.transaction_count + ' transactions extracted';

        // Transaction table
        var tbody = document.getElementById('txnBody');
        tbody.innerHTML = '';

        txns.forEach(function(txn, idx) {
            var amtClass = txn.amount >= 0 ? 'amt-credit' : 'amt-debit';
            var amtPrefix = txn.amount >= 0 ? '+' : '';
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="col-num">' + (idx + 1) + '</td>' +
                '<td class="col-date">' + txn.date + '</td>' +
                '<td>' + escapeHtml(txn.description) + '</td>' +
                '<td class="col-amount ' + amtClass + '">' + amtPrefix + formatNum(txn.amount) + '</td>' +
                '<td class="col-balance">' + formatNum(txn.balance) + '</td>';
            tbody.appendChild(tr);
        });

        // Scroll to preview
        document.getElementById('previewCard').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // =============================================
    // DOWNLOAD CSV
    // =============================================
    document.getElementById('btnDownloadCsv').addEventListener('click', function() {
        if (!parsedData || !parsedData.transactions.length) {
            Swal.fire({ icon: 'warning', title: 'No Data', text: 'No transactions to download.', confirmButtonColor: '#17A2B8' });
            return;
        }

        var header = parsedData.header;
        var txns = parsedData.transactions;
        var account = header.account_number || '';

        // Build CSV — QuickBooks format: account, date, description, amount
        var csv = 'account,date,description,amount\n';
        txns.forEach(function(txn) {
            var desc = '"' + txn.description.replace(/"/g, '""') + '"';
            csv += account + ',' + txn.date + ',' + desc + ',' + txn.amount + '\n';
        });

        // Download
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = (pdfFile ? pdfFile.name.replace('.pdf', '') : 'statement') + '-quickbooks.csv';
        link.click();
    });

    // =============================================
    // SAVE TO HISTORY
    // =============================================
    document.getElementById('btnSaveRecord').addEventListener('click', function() {
        if (!parsedData) return;

        var $client = $('#conv_client_id').find('option:selected');
        var summary = parsedData.summary;
        var header = parsedData.header;

        $.ajax({
            url: '{{ route("cimsbankconv.api.save-conversion") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                client_id: $('#conv_client_id').val() || null,
                client_code: $client.data('client_code') || '',
                company_name: $client.data('company_name') || header.account_holder || '',
                bank_type: 'fnb',
                account_number: header.account_number || '',
                statement_period: header.statement_period || '',
                opening_balance: header.opening_balance,
                closing_balance: header.closing_balance,
                total_credits: summary.total_credits,
                total_debits: summary.total_debits,
                credit_count: summary.credit_count,
                debit_count: summary.debit_count,
                transaction_count: summary.transaction_count,
                original_filename: pdfFile ? pdfFile.name : ''
            },
            dataType: 'json',
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: 'Conversion saved to history.',
                        confirmButtonColor: '#17A2B8',
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: 'Save Failed', text: 'Could not save conversion record.', confirmButtonColor: '#17A2B8' });
            }
        });
    });

    // =============================================
    // HELPERS
    // =============================================
    function formatNum(val) {
        var num = parseFloat(val);
        if (isNaN(num)) return '0.00';
        var parts = Math.abs(num).toFixed(2).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return (num < 0 ? '-' : '') + parts.join('.');
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

});
</script>
@endpush
