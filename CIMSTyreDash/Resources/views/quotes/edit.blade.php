@extends('layouts.default')

@section('title', 'Edit Quote ' . $quote->quote_number)

@push('styles')
<style>
    /* ---- Quote Builder Layout ---- */
    .quote-builder-left { min-height: 80vh; }
    .quote-builder-right { position: sticky; top: 20px; }
    .section-title { font-size: 1rem; font-weight: 600; color: #17A2B8; border-bottom: 2px solid #17A2B8; padding-bottom: 8px; margin-bottom: 15px; }
    .section-title i { margin-right: 8px; }

    /* ---- Autocomplete ---- */
    .autocomplete-wrapper { position: relative; }
    .autocomplete-results { position: absolute; top: 100%; left: 0; right: 0; z-index: 1050; background: #fff; border: 1px solid #dee2e6; border-top: 0; border-radius: 0 0 6px 6px; max-height: 250px; overflow-y: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); display: none; }
    .autocomplete-results .ac-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f0f0f0; }
    .autocomplete-results .ac-item:hover { background: #e9f7fa; }
    .autocomplete-results .ac-item .ac-primary { font-weight: 600; }
    .autocomplete-results .ac-item .ac-secondary { font-size: 0.85em; color: #6c757d; }

    /* ---- Quick Add Inline ---- */
    .quick-add-panel { display: none; background: #f8f9fa; border: 1px dashed #17A2B8; border-radius: 6px; padding: 15px; margin-top: 10px; }
    .quick-add-panel.show { display: block; }

    /* ---- Size Search Results ---- */
    .size-results-table { max-height: 350px; overflow-y: auto; }
    .size-results-table table { font-size: 0.88em; }
    .size-results-table .btn-add-option { padding: 2px 10px; font-size: 0.8em; }

    /* ---- Option Tabs ---- */
    .option-tab-content .option-details { background: #fdfdfd; border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; }
    .nav-tabs .nav-link { font-weight: 500; }
    .nav-tabs .nav-link.active { border-bottom: 3px solid #17A2B8; font-weight: 600; }

    /* ---- Services Section ---- */
    .service-row { background: #fdfdfd; border: 1px solid #e9ecef; border-radius: 6px; padding: 10px 15px; margin-bottom: 8px; }

    /* ---- Summary Card ---- */
    .summary-card { border: 2px solid #17A2B8; }
    .summary-card .card-header { background: linear-gradient(135deg, #0d3d56 0%, #17A2B8 100%); color: #fff; }
    .summary-line { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.95em; }
    .summary-line.total-line { font-size: 1.2em; font-weight: 700; border-top: 2px solid #17A2B8; padding-top: 10px; margin-top: 5px; }

    /* ---- Selected Customer/Vehicle Display ---- */
    .selected-entity { background: #d4edda; border: 1px solid #28a745; border-radius: 6px; padding: 10px 15px; display: none; }
    .selected-entity.show { display: flex; align-items: center; justify-content: space-between; }
    .selected-entity .entity-info { font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4><i class="fas fa-edit me-2"></i>Edit Quote {{ $quote->quote_number }}</h4>
                <span class="ml-1">TyreDash / Quotes / Edit</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.quotes.index') }}">Quotes</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.quotes.show', $quote->id) }}">{{ $quote->quote_number }}</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="quoteForm" method="POST" action="{{ route('cimstyredash.quotes.update', $quote->id) }}">
        @csrf
        @method('PUT')
        <input type="hidden" name="action" id="formAction" value="draft">

        <div class="row">
            {{-- LEFT PANEL: Quote Form (col-8 full width, no sidebar) --}}
            <div class="col-lg-8 quote-builder-left">

                {{-- HEADER SECTION --}}
                <div class="card">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-info-circle"></i>Quote Header</div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Quote Number</label>
                                <input type="text" class="form-control" value="{{ $quote->quote_number }}" readonly>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" name="quote_date" class="form-control" value="{{ old('quote_date', $quote->quote_date->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Valid Until</label>
                                <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $quote->valid_until ? $quote->valid_until->format('Y-m-d') : '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Branch</label>
                                <select name="branch_id" class="form-select">
                                    <option value="">-- Select --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $quote->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Salesman Name</label>
                                <input type="text" name="salesman_name" class="form-control" placeholder="Salesman name" value="{{ old('salesman_name', $quote->salesman_name) }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CUSTOMER SECTION --}}
                <div class="card">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-user"></i>Customer</div>
                        <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $quote->customer_id) }}">

                        {{-- Selected Customer Display --}}
                        <div class="selected-entity {{ $quote->customer ? 'show' : '' }}" id="selectedCustomer">
                            <span class="entity-info" id="selectedCustomerName">{{ $quote->customer ? $quote->customer->display_name : '' }}</span>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-clear" onclick="clearCustomer()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>

                        {{-- Search Input --}}
                        <div class="autocomplete-wrapper" id="customerSearchWrapper" style="{{ $quote->customer ? 'display:none' : '' }}">
                            <input type="text" class="form-control" id="customerSearch" placeholder="Search by name, phone, email..." autocomplete="off">
                            <div class="autocomplete-results" id="customerResults"></div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="toggleQuickAdd('customer')">
                            <i class="fas fa-plus me-1"></i> Quick Add Customer
                        </button>
                        <div class="quick-add-panel" id="customerQuickAdd">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-sm" id="qaFirstName" placeholder="First Name">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-sm" id="qaLastName" placeholder="Last Name">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control form-control-sm" id="qaPhone" placeholder="Phone">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- VEHICLE SECTION --}}
                <div class="card">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-car"></i>Vehicle</div>
                        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id', $quote->vehicle_id) }}">

                        {{-- Selected Vehicle Display --}}
                        <div class="selected-entity {{ $quote->vehicle ? 'show' : '' }}" id="selectedVehicle">
                            <span class="entity-info" id="selectedVehicleName">{{ $quote->vehicle ? $quote->vehicle->display_name : '' }}</span>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-clear" onclick="clearVehicle()">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>

                        {{-- Search Input --}}
                        <div class="autocomplete-wrapper" id="vehicleSearchWrapper" style="{{ $quote->vehicle ? 'display:none' : '' }}">
                            <input type="text" class="form-control" id="vehicleSearch" placeholder="Search by registration, make, model..." autocomplete="off">
                            <div class="autocomplete-results" id="vehicleResults"></div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="toggleQuickAdd('vehicle')">
                            <i class="fas fa-plus me-1"></i> Quick Add Vehicle
                        </button>
                        <div class="quick-add-panel" id="vehicleQuickAdd">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <input type="text" class="form-control form-control-sm" id="qaRegistration" placeholder="Registration">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control form-control-sm" id="qaMake" placeholder="Make">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control form-control-sm" id="qaModel" placeholder="Model">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control form-control-sm" id="qaYear" placeholder="Year">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SIZE SEARCH SECTION --}}
                <div class="card">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-search"></i>Tyre Size Search</div>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Enter Tyre Size (e.g. 265/65R17)</label>
                                <input type="text" class="form-control" id="sizeSearchInput" placeholder="265/65R17" autocomplete="off">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-primary w-100" id="sizeSearchBtn">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
                        </div>

                        <div id="sizeResultsContainer" class="mt-3" style="display:none;">
                            <h6 class="text-muted"><i class="fas fa-list me-1"></i>Search Results <span id="sizeResultsCount" class="badge bg-info"></span></h6>
                            <div class="size-results-table">
                                <table class="table table-sm table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>Size</th>
                                            <th class="text-end">Cost</th>
                                            <th class="text-end">Sell</th>
                                            <th class="text-center">SOH</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sizeResultsBody"></tbody>
                                </table>
                            </div>
                            <div id="sizeNoResults" class="text-center text-muted py-3" style="display:none;">
                                <i class="fas fa-info-circle me-1"></i> No products found for this size.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- OPTION TABS SECTION --}}
                <div class="card">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-th-list"></i>Quote Options (up to 5)</div>

                        <ul class="nav nav-tabs" id="quoteOptionTabs" role="tablist"></ul>
                        <div class="tab-content option-tab-content mt-3" id="quoteOptionTabContent">
                            <div id="noOptionsMsg" class="text-center text-muted py-4" style="display:none;">
                                <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                                Use the Size Search above to add tyre options.
                            </div>
                        </div>

                        <div id="optionHiddenInputs"></div>
                    </div>
                </div>

                {{-- SERVICES SECTION --}}
                <div class="card">
                    <div class="card-body">
                        <div class="section-title"><i class="fas fa-cogs"></i>Services</div>
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Add Service</label>
                                <select class="form-select" id="serviceSelect">
                                    <option value="">-- Select Service --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}"
                                                data-name="{{ $service->name }}"
                                                data-code="{{ $service->code }}"
                                                data-price="{{ $service->price }}"
                                                data-per-tyre="{{ $service->price_per_tyre ? '1' : '0' }}">
                                            {{ $service->name }} ({{ $service->code }}) - {{ number_format($service->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-success w-100" id="addServiceBtn">
                                    <i class="fas fa-plus me-1"></i> Add Service
                                </button>
                            </div>
                        </div>

                        <div id="servicesContainer"></div>
                        <div id="noServicesMsg" class="text-center text-muted py-3" style="display:none;">
                            <i class="fas fa-info-circle me-1"></i> No services added yet.
                        </div>

                        <div id="serviceHiddenInputs"></div>
                    </div>
                </div>
            </div>

            {{-- RIGHT PANEL: Summary Card (col-4) --}}
            <div class="col-lg-4">
                <div class="quote-builder-right">
                    <div class="card summary-card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Quote Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="summary-line">
                                <span>Selected Option:</span>
                                <span id="summaryOptionTotal">R0.00</span>
                            </div>
                            <div class="summary-line">
                                <span>Services:</span>
                                <span id="summaryServicesTotal">R0.00</span>
                            </div>
                            <div class="summary-line">
                                <span>Subtotal (excl VAT):</span>
                                <span id="summarySubtotal">R0.00</span>
                            </div>
                            <div class="summary-line">
                                <span>VAT ({{ $vatRate }}%):</span>
                                <span id="summaryVat">R0.00</span>
                            </div>
                            <div class="summary-line total-line">
                                <span>Grand Total:</span>
                                <span id="summaryGrandTotal">R0.00</span>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label">Customer Order Ref</label>
                                <input type="text" name="customer_order_ref" class="form-control" placeholder="PO / reference number" value="{{ old('customer_order_ref', $quote->customer_order_ref) }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Customer Comment</label>
                                <textarea name="customer_comment" class="form-control" rows="2" placeholder="Visible to customer...">{{ old('customer_comment', $quote->customer_comment) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Internal Notes</label>
                                <textarea name="internal_notes" class="form-control" rows="2" placeholder="Internal only...">{{ old('internal_notes', $quote->internal_notes) }}</textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-warning btn-lg" onclick="submitQuote('draft')">
                                    <i class="fas fa-save me-1"></i> Update Quote
                                </button>
                                <a href="{{ route('cimstyredash.quotes.show', $quote->id) }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // ---- Configuration ----
    const ROUTES = {
        searchCustomers: '{{ route("cimstyredash.ajax.search-customers") }}',
        searchVehicles:  '{{ route("cimstyredash.ajax.search-vehicles") }}',
        sizeSearch:      '{{ route("cimstyredash.ajax.size-search") }}',
    };
    const VAT_RATE = {{ $vatRate }};
    const DEFAULT_MARKUP = {{ $defaultMarkup }};
    const MAX_OPTIONS = 5;

    // ---- Pre-populated State from existing quote ----
    let options = [];
    let services = [];
    let debounceTimer = null;

    // Load existing options
    @foreach($quote->quoteOptions as $opt)
    options.push({
        product_id: {{ $opt->product_id }},
        brand: @json($opt->product->brand->name ?? 'N/A'),
        brand_logo: @json($opt->product->brand->logo_url ?? ''),
        model_name: @json($opt->product->model_name ?? ''),
        size: @json($opt->product->size->full_size ?? ''),
        load_index: @json($opt->product->load_index ?? ''),
        speed_rating: @json($opt->product->speed_rating ?? ''),
        quantity: {{ $opt->quantity }},
        unit_cost: {{ (float) $opt->unit_cost }},
        unit_price: {{ (float) $opt->unit_price }},
        markup_pct: {{ (float) $opt->markup_pct }},
        discount_pct: {{ (float) $opt->discount_pct }},
        line_total: {{ (float) $opt->line_total }},
        is_selected: {{ $opt->is_selected ? 'true' : 'false' }},
    });
    @endforeach

    // Load existing services
    @foreach($quote->quoteServices as $qs)
    services.push({
        service_id: {{ $qs->service_id }},
        name: @json($qs->service->name ?? 'N/A'),
        code: @json($qs->service->code ?? ''),
        unit_price: {{ (float) $qs->unit_price }},
        quantity: {{ $qs->quantity }},
        per_tyre: {{ ($qs->service && $qs->service->price_per_tyre) ? 'true' : 'false' }},
        line_total: {{ (float) $qs->line_total }},
    });
    @endforeach

    // ==================================================================
    // CUSTOMER AUTOCOMPLETE
    // ==================================================================
    const customerSearch = document.getElementById('customerSearch');
    const customerResults = document.getElementById('customerResults');
    const customerIdInput = document.getElementById('customer_id');
    const selectedCustomerEl = document.getElementById('selectedCustomer');
    const selectedCustomerName = document.getElementById('selectedCustomerName');
    const customerSearchWrapper = document.getElementById('customerSearchWrapper');

    customerSearch.addEventListener('input', function() {
        const term = this.value.trim();
        if (term.length < 2) { customerResults.style.display = 'none'; return; }
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetch(ROUTES.searchCustomers + '?term=' + encodeURIComponent(term), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                customerResults.innerHTML = '';
                if (data.customers && data.customers.length) {
                    data.customers.forEach(c => {
                        const div = document.createElement('div');
                        div.className = 'ac-item';
                        div.innerHTML = '<div class="ac-primary">' + escHtml(c.display_name || c.full_name) + '</div>' +
                                        '<div class="ac-secondary">' + escHtml(c.phone || c.cell || '') + ' ' + escHtml(c.email || '') + '</div>';
                        div.addEventListener('click', () => selectCustomer(c));
                        customerResults.appendChild(div);
                    });
                    customerResults.style.display = 'block';
                } else {
                    customerResults.style.display = 'none';
                }
            })
            .catch(() => { customerResults.style.display = 'none'; });
        }, 300);
    });

    function selectCustomer(c) {
        customerIdInput.value = c.id;
        selectedCustomerName.textContent = c.display_name || c.full_name;
        selectedCustomerEl.classList.add('show');
        customerSearchWrapper.style.display = 'none';
        customerResults.style.display = 'none';
        customerSearch.value = '';
    }

    window.clearCustomer = function() {
        customerIdInput.value = '';
        selectedCustomerEl.classList.remove('show');
        customerSearchWrapper.style.display = 'block';
        customerSearch.value = '';
    };

    // ==================================================================
    // VEHICLE AUTOCOMPLETE
    // ==================================================================
    const vehicleSearch = document.getElementById('vehicleSearch');
    const vehicleResults = document.getElementById('vehicleResults');
    const vehicleIdInput = document.getElementById('vehicle_id');
    const selectedVehicleEl = document.getElementById('selectedVehicle');
    const selectedVehicleName = document.getElementById('selectedVehicleName');
    const vehicleSearchWrapper = document.getElementById('vehicleSearchWrapper');

    vehicleSearch.addEventListener('input', function() {
        const term = this.value.trim();
        if (term.length < 2) { vehicleResults.style.display = 'none'; return; }
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetch(ROUTES.searchVehicles + '?term=' + encodeURIComponent(term), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                vehicleResults.innerHTML = '';
                if (data.vehicles && data.vehicles.length) {
                    data.vehicles.forEach(v => {
                        const div = document.createElement('div');
                        div.className = 'ac-item';
                        div.innerHTML = '<div class="ac-primary">' + escHtml(v.display_name || v.registration) + '</div>' +
                                        '<div class="ac-secondary">' + escHtml(v.make || '') + ' ' + escHtml(v.model || '') + '</div>';
                        div.addEventListener('click', () => selectVehicle(v));
                        vehicleResults.appendChild(div);
                    });
                    vehicleResults.style.display = 'block';
                } else {
                    vehicleResults.style.display = 'none';
                }
            })
            .catch(() => { vehicleResults.style.display = 'none'; });
        }, 300);
    });

    function selectVehicle(v) {
        vehicleIdInput.value = v.id;
        selectedVehicleName.textContent = v.display_name || v.registration;
        selectedVehicleEl.classList.add('show');
        vehicleSearchWrapper.style.display = 'none';
        vehicleResults.style.display = 'none';
        vehicleSearch.value = '';
        if (v.current_tyre_size) {
            document.getElementById('sizeSearchInput').value = v.current_tyre_size;
        }
    }

    window.clearVehicle = function() {
        vehicleIdInput.value = '';
        selectedVehicleEl.classList.remove('show');
        vehicleSearchWrapper.style.display = 'block';
        vehicleSearch.value = '';
    };

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#customerSearchWrapper')) customerResults.style.display = 'none';
        if (!e.target.closest('#vehicleSearchWrapper')) vehicleResults.style.display = 'none';
    });

    window.toggleQuickAdd = function(type) {
        document.getElementById(type + 'QuickAdd').classList.toggle('show');
    };

    // ==================================================================
    // SIZE SEARCH
    // ==================================================================
    const sizeSearchInput = document.getElementById('sizeSearchInput');
    const sizeSearchBtn = document.getElementById('sizeSearchBtn');
    const sizeResultsContainer = document.getElementById('sizeResultsContainer');
    const sizeResultsBody = document.getElementById('sizeResultsBody');
    const sizeResultsCount = document.getElementById('sizeResultsCount');
    const sizeNoResults = document.getElementById('sizeNoResults');

    sizeSearchBtn.addEventListener('click', performSizeSearch);
    sizeSearchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); performSizeSearch(); }
    });

    function performSizeSearch() {
        const size = sizeSearchInput.value.trim();
        if (!size) return;

        sizeSearchBtn.disabled = true;
        sizeSearchBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Searching...';

        fetch(ROUTES.sizeSearch + '?size=' + encodeURIComponent(size), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            sizeResultsBody.innerHTML = '';
            sizeResultsContainer.style.display = 'block';

            if (data.products && data.products.length) {
                sizeNoResults.style.display = 'none';
                sizeResultsCount.textContent = data.products.length + ' found';
                data.products.forEach(p => {
                    const tr = document.createElement('tr');
                    const brandLogoHtml = p.brand_logo ? '<span class="brand-logo-box me-1"><img src="/public/modules/cimstyredash/brands/' + escHtml(p.brand_logo) + '" alt="" onerror="this.parentElement.style.display=\'none\'"></span>' : '';
                    tr.innerHTML = '<td><div class="d-flex align-items-center gap-1">' + brandLogoHtml + escHtml(p.brand) + '</div></td>' +
                        '<td>' + escHtml(p.model_name || '-') + '</td>' +
                        '<td>' + escHtml(p.size) + '</td>' +
                        '<td class="text-end">' + formatMoney(p.cost_price) + '</td>' +
                        '<td class="text-end">' + formatMoney(p.sell_price) + '</td>' +
                        '<td class="text-center">' + (p.total_stock || 0) + '</td>' +
                        '<td class="text-center"><button type="button" class="btn btn-sm btn-success btn-add-option" data-product=\'' + escAttr(JSON.stringify(p)) + '\'><i class="fas fa-plus"></i> Add</button></td>';
                    sizeResultsBody.appendChild(tr);
                });
                sizeResultsBody.querySelectorAll('.btn-add-option').forEach(btn => {
                    btn.addEventListener('click', function() {
                        addOption(JSON.parse(this.getAttribute('data-product')));
                    });
                });
            } else {
                sizeNoResults.style.display = 'block';
                sizeResultsCount.textContent = '0 found';
            }
        })
        .catch(() => {
            sizeResultsContainer.style.display = 'block';
            sizeNoResults.style.display = 'block';
            sizeResultsCount.textContent = 'Error';
        })
        .finally(() => {
            sizeSearchBtn.disabled = false;
            sizeSearchBtn.innerHTML = '<i class="fas fa-search me-1"></i> Search';
        });
    }

    // ==================================================================
    // OPTION MANAGEMENT
    // ==================================================================
    function addOption(product) {
        if (options.length >= MAX_OPTIONS) {
            alert('Maximum of ' + MAX_OPTIONS + ' options allowed per quote.');
            return;
        }
        const sellPrice = parseFloat(product.sell_price) || 0;
        const costPrice = parseFloat(product.cost_price) || 0;
        const markup = parseFloat(product.markup_pct) || DEFAULT_MARKUP;
        options.push({
            product_id: product.id,
            brand: product.brand,
            brand_logo: product.brand_logo || '',
            model_name: product.model_name || '',
            size: product.size,
            load_index: product.load_index || '',
            speed_rating: product.speed_rating || '',
            quantity: 4,
            unit_cost: costPrice,
            unit_price: sellPrice,
            markup_pct: markup,
            discount_pct: 0,
            line_total: 4 * sellPrice,
            is_selected: (options.length === 0),
        });
        renderOptionTabs();
        switchToOptionTab(options.length - 1);
        recalculateTotals();
    }

    function renderOptionTabs() {
        const tabsEl = document.getElementById('quoteOptionTabs');
        const contentEl = document.getElementById('quoteOptionTabContent');
        const noMsg = document.getElementById('noOptionsMsg');

        tabsEl.innerHTML = '';
        contentEl.querySelectorAll('.tab-pane').forEach(p => p.remove());

        if (options.length === 0) {
            noMsg.style.display = 'block';
            updateOptionHiddenInputs();
            return;
        }
        noMsg.style.display = 'none';

        options.forEach((opt, idx) => {
            const li = document.createElement('li');
            li.className = 'nav-item';
            li.innerHTML = '<button class="nav-link" id="opt-tab-' + idx + '" data-bs-toggle="tab" data-bs-target="#opt-pane-' + idx + '" type="button" role="tab">' +
                'Option ' + (idx + 1) +
                (opt.is_selected ? ' <span class="badge bg-success">Selected</span>' : '') +
                '</button>';
            tabsEl.appendChild(li);

            const pane = document.createElement('div');
            pane.className = 'tab-pane fade';
            pane.id = 'opt-pane-' + idx;
            pane.innerHTML = buildOptionPane(opt, idx);
            contentEl.appendChild(pane);
        });

        options.forEach((opt, idx) => bindOptionEvents(idx));
        updateOptionHiddenInputs();
    }

    function buildOptionPane(opt, idx) {
        return '<div class="option-details">' +
            '<div class="d-flex justify-content-between align-items-start mb-3">' +
                '<div>' + (opt.brand_logo ? '<span class="brand-logo-box me-2"><img src="/public/modules/cimstyredash/brands/' + escHtml(opt.brand_logo) + '" alt="" onerror="this.parentElement.style.display=\'none\'"></span>' : '') + '<strong>' + escHtml(opt.brand) + '</strong> ' + escHtml(opt.model_name) + ' (' + escHtml(opt.size) + ')' +
                    (opt.load_index ? ' | ' + escHtml(opt.load_index) + '/' + escHtml(opt.speed_rating) : '') + '</div>' +
                '<div>' +
                    (!opt.is_selected ? '<button type="button" class="btn btn-sm btn-outline-success me-1 btn-select-option" data-idx="' + idx + '"><i class="fas fa-check"></i> Select</button>' : '<span class="badge bg-success">Selected</span> ') +
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-option" data-idx="' + idx + '"><i class="fas fa-trash"></i> Remove</button>' +
                '</div>' +
            '</div>' +
            '<div class="row g-2">' +
                '<div class="col-md-2"><label class="form-label">Qty</label><input type="number" class="form-control form-control-sm opt-qty" data-idx="' + idx + '" value="' + opt.quantity + '" min="1" max="20"></div>' +
                '<div class="col-md-2"><label class="form-label">Unit Cost</label><input type="number" class="form-control form-control-sm opt-cost" data-idx="' + idx + '" value="' + opt.unit_cost.toFixed(2) + '" step="0.01" readonly></div>' +
                '<div class="col-md-2"><label class="form-label">Unit Price</label><input type="number" class="form-control form-control-sm opt-price" data-idx="' + idx + '" value="' + opt.unit_price.toFixed(2) + '" step="0.01" min="0"></div>' +
                '<div class="col-md-2"><label class="form-label">Markup %</label><input type="number" class="form-control form-control-sm opt-markup" data-idx="' + idx + '" value="' + opt.markup_pct.toFixed(1) + '" step="0.1" min="0" max="999"></div>' +
                '<div class="col-md-2"><label class="form-label">Discount %</label><input type="number" class="form-control form-control-sm opt-discount" data-idx="' + idx + '" value="' + opt.discount_pct.toFixed(1) + '" step="0.1" min="0" max="100"></div>' +
                '<div class="col-md-2"><label class="form-label">Line Total</label><input type="text" class="form-control form-control-sm fw-bold opt-line-total" data-idx="' + idx + '" value="' + formatMoney(opt.line_total) + '" readonly></div>' +
            '</div></div>';
    }

    function bindOptionEvents(idx) {
        const pane = document.getElementById('opt-pane-' + idx);
        if (!pane) return;

        const qtyInput = pane.querySelector('.opt-qty');
        const priceInput = pane.querySelector('.opt-price');
        const markupInput = pane.querySelector('.opt-markup');
        const discountInput = pane.querySelector('.opt-discount');

        if (qtyInput) qtyInput.addEventListener('input', () => updateOptionCalc(idx));
        if (priceInput) priceInput.addEventListener('input', () => updateOptionCalc(idx));
        if (markupInput) markupInput.addEventListener('input', () => {
            const cost = options[idx].unit_cost;
            const markup = parseFloat(markupInput.value) || 0;
            priceInput.value = (cost * (1 + markup / 100)).toFixed(2);
            options[idx].markup_pct = markup;
            updateOptionCalc(idx);
        });
        if (discountInput) discountInput.addEventListener('input', () => updateOptionCalc(idx));

        const selectBtn = pane.querySelector('.btn-select-option');
        if (selectBtn) selectBtn.addEventListener('click', () => {
            options.forEach((o, i) => o.is_selected = (i === idx));
            renderOptionTabs();
            switchToOptionTab(idx);
            recalculateTotals();
        });

        const removeBtn = pane.querySelector('.btn-remove-option');
        if (removeBtn) removeBtn.addEventListener('click', () => {
            options.splice(idx, 1);
            if (options.length > 0 && !options.some(o => o.is_selected)) options[0].is_selected = true;
            renderOptionTabs();
            if (options.length > 0) switchToOptionTab(0);
            recalculateTotals();
        });
    }

    function updateOptionCalc(idx) {
        const pane = document.getElementById('opt-pane-' + idx);
        if (!pane) return;
        const qty = parseInt(pane.querySelector('.opt-qty').value) || 1;
        const price = parseFloat(pane.querySelector('.opt-price').value) || 0;
        const discount = parseFloat(pane.querySelector('.opt-discount').value) || 0;
        const lineTotal = qty * price * (1 - discount / 100);
        options[idx].quantity = qty;
        options[idx].unit_price = price;
        options[idx].discount_pct = discount;
        options[idx].line_total = Math.round(lineTotal * 100) / 100;
        pane.querySelector('.opt-line-total').value = formatMoney(options[idx].line_total);
        updateOptionHiddenInputs();
        recalculateTotals();
        updateServiceQuantities();
    }

    function switchToOptionTab(idx) {
        const tabBtn = document.getElementById('opt-tab-' + idx);
        if (tabBtn) { const tab = new bootstrap.Tab(tabBtn); tab.show(); }
    }

    function updateOptionHiddenInputs() {
        const container = document.getElementById('optionHiddenInputs');
        container.innerHTML = '';
        options.forEach((opt, idx) => {
            container.innerHTML +=
                '<input type="hidden" name="options[' + idx + '][product_id]" value="' + opt.product_id + '">' +
                '<input type="hidden" name="options[' + idx + '][quantity]" value="' + opt.quantity + '">' +
                '<input type="hidden" name="options[' + idx + '][unit_price]" value="' + opt.unit_price.toFixed(2) + '">' +
                '<input type="hidden" name="options[' + idx + '][unit_cost]" value="' + opt.unit_cost.toFixed(2) + '">' +
                '<input type="hidden" name="options[' + idx + '][markup_pct]" value="' + opt.markup_pct.toFixed(1) + '">' +
                '<input type="hidden" name="options[' + idx + '][discount_pct]" value="' + opt.discount_pct.toFixed(1) + '">' +
                '<input type="hidden" name="options[' + idx + '][is_selected]" value="' + (opt.is_selected ? '1' : '0') + '">';
        });
    }

    // ==================================================================
    // SERVICE MANAGEMENT
    // ==================================================================
    const addServiceBtn = document.getElementById('addServiceBtn');
    const serviceSelect = document.getElementById('serviceSelect');
    const servicesContainer = document.getElementById('servicesContainer');
    const noServicesMsg = document.getElementById('noServicesMsg');

    addServiceBtn.addEventListener('click', function() {
        const sel = serviceSelect;
        if (!sel.value) return;
        const opt = sel.options[sel.selectedIndex];
        const serviceId = parseInt(sel.value);
        if (services.some(s => s.service_id === serviceId)) { alert('This service has already been added.'); return; }
        const price = parseFloat(opt.dataset.price) || 0;
        const perTyre = opt.dataset.perTyre === '1';
        const qty = perTyre ? getSelectedOptionQty() : 1;
        services.push({
            service_id: serviceId, name: opt.dataset.name, code: opt.dataset.code,
            unit_price: price, quantity: qty, per_tyre: perTyre,
            line_total: Math.round(qty * price * 100) / 100,
        });
        renderServices();
        recalculateTotals();
        sel.value = '';
    });

    function renderServices() {
        servicesContainer.innerHTML = '';
        if (services.length === 0) { noServicesMsg.style.display = 'block'; updateServiceHiddenInputs(); return; }
        noServicesMsg.style.display = 'none';

        services.forEach((svc, idx) => {
            const row = document.createElement('div');
            row.className = 'service-row d-flex align-items-center gap-3';
            row.innerHTML = '<div class="flex-grow-1"><strong>' + escHtml(svc.name) + '</strong> <small class="text-muted">(' + escHtml(svc.code) + ')</small></div>' +
                '<div style="width:80px;"><label class="form-label mb-0" style="font-size:0.75em;">Qty</label><input type="number" class="form-control form-control-sm svc-qty" data-idx="' + idx + '" value="' + svc.quantity + '" min="1" max="100"></div>' +
                '<div style="width:120px;"><label class="form-label mb-0" style="font-size:0.75em;">Unit Price</label><input type="number" class="form-control form-control-sm svc-price" data-idx="' + idx + '" value="' + svc.unit_price.toFixed(2) + '" step="0.01" min="0"></div>' +
                '<div style="width:100px;"><label class="form-label mb-0" style="font-size:0.75em;">Total</label><input type="text" class="form-control form-control-sm fw-bold svc-total" value="' + formatMoney(svc.line_total) + '" readonly></div>' +
                '<div><button type="button" class="btn btn-sm btn-outline-danger btn-remove-svc" data-idx="' + idx + '"><i class="fas fa-times"></i></button></div>';
            servicesContainer.appendChild(row);
        });

        servicesContainer.querySelectorAll('.svc-qty, .svc-price').forEach(input => {
            input.addEventListener('input', function() {
                const idx = parseInt(this.dataset.idx);
                const row = this.closest('.service-row');
                const qty = parseInt(row.querySelector('.svc-qty').value) || 1;
                const price = parseFloat(row.querySelector('.svc-price').value) || 0;
                services[idx].quantity = qty;
                services[idx].unit_price = price;
                services[idx].line_total = Math.round(qty * price * 100) / 100;
                row.querySelector('.svc-total').value = formatMoney(services[idx].line_total);
                updateServiceHiddenInputs();
                recalculateTotals();
            });
        });

        servicesContainer.querySelectorAll('.btn-remove-svc').forEach(btn => {
            btn.addEventListener('click', function() {
                services.splice(parseInt(this.dataset.idx), 1);
                renderServices();
                recalculateTotals();
            });
        });

        updateServiceHiddenInputs();
    }

    function updateServiceHiddenInputs() {
        const container = document.getElementById('serviceHiddenInputs');
        container.innerHTML = '';
        services.forEach((svc, idx) => {
            container.innerHTML +=
                '<input type="hidden" name="services[' + idx + '][service_id]" value="' + svc.service_id + '">' +
                '<input type="hidden" name="services[' + idx + '][quantity]" value="' + svc.quantity + '">' +
                '<input type="hidden" name="services[' + idx + '][unit_price]" value="' + svc.unit_price.toFixed(2) + '">';
        });
    }

    function getSelectedOptionQty() {
        const selected = options.find(o => o.is_selected);
        return selected ? selected.quantity : 4;
    }

    function updateServiceQuantities() {
        const tyreQty = getSelectedOptionQty();
        services.forEach(svc => {
            if (svc.per_tyre) {
                svc.quantity = tyreQty;
                svc.line_total = Math.round(tyreQty * svc.unit_price * 100) / 100;
            }
        });
        renderServices();
    }

    // ==================================================================
    // TOTALS
    // ==================================================================
    function recalculateTotals() {
        const selectedOpt = options.find(o => o.is_selected);
        const optionTotal = selectedOpt ? selectedOpt.line_total : 0;
        const servicesTotal = services.reduce((sum, s) => sum + s.line_total, 0);
        const grandTotal = optionTotal + servicesTotal;
        const vatDivisor = 1 + (VAT_RATE / 100);
        const subtotalExcl = grandTotal / vatDivisor;
        const vatAmount = grandTotal - subtotalExcl;

        document.getElementById('summaryOptionTotal').textContent = formatMoney(optionTotal);
        document.getElementById('summaryServicesTotal').textContent = formatMoney(servicesTotal);
        document.getElementById('summarySubtotal').textContent = formatMoney(subtotalExcl);
        document.getElementById('summaryVat').textContent = formatMoney(vatAmount);
        document.getElementById('summaryGrandTotal').textContent = formatMoney(grandTotal);
    }

    // ==================================================================
    // FORM SUBMISSION
    // ==================================================================
    window.submitQuote = function(action) {
        document.getElementById('formAction').value = action;
        const quoteDate = document.querySelector('[name="quote_date"]').value;
        if (!quoteDate) { alert('Please select a quote date.'); return; }
        updateOptionHiddenInputs();
        updateServiceHiddenInputs();
        document.getElementById('quoteForm').submit();
    };

    // ==================================================================
    // UTILITIES
    // ==================================================================
    function formatMoney(amount) {
        return 'R' + parseFloat(amount || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }
    function escHtml(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }
    function escAttr(str) {
        return (str || '').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
    }

    // Initialize - render pre-populated data
    renderOptionTabs();
    if (options.length > 0) switchToOptionTab(0);
    renderServices();
    recalculateTotals();

})();
</script>
@endpush
