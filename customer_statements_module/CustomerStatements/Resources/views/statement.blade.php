@extends('layout.wrapper')

@section('settings-css')
<link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
<style>
    /* Statement page styling */
    .statement-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        padding: 0;
        overflow: hidden;
    }

    /* Header bar */
    .statement-top-bar {
        background: linear-gradient(135deg, #17A2B8, #0d3d56);
        padding: 20px 30px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .statement-top-bar h4 {
        color: #fff;
        margin: 0;
        font-weight: 600;
    }
    .statement-top-bar .btn-group .btn {
        border-radius: 5px;
        margin-left: 8px;
        font-weight: 500;
    }
    .btn-pdf {
        background: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }
    .btn-pdf:hover {
        background: #c82333;
        border-color: #bd2130;
        color: #fff;
    }
    .btn-email-stmt {
        background: #28a745;
        border-color: #28a745;
        color: #fff;
    }
    .btn-email-stmt:hover {
        background: #218838;
        border-color: #1e7e34;
        color: #fff;
    }
    .btn-print {
        background: #6c757d;
        border-color: #6c757d;
        color: #fff;
    }
    .btn-print:hover {
        background: #5a6268;
        border-color: #545b62;
        color: #fff;
    }
    .btn-back {
        background: rgba(255,255,255,0.2);
        border-color: rgba(255,255,255,0.4);
        color: #fff;
    }
    .btn-back:hover {
        background: rgba(255,255,255,0.3);
        color: #fff;
    }

    /* Statement body */
    .statement-body {
        padding: 30px;
    }

    /* Company header */
    .company-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 3px solid #17A2B8;
    }
    .company-logo img {
        max-height: 80px;
        max-width: 250px;
    }
    .company-info {
        text-align: right;
    }
    .company-info h4 {
        color: #0d3d56;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .company-info p {
        margin: 0;
        color: #555;
        font-size: 13px;
        line-height: 1.6;
    }

    /* Statement title */
    .statement-title {
        text-align: center;
        margin: 20px 0;
    }
    .statement-title h2 {
        color: #0d3d56;
        font-weight: 700;
        font-size: 26px;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    .statement-title .period {
        color: #666;
        font-size: 14px;
    }

    /* Client details */
    .client-details-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .client-details-box h5 {
        color: #0d3d56;
        font-weight: 600;
        margin-bottom: 12px;
        font-size: 15px;
    }
    .client-details-box table td {
        padding: 3px 10px 3px 0;
        font-size: 13px;
        color: #333;
        vertical-align: top;
    }
    .client-details-box table td:first-child {
        font-weight: 600;
        color: #555;
        width: 130px;
    }

    /* Transaction table */
    .statement-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
        font-size: 13px;
    }
    .statement-table thead th {
        background: #1a1a2e;
        color: #fff;
        padding: 12px 15px;
        font-weight: 600;
        text-align: left;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .statement-table thead th:nth-child(n+4) {
        text-align: right;
    }
    .statement-table tbody td {
        padding: 10px 15px;
        border-bottom: 1px solid #e9ecef;
        color: #333;
    }
    .statement-table tbody td:nth-child(n+4) {
        text-align: right;
        font-family: 'Courier New', monospace;
    }
    .statement-table tbody tr:nth-child(even) {
        background: #fafbfc;
    }
    .statement-table tbody tr:hover {
        background: #f0f9ff;
    }
    .statement-table tbody tr.opening-balance-row {
        background: #e8f4f8;
        font-weight: 600;
    }
    .statement-table tbody tr.opening-balance-row td {
        border-bottom: 2px solid #17A2B8;
    }
    .statement-table tbody tr.row-invoice td.ref-col {
        color: #17A2B8;
        font-weight: 500;
    }
    .statement-table tbody tr.row-payment td.ref-col {
        color: #28a745;
        font-weight: 500;
    }

    /* Totals rows */
    .statement-table tfoot td {
        padding: 12px 15px;
        font-weight: 700;
        font-size: 13px;
    }
    .statement-table tfoot td:nth-child(n+4) {
        text-align: right;
        font-family: 'Courier New', monospace;
    }
    .statement-table tfoot tr.totals-row {
        background: #f0f0f0;
        border-top: 2px solid #333;
    }
    .statement-table tfoot tr.closing-balance-row {
        background: #0d3d56;
        color: #fff;
    }
    .statement-table tfoot tr.closing-balance-row td {
        color: #fff;
        font-size: 14px;
        padding: 14px 15px;
    }

    /* Aging summary */
    .aging-section {
        margin-top: 30px;
    }
    .aging-section h5 {
        color: #0d3d56;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 16px;
    }
    .aging-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .aging-table thead th {
        background: #17A2B8;
        color: #fff;
        padding: 12px 20px;
        text-align: center;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .aging-table tbody td {
        padding: 14px 20px;
        text-align: center;
        font-weight: 600;
        font-size: 15px;
        border: 1px solid #e9ecef;
        font-family: 'Courier New', monospace;
    }
    .aging-table tbody td.aging-current {
        background: #d4edda;
        color: #155724;
    }
    .aging-table tbody td.aging-30 {
        background: #fff3cd;
        color: #856404;
    }
    .aging-table tbody td.aging-60 {
        background: #fce4d6;
        color: #c0392b;
    }
    .aging-table tbody td.aging-90 {
        background: #f8d7da;
        color: #721c24;
    }
    .aging-table tbody td.aging-total {
        background: #0d3d56;
        color: #fff;
        font-size: 16px;
    }

    /* Type badges */
    .type-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .type-invoice {
        background: #e8f4f8;
        color: #17A2B8;
    }
    .type-payment {
        background: #d4edda;
        color: #28a745;
    }

    /* Print styles */
    @media print {
        .statement-top-bar,
        .no-print {
            display: none !important;
        }
        .statement-container {
            box-shadow: none;
            border: none;
        }
        .statement-body {
            padding: 10px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="mdi mdi-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="mdi mdi-alert-circle mr-2"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="statement-container">

        <!-- Top Action Bar -->
        <div class="statement-top-bar">
            <div>
                <a href="{{ route('statements.index') }}" class="btn btn-sm btn-back">
                    <i class="mdi mdi-arrow-left mr-1"></i> Back
                </a>
                <h4 class="d-inline-block ml-3">Statement of Account</h4>
            </div>
            <div class="btn-group">
                <a href="{{ route('statements.pdf', ['client_id' => $client->client_id]) }}?from={{ $from_date }}&to={{ $to_date }}"
                   class="btn btn-sm btn-pdf" target="_blank">
                    <i class="mdi mdi-file-pdf mr-1"></i> Download PDF
                </a>
                <button type="button" class="btn btn-sm btn-email-stmt" id="emailBtn"
                        data-client-id="{{ $client->client_id }}"
                        data-from="{{ $from_date }}"
                        data-to="{{ $to_date }}">
                    <i class="mdi mdi-email mr-1"></i> Email to Client
                </button>
                <button type="button" class="btn btn-sm btn-print" onclick="window.print()">
                    <i class="mdi mdi-printer mr-1"></i> Print
                </button>
            </div>
        </div>

        <!-- Statement Body -->
        <div class="statement-body" id="printableArea">

            <!-- Company Header -->
            <div class="company-header">
                <div class="company-logo">
                    <img src="{{ asset('storage/logos/app/cims_inv_logo.png') }}" alt="Company Logo">
                </div>
                <div class="company-info">
                    <h4>{{ $settings['settings_company_name'] }}</h4>
                    <p>
                        {{ $settings['settings_company_address_line_1'] }}<br>
                        {{ $settings['settings_company_city'] }}, {{ $settings['settings_company_state'] }}<br>
                        {{ $settings['settings_company_zipcode'] }}, {{ $settings['settings_company_country'] }}<br>
                        {{ $settings['settings_company_customfield_1'] }}
                    </p>
                </div>
            </div>

            <!-- Statement Title -->
            <div class="statement-title">
                <h2>Statement of Account</h2>
                <p class="period">
                    Period: {{ \Modules\CustomerStatements\Services\StatementService::formatDate($from_date) }}
                    to {{ \Modules\CustomerStatements\Services\StatementService::formatDate($to_date) }}
                </p>
            </div>

            <!-- Client Details -->
            <div class="client-details-box">
                <h5>Bill To:</h5>
                <table>
                    <tr>
                        <td>Company:</td>
                        <td><strong>{{ $client->client_company_name }}</strong></td>
                    </tr>
                    @if($client_code)
                    <tr>
                        <td>Client Code:</td>
                        <td>{{ $client_code }}</td>
                    </tr>
                    @endif
                    @if($client->client_billing_street || $client->client_billing_city)
                    <tr>
                        <td>Address:</td>
                        <td>
                            {{ $client->client_billing_street }}<br>
                            @if($client->client_billing_city){{ $client->client_billing_city }}, @endif
                            @if($client->client_billing_state){{ $client->client_billing_state }}@endif
                            @if($client->client_billing_zip) {{ $client->client_billing_zip }}@endif
                            @if($client->client_billing_country)<br>{{ $client->client_billing_country }}@endif
                        </td>
                    </tr>
                    @endif
                    @if($client->client_vat)
                    <tr>
                        <td>VAT Number:</td>
                        <td>{{ $client->client_vat }}</td>
                    </tr>
                    @endif
                    @if($client->client_phone)
                    <tr>
                        <td>Phone:</td>
                        <td>{{ $client->client_phone }}</td>
                    </tr>
                    @endif
                    @if($client_email)
                    <tr>
                        <td>Email:</td>
                        <td>{{ $client_email }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            @php
                $currency = $settings['settings_system_currency_symbol'] ?? 'R ';
            @endphp

            <!-- Transaction Table -->
            <table class="statement-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Date</th>
                        <th style="width: 90px;">Type</th>
                        <th style="width: 130px;">Reference</th>
                        <th>Description</th>
                        <th style="width: 120px;">Debit</th>
                        <th style="width: 120px;">Credit</th>
                        <th style="width: 130px;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Opening Balance Row -->
                    <tr class="opening-balance-row">
                        <td>{{ \Modules\CustomerStatements\Services\StatementService::formatDate($from_date) }}</td>
                        <td></td>
                        <td></td>
                        <td><strong>Opening Balance</strong></td>
                        <td></td>
                        <td></td>
                        <td><strong>{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($opening_balance, $currency) }}</strong></td>
                    </tr>

                    @forelse($transactions as $txn)
                    <tr class="row-{{ strtolower($txn['type']) }}">
                        <td>{{ \Modules\CustomerStatements\Services\StatementService::formatDate($txn['date']) }}</td>
                        <td>
                            <span class="type-badge type-{{ strtolower($txn['type']) }}">
                                {{ $txn['type'] }}
                            </span>
                        </td>
                        <td class="ref-col">{{ $txn['reference'] }}</td>
                        <td>{{ $txn['description'] }}</td>
                        <td>{{ $txn['debit'] > 0 ? \Modules\CustomerStatements\Services\StatementService::formatCurrency($txn['debit'], $currency) : '' }}</td>
                        <td>{{ $txn['credit'] > 0 ? \Modules\CustomerStatements\Services\StatementService::formatCurrency($txn['credit'], $currency) : '' }}</td>
                        <td>{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($txn['balance'], $currency) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="mdi mdi-file-document-outline" style="font-size: 32px; display: block; margin-bottom: 10px;"></i>
                            No transactions found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="totals-row">
                        <td colspan="4" class="text-right"><strong>Totals:</strong></td>
                        <td>{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($total_debits, $currency) }}</td>
                        <td>{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($total_credits, $currency) }}</td>
                        <td></td>
                    </tr>
                    <tr class="closing-balance-row">
                        <td colspan="6" class="text-right">Closing Balance:</td>
                        <td>{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($closing_balance, $currency) }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Aging Summary -->
            @if(isset($aging))
            <div class="aging-section">
                <h5><i class="mdi mdi-clock-outline mr-2"></i> Aging Summary</h5>
                <table class="aging-table">
                    <thead>
                        <tr>
                            <th>Current (0-30 days)</th>
                            <th>30 Days (31-60 days)</th>
                            <th>60 Days (61-90 days)</th>
                            <th>90+ Days (91+ days)</th>
                            <th>Total Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="aging-current">{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($aging['buckets']['current'], $currency) }}</td>
                            <td class="aging-30">{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($aging['buckets']['30_days'], $currency) }}</td>
                            <td class="aging-60">{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($aging['buckets']['60_days'], $currency) }}</td>
                            <td class="aging-90">{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($aging['buckets']['90_plus'], $currency) }}</td>
                            <td class="aging-total">{{ \Modules\CustomerStatements\Services\StatementService::formatCurrency($aging['total'], $currency) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

        </div><!-- /statement-body -->

    </div><!-- /statement-container -->

</div>

<!-- Email Confirmation Modal -->
<div class="modal fade" id="emailConfirmModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #17A2B8; color: #fff;">
                <h5 class="modal-title" style="color: #fff;">
                    <i class="mdi mdi-email-outline mr-2"></i> Email Statement
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="emailForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p>Send this statement to:</p>
                    <p><strong id="emailRecipient">{{ $client_email ?? 'No email on file' }}</strong></p>
                    @if($client_email)
                    <input type="hidden" name="from_date" value="{{ $from_date }}">
                    <input type="hidden" name="to_date" value="{{ $to_date }}">
                    <p class="text-muted small">
                        The statement PDF will be attached to the email.
                    </p>
                    @else
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert mr-1"></i>
                        No email address found for this client. Please add an email to the client's account owner first.
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    @if($client_email)
                    <button type="submit" class="btn btn-success" id="sendEmailBtn">
                        <i class="mdi mdi-send mr-1"></i> Send Email
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('settings-js')
<script>
$(document).ready(function() {
    // Email button click handler
    $('#emailBtn').on('click', function() {
        var clientId = $(this).data('client-id');
        var fromDate = $(this).data('from');
        var toDate = $(this).data('to');

        // Set the form action
        var actionUrl = '{{ url("statements") }}/' + clientId + '/email';
        $('#emailForm').attr('action', actionUrl);

        // Show the modal
        $('#emailConfirmModal').modal('show');
    });

    // Email form submission
    $('#emailForm').on('submit', function() {
        $('#sendEmailBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i> Sending...');
    });
});
</script>
@endsection
