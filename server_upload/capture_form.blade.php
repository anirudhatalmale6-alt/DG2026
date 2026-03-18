@extends('layouts.default')

@push('styles')
<style>
    /* ═══ CAPTURE FORM - Matches CIMS system styling ═══ */
    .cf-page { padding: 20px 0 40px; max-width: 1100px; margin: 0 auto; }

    /* Client selector bar */
    .cf-selector {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 16px 20px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .cf-selector-row {
        display: flex;
        gap: 14px;
        align-items: flex-end;
    }
    .cf-selector-col { flex: 1; min-width: 0; }
    .cf-selector-col label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #1a3c4d;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .cf-selector-col-btn { flex: 0 0 auto; }

    /* Card wrapper - matches CIMS info_sheet cards */
    .cf-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .cf-card-header {
        background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
        padding: 12px 20px;
    }
    .cf-card-header h3 {
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        margin: 0;
        letter-spacing: 0.5px;
    }
    .cf-card-header h3 i { margin-right: 8px; }
    .cf-card-body { padding: 20px; }

    .cf-description {
        font-size: 14px;
        color: #555;
        margin-bottom: 16px;
        line-height: 1.6;
    }
    .cf-description ul {
        margin: 8px 0 0;
        padding-left: 20px;
    }
    .cf-description ul li { margin-bottom: 4px; }

    /* Export buttons bar - matches CIMS system */
    .cf-export-bar {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        padding: 12px 24px;
        background: #f8f9fa;
        border-top: 1px solid #e0e0e0;
    }

    /* Empty state */
    .cf-empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }
    .cf-empty-state i {
        font-size: 48px;
        color: #ccc;
        margin-bottom: 12px;
        display: block;
    }
    .cf-empty-state p {
        font-size: 16px;
        font-weight: 500;
    }

    @media print {
        .cf-selector, .cf-export-bar, .page-titles, .header, .sidebar, .footer-content, .cims-master-menu { display: none !important; }
        body { background: #fff !important; }
        .cf-page { padding: 0; }
    }
</style>
@endpush

@section('content')
<div class="cf-page">

    {{-- ═══ BLANK CAPTURE FORM CARD ═══ --}}
    <div class="cf-card">
        <div class="cf-card-header">
            <h3><i class="fa fa-file-alt"></i> Blank Client Capture Form</h3>
        </div>
        <div class="cf-card-body">
            <div class="cf-description">
                Download a blank capture form for manual data collection. This printable PDF follows the same flow as the CIMS client master form, making it easy for data capturers to input information.
                <ul>
                    <li>All sections: Company Info, Tax, Payroll, VAT, Contact, Address, Directors, SARS, Banking, BEE, General</li>
                    <li>Document checklist for gathering required supporting documents</li>
                    <li>Large handwriting-friendly fields</li>
                </ul>
            </div>
        </div>
        <div class="cf-export-bar">
            <button onclick="window.print();" class="btn btn-sm btn-outline-secondary">
                <i class="fa fa-print me-1"></i> Print
            </button>
            <a href="{{ route('client.capture-form-blank-pdf') }}" target="_blank" class="btn btn-sm btn-danger">
                <i class="fa fa-file-pdf me-1"></i> Download PDF
            </a>
        </div>
    </div>

    {{-- ═══ COMPLETED CAPTURE FORM CARD ═══ --}}
    <div class="cf-card">
        <div class="cf-card-header">
            <h3><i class="fa fa-file-invoice"></i> Completed Client Capture Form</h3>
        </div>
        <div class="cf-card-body">
            <div class="cf-description">
                Generate a pre-filled capture form for any client in the system. Select a client below to produce a PDF with all their captured data.
            </div>

            <div class="cf-selector">
                <div class="cf-selector-row">
                    <div class="cf-selector-col" style="flex:2;">
                        <label><i class="fa fa-user me-1"></i> Select Client</label>
                        <select id="cfClientSelect" class="sd_drop_class" data-live-search="true" data-size="10" title="-- Select Client --" style="width:100%;">
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $c)
                            <option value="{{ $c->client_id }}" {{ ($selectedClientId == $c->client_id) ? 'selected' : '' }}>{{ $c->client_code }} - {{ $c->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cf-selector-col-btn">
                        <button id="btnLoadCompleted" class="btn btn-primary" style="background:linear-gradient(135deg,#0e6977,#148f9f);border:none;padding:8px 20px;">
                            <i class="fa fa-search me-1"></i> Load
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="cf-export-bar">
            <button id="btnEmailCompleted" class="btn btn-sm btn-outline-primary" disabled>
                <i class="fa fa-envelope me-1"></i> Email
            </button>
            <a id="btnDownloadPdf" href="#" target="_blank" class="btn btn-sm btn-danger disabled" style="pointer-events:none;">
                <i class="fa fa-file-pdf me-1"></i> PDF
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('select.sd_drop_class').selectpicker({ liveSearch: true, size: 10 });

    function getCompletedPdfUrl() {
        var clientId = $('#cfClientSelect').val();
        if (!clientId) return null;
        return '{{ route("client.capture-form-completed-pdf") }}?client_id=' + clientId;
    }

    // Enable/disable buttons based on client selection
    $('#cfClientSelect').on('changed.bs.select', function() {
        var hasClient = !!$(this).val();
        var url = getCompletedPdfUrl();
        $('#btnEmailCompleted').prop('disabled', !hasClient);
        if (hasClient && url) {
            $('#btnDownloadPdf').attr('href', url).removeClass('disabled').css('pointer-events', 'auto');
        } else {
            $('#btnDownloadPdf').attr('href', '#').addClass('disabled').css('pointer-events', 'none');
        }
    });

    // Load button - opens PDF in new tab
    $('#btnLoadCompleted').on('click', function() {
        var url = getCompletedPdfUrl();
        if (!url) {
            Swal.fire({ icon: 'warning', title: 'Selection Required', text: 'Please select a client first.', confirmButtonColor: '#148f9f' });
            return;
        }
        window.open(url, '_blank');
    });

    // Email button
    $('#btnEmailCompleted').on('click', function() {
        var clientId = $('#cfClientSelect').val();
        if (!clientId) return;
        Swal.fire({
            title: 'Email Capture Form',
            input: 'email',
            inputLabel: 'Enter recipient email address',
            inputPlaceholder: 'email@example.com',
            showCancelButton: true,
            confirmButtonColor: '#148f9f',
            confirmButtonText: '<i class="fa fa-paper-plane me-1"></i> Send',
            inputValidator: function(value) {
                if (!value) return 'Please enter an email address.';
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                Swal.fire({ icon: 'info', title: 'Coming Soon', text: 'Email functionality will be available in the next update.', confirmButtonColor: '#148f9f' });
            }
        });
    });
});
</script>
@endpush
