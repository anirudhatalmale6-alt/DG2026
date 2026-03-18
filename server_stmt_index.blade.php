@extends('layout.wrapper')

@section('settings-css')
<!-- Select2 CSS (if not already loaded globally) -->
<link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('vendor/pickadate/themes/classic.css') }}" rel="stylesheet" />
<link href="{{ asset('vendor/pickadate/themes/classic.date.css') }}" rel="stylesheet" />
<style>
    .statement-header-card {
        background: linear-gradient(135deg, #17A2B8, #0d3d56);
        border-radius: 8px;
        color: #fff;
        padding: 30px;
        margin-bottom: 25px;
    }
    .statement-header-card h3 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .statement-header-card p {
        color: rgba(255,255,255,0.8);
        margin-bottom: 0;
    }
    .statement-form-card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }
    .statement-form-card .card-header {
        background: #f8f9fa;
        border-bottom: 2px solid #17A2B8;
        border-radius: 8px 8px 0 0 !important;
        padding: 15px 20px;
    }
    .statement-form-card .card-header h5 {
        color: #0d3d56;
        font-weight: 600;
        margin: 0;
    }
    .statement-form-card .card-body {
        padding: 25px;
    }
    .btn-statement {
        background: #20c997;
        border-color: #20c997;
        color: #fff;
        font-weight: 700;
        padding: 14px 50px;
        border-radius: 5px;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(32,201,151,0.4);
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-statement:hover {
        background: #17a085;
        border-color: #17a085;
        color: #fff;
        box-shadow: 0 6px 16px rgba(32,201,151,0.5);
    }
    .select2-container--default .select2-selection--single {
        height: 42px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 42px;
        padding-left: 12px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
    }
    .form-control.pickadate {
        height: 42px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 8px 12px;
    }
    .info-box {
        background: #f0f9ff;
        border-left: 4px solid #17A2B8;
        padding: 15px 20px;
        border-radius: 0 5px 5px 0;
        margin-top: 20px;
    }
    .info-box i {
        color: #17A2B8;
        margin-right: 8px;
    }
</style>
@endsection

@section('content')
<!-- main content -->
<div class="container-fluid">

    <!-- Header -->
    <div class="statement-header-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3><i class="sl-icon-docs mr-2"></i> Customer Statements</h3>
                <p>Generate, download, and email customer account statements with aging analysis.</p>
            </div>
            <div class="col-md-4 text-right">
                <i class="sl-icon-notebook" style="font-size: 60px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>

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

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Statement Generator Form -->
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card statement-form-card">
                <div class="card-header">
                    <h5><i class="mdi mdi-file-document-outline mr-2"></i> Generate Statement</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('statements.generate') }}" method="POST" id="statementForm">
                        @csrf

                        <!-- Client Selection -->
                        <div class="form-group">
                            <label class="form-label font-weight-bold text-dark">
                                <i class="mdi mdi-account-outline mr-1"></i> Select Client
                                <span class="text-danger">*</span>
                            </label>
                            <select name="client_id" id="client_id" class="form-control select2-client" required>
                                <option value="">-- Select a Client --</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->client_id }}">{{ $client->client_company_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold text-dark">
                                        <i class="mdi mdi-calendar mr-1"></i> From Date
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="from_date" id="from_date"
                                           class="form-control pickadate"
                                           value="{{ old('from_date', \Carbon\Carbon::parse($defaultFrom)->format('d-m-Y')) }}"
                                           autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label font-weight-bold text-dark">
                                        <i class="mdi mdi-calendar mr-1"></i> To Date
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="to_date" id="to_date"
                                           class="form-control pickadate"
                                           value="{{ old('to_date', \Carbon\Carbon::parse($defaultTo)->format('d-m-Y')) }}"
                                           autocomplete="off" required>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Date Range Buttons -->
                        <div class="form-group">
                            <label class="form-label font-weight-bold text-dark mb-2">Quick Select:</label>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary quick-range" data-range="this_month">This Month</button>
                                <button type="button" class="btn btn-outline-secondary quick-range" data-range="last_month">Last Month</button>
                                <button type="button" class="btn btn-outline-secondary quick-range" data-range="this_quarter">This Quarter</button>
                                <button type="button" class="btn btn-outline-secondary quick-range" data-range="last_quarter">Last Quarter</button>
                                <button type="button" class="btn btn-outline-secondary quick-range" data-range="this_year">This Year</button>
                                <button type="button" class="btn btn-outline-secondary quick-range" data-range="last_year">Last Year</button>
                            </div>
                        </div>

                        <hr>

                        <!-- Submit Button -->
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-statement btn-lg" id="generateBtn">
                                <i class="mdi mdi-file-chart mr-2"></i> Generate Statement
                            </button>
                        </div>
                    </form>

                    <!-- Info Box -->
                    <div class="info-box">
                        <p class="mb-0">
                            <i class="mdi mdi-information-outline"></i>
                            <strong>Note:</strong> The statement will include all invoices and payments within the selected
                            date range, along with an opening balance and aging summary for outstanding amounts.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('settings-js')
<!-- Select2 JS -->
<script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
<!-- Pickadate JS -->
<script src="{{ asset('vendor/pickadate/picker.js') }}"></script>
<script src="{{ asset('vendor/pickadate/picker.date.js') }}"></script>
<script>
$(document).ready(function() {

    // Initialize Select2 for client dropdown
    $('.select2-client').select2({
        placeholder: '-- Select a Client --',
        allowClear: true,
        width: '100%'
    });

    // Initialize Pickadate for date fields
    var fromPicker = $('#from_date').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'yyyy-mm-dd',
        hiddenName: true,
        selectMonths: true,
        selectYears: 15,
        today: 'Today',
        clear: 'Clear',
        close: 'Close'
    });

    var toPicker = $('#to_date').pickadate({
        format: 'dd-mm-yyyy',
        formatSubmit: 'yyyy-mm-dd',
        hiddenName: true,
        selectMonths: true,
        selectYears: 15,
        today: 'Today',
        clear: 'Clear',
        close: 'Close'
    });

    // Quick date range buttons
    $('.quick-range').on('click', function() {
        var range = $(this).data('range');
        var now = new Date();
        var fromDate, toDate;

        switch(range) {
            case 'this_month':
                fromDate = new Date(now.getFullYear(), now.getMonth(), 1);
                toDate = now;
                break;
            case 'last_month':
                fromDate = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                toDate = new Date(now.getFullYear(), now.getMonth(), 0);
                break;
            case 'this_quarter':
                var qMonth = Math.floor(now.getMonth() / 3) * 3;
                fromDate = new Date(now.getFullYear(), qMonth, 1);
                toDate = now;
                break;
            case 'last_quarter':
                var lqMonth = Math.floor(now.getMonth() / 3) * 3 - 3;
                var lqYear = now.getFullYear();
                if (lqMonth < 0) { lqMonth += 12; lqYear--; }
                fromDate = new Date(lqYear, lqMonth, 1);
                toDate = new Date(lqYear, lqMonth + 3, 0);
                break;
            case 'this_year':
                fromDate = new Date(now.getFullYear(), 0, 1);
                toDate = now;
                break;
            case 'last_year':
                fromDate = new Date(now.getFullYear() - 1, 0, 1);
                toDate = new Date(now.getFullYear() - 1, 11, 31);
                break;
        }

        // Set the pickers
        var fromPickerObj = fromPicker.pickadate('picker');
        var toPickerObj = toPicker.pickadate('picker');

        if (fromPickerObj && toPickerObj) {
            fromPickerObj.set('select', fromDate);
            toPickerObj.set('select', toDate);
        }

        // Highlight the active button
        $('.quick-range').removeClass('btn-secondary').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-secondary');

        // Auto-submit if a client is already selected
        var clientId = $('#client_id').val();
        if (clientId) {
            setTimeout(function() {
                $('#statementForm').submit();
            }, 300);
        }
    });

    // Form submission loading state
    $('#statementForm').on('submit', function() {
        var clientId = $('#client_id').val();
        if (!clientId) {
            alert('Please select a client.');
            return false;
        }
        $('#generateBtn').prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-2"></i> Generating...');
    });
});
</script>
@endsection
