@extends('layouts.default')

@section('content')

<style>
/* ═══════════════════════════════════════════════════════ */
/* TAX COMPLIANCE STATUS                                   */
/* ═══════════════════════════════════════════════════════ */

.tcs-wrapper {
    max-width: 1100px;
    margin: 0 auto;
    padding: 15px;
}

/* Page title bar */
.tcs-title-bar {
    background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
    color: #fff;
    padding: 12px 20px;
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 4px 4px 0 0;
    margin-bottom: 0;
}

/* Client selector bar */
.tcs-selector-bar {
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-top: none;
    padding: 12px 20px;
    margin-bottom: 15px;
    border-radius: 0 0 4px 4px;
}

/* Client details panel */
.tcs-client-panel {
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 15px;
    overflow: hidden;
}
.tcs-client-panel-body {
    display: flex;
    gap: 0;
}
.tcs-client-details {
    flex: 1;
    padding: 0;
}
.tcs-client-details table {
    width: 100%;
    border-collapse: collapse;
}
.tcs-client-details table td {
    padding: 6px 14px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}
.tcs-client-details table td:first-child {
    font-weight: bold;
    color: #1a3c4d;
    width: 200px;
    background: #f8f9fa;
    border-right: 1px solid #eee;
}
.tcs-client-details table td:last-child {
    color: #333;
}
.tcs-refresh-panel {
    width: 280px;
    border-left: 2px solid #148f9f;
    background: #f8f9fa;
    padding: 0;
}
.tcs-refresh-panel table {
    width: 100%;
    border-collapse: collapse;
}
.tcs-refresh-panel table td {
    padding: 6px 14px;
    border-bottom: 1px solid #eee;
    font-size: 12px;
}
.tcs-refresh-panel table td:first-child {
    font-weight: bold;
    color: #1a3c4d;
}
.tcs-section-header-label {
    background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
    color: #fff;
    padding: 5px 14px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Search Results area */
.tcs-results-panel {
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}
.tcs-results-tabs {
    display: flex;
    background: #e9ecef;
    border-bottom: 2px solid #148f9f;
}
.tcs-results-tab {
    padding: 8px 20px;
    font-size: 12px;
    font-weight: bold;
    color: #555;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.tcs-results-tab.active {
    color: #148f9f;
    border-bottom-color: #148f9f;
    background: #fff;
}
.tcs-results-body {
    padding: 15px;
}

/* Compliance profile header */
.tcs-profile-header {
    background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
    color: #fff;
    text-align: center;
    padding: 10px;
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 4px;
    margin-bottom: 15px;
}

/* Overall compliance row */
.tcs-overall-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 15px;
}
.tcs-overall-label {
    font-size: 14px;
    font-weight: bold;
    color: #1a3c4d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Compliance badges */
.tcs-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 14px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}
.tcs-badge-compliant {
    background: #28a745;
    color: #fff;
}
.tcs-badge-noncompliant {
    background: #dc3545;
    color: #fff;
}
.tcs-badge-pending {
    background: #ffc107;
    color: #333;
}
.tcs-badge img {
    width: 22px;
    height: 22px;
    margin-right: 6px;
}

/* Accordion sections */
.tcs-accordion {
    margin-bottom: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}
.tcs-accordion-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px;
    background: #e9ecef;
    cursor: pointer;
    user-select: none;
    transition: background 0.2s;
}
.tcs-accordion-header:hover {
    background: #dde1e5;
}
.tcs-accordion-title {
    font-size: 14px;
    font-weight: bold;
    color: #1a3c4d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.tcs-accordion-title i {
    color: #148f9f;
    margin-right: 8px;
    font-size: 16px;
    transition: transform 0.3s;
}
.tcs-accordion-header[aria-expanded="true"] .tcs-accordion-title i.fa-plus-circle {
    display: none;
}
.tcs-accordion-header[aria-expanded="false"] .tcs-accordion-title i.fa-minus-circle {
    display: none;
}

/* Accordion sub-items */
.tcs-sub-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px 10px 40px;
    border-top: 1px solid #eee;
    background: #fff;
}
.tcs-sub-item:hover {
    background: #fafafa;
}
.tcs-sub-item-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 16px 8px 64px;
    border-top: 1px solid #f0f0f0;
    background: #f9fafb;
}
.tcs-sub-label {
    font-size: 13px;
    color: #333;
    font-weight: 500;
}
.tcs-sub-label i {
    color: #148f9f;
    margin-right: 8px;
    width: 18px;
    text-align: center;
}
.tcs-sub-value {
    font-size: 12px;
    color: #666;
    margin-left: 8px;
}

/* Export buttons */
.tcs-export-bar {
    margin-top: 15px;
    display: flex;
    gap: 8px;
    justify-content: flex-end;
}
.tcs-export-bar .btn {
    padding: 6px 16px;
    font-size: 12px;
    font-weight: 600;
    border-radius: 4px;
}

/* No client selected state */
.tcs-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}
.tcs-empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    color: #ccc;
}
.tcs-empty-state h4 {
    color: #666;
    margin-bottom: 8px;
}
</style>

<div class="tcs-wrapper">

    {{-- Title Bar --}}
    <div class="tcs-title-bar">
        <i class="fas fa-shield-alt"></i> Tax Compliance Status
    </div>

    {{-- Client Selector Bar --}}
    <div class="tcs-selector-bar">
        <form method="GET" action="{{ route('client.compliance-status') }}" id="tcsClientForm">
            <div class="row" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <div style="flex:1; min-width:300px;">
                    <select name="client_id" class="form-control default-select sd_drop_class" data-live-search="true" data-size="10" title="-- Select Client --" id="tcsClientSelect">
                        @foreach($clients as $c)
                            <option value="{{ $c->client_id }}" {{ (isset($client) && $client->client_id == $c->client_id) ? 'selected' : '' }}>
                                {{ $c->client_code }} - {{ $c->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-sm" style="background:#148f9f;color:#fff;padding:6px 20px;font-weight:600;">
                        <i class="fas fa-search"></i> Load
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if(isset($client))

    {{-- Client Details Panel --}}
    <div class="tcs-client-panel">
        <div style="display:flex;">
            {{-- Client Details --}}
            <div style="flex:1;">
                <div class="tcs-section-header-label">Client Details</div>
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="padding:7px 14px;font-weight:bold;color:#1a3c4d;width:180px;background:#f8f9fa;border-right:1px solid #eee;border-bottom:1px solid #eee;font-size:13px;">Client Name:</td>
                        <td style="padding:7px 14px;color:#333;border-bottom:1px solid #eee;font-size:13px;">{{ $client->company_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:7px 14px;font-weight:bold;color:#1a3c4d;width:180px;background:#f8f9fa;border-right:1px solid #eee;border-bottom:1px solid #eee;font-size:13px;">Trading Name:</td>
                        <td style="padding:7px 14px;color:#333;border-bottom:1px solid #eee;font-size:13px;">{{ $client->trading_name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:7px 14px;font-weight:bold;color:#1a3c4d;width:180px;background:#f8f9fa;border-right:1px solid #eee;border-bottom:1px solid #eee;font-size:13px;">Registration Number:</td>
                        <td style="padding:7px 14px;color:#333;border-bottom:1px solid #eee;font-size:13px;">{{ $client->company_reg_number ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="padding:7px 14px;font-weight:bold;color:#1a3c4d;width:180px;background:#f8f9fa;border-right:1px solid #eee;font-size:13px;">Tax Reference:</td>
                        <td style="padding:7px 14px;color:#333;font-size:13px;">{{ $client->tax_number ?? '—' }}</td>
                    </tr>
                </table>
            </div>
            {{-- Refresh Status --}}
            <div style="width:280px;border-left:2px solid #148f9f;background:#f8f9fa;">
                <div class="tcs-section-header-label">Refresh Status</div>
                <table style="width:100%;border-collapse:collapse;">
                    <tr>
                        <td style="padding:7px 14px;font-weight:bold;color:#1a3c4d;border-bottom:1px solid #eee;font-size:12px;">Last Refreshed:</td>
                        <td style="padding:7px 14px;color:#333;border-bottom:1px solid #eee;font-size:12px;">{{ now()->format('Y/m/d H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="padding:10px 14px;text-align:center;">
                            <a href="{{ route('client.compliance-status', ['client_id' => $client->client_id]) }}" class="btn btn-sm" style="background:#148f9f;color:#fff;font-weight:600;padding:5px 18px;">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Search Results / Compliance Profile --}}
    <div class="tcs-results-panel">

        {{-- Tabs --}}
        <div class="tcs-results-tabs">
            <div class="tcs-results-tab active">My Compliance Profile</div>
        </div>

        <div class="tcs-results-body">

            {{-- Profile Header --}}
            <div class="tcs-profile-header">
                My Compliance Profile
            </div>

            {{-- Overall Compliance Status --}}
            @php
                $hasIncomeTax = !empty($client->tax_number);
                $hasPaye = !empty($client->paye_number);
                $hasVat = !empty($client->vat_number);
                $regCompliant = $hasIncomeTax && $hasPaye && $hasVat;

                // Submission of Returns - check EMP201 data
                $emp201Filed = isset($emp201Data) ? $emp201Data->count() : 0;
                $submissionsCompliant = $emp201Filed >= 12;

                // Debt - check for outstanding amounts
                $totalOutstanding = 0;
                if (isset($emp201Data)) {
                    foreach ($emp201Data as $d) {
                        $paid = $d->amount_paid ?? 0;
                        $liable = $d->payroll_liability ?? 0;
                        if ($paid < $liable) {
                            $totalOutstanding += ($liable - $paid);
                        }
                    }
                }
                $debtCompliant = $totalOutstanding <= 0;

                $overallCompliant = $regCompliant && $submissionsCompliant && $debtCompliant;
            @endphp

            <div class="tcs-overall-row">
                <div class="tcs-overall-label">Overall Compliance Status</div>
                <div>
                    @if($overallCompliant)
                        <span class="tcs-badge tcs-badge-compliant"><i class="fas fa-check-circle" style="margin-right:6px;"></i> Compliant</span>
                    @else
                        <span class="tcs-badge tcs-badge-noncompliant"><i class="fas fa-times-circle" style="margin-right:6px;"></i> Non-Compliant</span>
                    @endif
                </div>
            </div>

            {{-- ═══════════════════════════════════ --}}
            {{-- REGISTRATION SECTION               --}}
            {{-- ═══════════════════════════════════ --}}
            <div class="tcs-accordion">
                <div class="tcs-accordion-header" data-toggle="collapse" data-target="#tcsRegistration" aria-expanded="true">
                    <div class="tcs-accordion-title">
                        <i class="fas fa-plus-circle"></i>
                        <i class="fas fa-minus-circle"></i>
                        Registration
                    </div>
                    <div>
                        @if($regCompliant)
                            <span class="tcs-badge tcs-badge-compliant"><i class="fas fa-check-circle" style="margin-right:4px;"></i> Compliant</span>
                        @else
                            <span class="tcs-badge tcs-badge-noncompliant"><i class="fas fa-times-circle" style="margin-right:4px;"></i> Non-Compliant</span>
                        @endif
                    </div>
                </div>
                <div id="tcsRegistration" class="collapse show">
                    {{-- Income Tax --}}
                    <div class="tcs-sub-item">
                        <div class="tcs-sub-label"><i class="fas fa-file-invoice-dollar"></i> Income Tax</div>
                        <div>
                            @if($hasIncomeTax)
                                <span class="tcs-badge tcs-badge-compliant" style="font-size:11px;padding:3px 10px;"><i class="fas fa-check-circle" style="margin-right:4px;"></i> Compliant</span>
                            @else
                                <span class="tcs-badge tcs-badge-noncompliant" style="font-size:11px;padding:3px 10px;"><i class="fas fa-times-circle" style="margin-right:4px;"></i> Non-Compliant</span>
                            @endif
                        </div>
                    </div>
                    @if($hasIncomeTax)
                    <div class="tcs-sub-item-inner">
                        <div class="tcs-sub-label" style="color:#666;"><i class="fas fa-hashtag" style="color:#aaa;"></i> {{ $client->tax_number }}
                            @if($client->tax_reg_date)
                                <span class="tcs-sub-value">[ Registered: {{ \Carbon\Carbon::parse($client->tax_reg_date)->format('d M Y') }} ]</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- PAYE --}}
                    <div class="tcs-sub-item">
                        <div class="tcs-sub-label"><i class="fas fa-money-check-alt"></i> PAYE</div>
                        <div>
                            @if($hasPaye)
                                <span class="tcs-badge tcs-badge-compliant" style="font-size:11px;padding:3px 10px;"><i class="fas fa-check-circle" style="margin-right:4px;"></i> Compliant</span>
                            @else
                                <span class="tcs-badge tcs-badge-noncompliant" style="font-size:11px;padding:3px 10px;"><i class="fas fa-times-circle" style="margin-right:4px;"></i> Non-Compliant</span>
                            @endif
                        </div>
                    </div>
                    @if($hasPaye)
                    <div class="tcs-sub-item-inner">
                        <div class="tcs-sub-label" style="color:#666;"><i class="fas fa-hashtag" style="color:#aaa;"></i> {{ $client->paye_number }}
                            @if($client->payroll_liability_date)
                                <span class="tcs-sub-value">[ Registered: {{ \Carbon\Carbon::parse($client->payroll_liability_date)->format('d M Y') }} ]</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- VAT --}}
                    <div class="tcs-sub-item">
                        <div class="tcs-sub-label"><i class="fas fa-percent"></i> VAT</div>
                        <div>
                            @if($hasVat)
                                <span class="tcs-badge tcs-badge-compliant" style="font-size:11px;padding:3px 10px;"><i class="fas fa-check-circle" style="margin-right:4px;"></i> Compliant</span>
                            @else
                                <span class="tcs-badge tcs-badge-noncompliant" style="font-size:11px;padding:3px 10px;"><i class="fas fa-times-circle" style="margin-right:4px;"></i> Non-Compliant</span>
                            @endif
                        </div>
                    </div>
                    @if($hasVat)
                    <div class="tcs-sub-item-inner">
                        <div class="tcs-sub-label" style="color:#666;"><i class="fas fa-hashtag" style="color:#aaa;"></i> {{ $client->vat_number }}
                            @if($client->vat_reg_date)
                                <span class="tcs-sub-value">[ Registered: {{ \Carbon\Carbon::parse($client->vat_reg_date)->format('d M Y') }} ]</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ═══════════════════════════════════ --}}
            {{-- SUBMISSION OF RETURNS SECTION       --}}
            {{-- ═══════════════════════════════════ --}}
            <div class="tcs-accordion">
                <div class="tcs-accordion-header collapsed" data-toggle="collapse" data-target="#tcsSubmissions" aria-expanded="false">
                    <div class="tcs-accordion-title">
                        <i class="fas fa-plus-circle"></i>
                        <i class="fas fa-minus-circle"></i>
                        Submission of Returns
                    </div>
                    <div>
                        @if($submissionsCompliant)
                            <span class="tcs-badge tcs-badge-compliant"><i class="fas fa-check-circle" style="margin-right:4px;"></i> Compliant</span>
                        @else
                            <span class="tcs-badge tcs-badge-noncompliant"><i class="fas fa-times-circle" style="margin-right:4px;"></i> Non-Compliant</span>
                        @endif
                    </div>
                </div>
                <div id="tcsSubmissions" class="collapse">
                    {{-- EMP201 Returns --}}
                    <div class="tcs-sub-item">
                        <div class="tcs-sub-label"><i class="fas fa-file-alt"></i> EMP201 Monthly Returns</div>
                        <div>
                            @if($submissionsCompliant)
                                <span class="tcs-badge tcs-badge-compliant" style="font-size:11px;padding:3px 10px;"><i class="fas fa-check-circle" style="margin-right:4px;"></i> {{ $emp201Filed }} / 12 Filed</span>
                            @else
                                <span class="tcs-badge tcs-badge-noncompliant" style="font-size:11px;padding:3px 10px;"><i class="fas fa-times-circle" style="margin-right:4px;"></i> {{ $emp201Filed }} / 12 Filed</span>
                            @endif
                        </div>
                    </div>
                    @if(isset($emp201Data) && $emp201Data->count() > 0)
                        @php
                            $periodLookup = [];
                            foreach($emp201Data as $dec) {
                                $suffix = substr($dec->payment_period, 4, 2);
                                $periodLookup[$suffix] = $dec;
                            }
                        @endphp
                        @if(isset($periodMonths))
                        @foreach($periodMonths as $key => $monthLabel)
                            @php $d = $periodLookup[$key] ?? null; @endphp
                            <div class="tcs-sub-item-inner">
                                <div class="tcs-sub-label" style="color:#666;">
                                    <i class="fas fa-calendar-day" style="color:#aaa;"></i> {{ $monthLabel }}
                                </div>
                                <div>
                                    @if($d)
                                        <span class="tcs-badge tcs-badge-compliant" style="font-size:10px;padding:2px 8px;">Filed</span>
                                    @else
                                        <span class="tcs-badge tcs-badge-noncompliant" style="font-size:10px;padding:2px 8px;">Outstanding</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        @endif
                    @endif
                </div>
            </div>

            {{-- ═══════════════════════════════════ --}}
            {{-- DEBT SECTION                        --}}
            {{-- ═══════════════════════════════════ --}}
            <div class="tcs-accordion">
                <div class="tcs-accordion-header collapsed" data-toggle="collapse" data-target="#tcsDebt" aria-expanded="false">
                    <div class="tcs-accordion-title">
                        <i class="fas fa-plus-circle"></i>
                        <i class="fas fa-minus-circle"></i>
                        Debt
                    </div>
                    <div>
                        @if($debtCompliant)
                            <span class="tcs-badge tcs-badge-compliant"><i class="fas fa-check-circle" style="margin-right:4px;"></i> Compliant</span>
                        @else
                            <span class="tcs-badge tcs-badge-noncompliant"><i class="fas fa-times-circle" style="margin-right:4px;"></i> Non-Compliant</span>
                        @endif
                    </div>
                </div>
                <div id="tcsDebt" class="collapse">
                    <div class="tcs-sub-item">
                        <div class="tcs-sub-label"><i class="fas fa-coins"></i> Outstanding EMP201 Debt</div>
                        <div>
                            @if($totalOutstanding > 0)
                                <span style="font-size:13px;font-weight:bold;color:#dc3545;">R {{ number_format($totalOutstanding, 2) }}</span>
                            @else
                                <span style="font-size:13px;font-weight:bold;color:#28a745;">R 0.00</span>
                            @endif
                        </div>
                    </div>
                    @if(isset($emp201Data) && $totalOutstanding > 0)
                        @foreach($emp201Data as $d)
                            @php
                                $paid = $d->amount_paid ?? 0;
                                $liable = $d->payroll_liability ?? 0;
                                $owing = $liable - $paid;
                            @endphp
                            @if($owing > 0)
                            <div class="tcs-sub-item-inner">
                                <div class="tcs-sub-label" style="color:#666;">
                                    <i class="fas fa-exclamation-triangle" style="color:#dc3545;"></i> Period {{ $d->payment_period }}
                                </div>
                                <div>
                                    <span style="font-size:12px;color:#dc3545;font-weight:600;">R {{ number_format($owing, 2) }} outstanding</span>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Export Buttons --}}
    <div class="tcs-export-bar">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print();">
            <i class="fas fa-print"></i> Print
        </button>
        <a href="{{ route('client.compliance-status-pdf', ['client_id' => $client->client_id]) }}" target="_blank" class="btn btn-outline-danger">
            <i class="fas fa-file-pdf"></i> PDF
        </a>
        <button type="button" class="btn btn-outline-success" id="tcsExportExcel">
            <i class="fas fa-file-excel"></i> Excel
        </button>
    </div>

    @else

    {{-- Empty State --}}
    <div class="tcs-results-panel">
        <div class="tcs-empty-state">
            <div><i class="fas fa-shield-alt"></i></div>
            <h4>Select a Client</h4>
            <p>Choose a client from the dropdown above to view their Tax Compliance Status.</p>
        </div>
    </div>

    @endif

</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit on client selection
    $('#tcsClientSelect').on('changed.bs.select', function() {
        if ($(this).val()) {
            $('#tcsClientForm').submit();
        }
    });

    // Toggle +/- icons on accordion
    $('.tcs-accordion-header').on('click', function() {
        var expanded = $(this).attr('aria-expanded');
        if (expanded === 'true') {
            $(this).attr('aria-expanded', 'false');
        } else {
            $(this).attr('aria-expanded', 'true');
        }
    });
});
</script>
@endpush
