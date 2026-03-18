@extends('layouts.default')

@section('title', 'Edit Job Card - ' . $jobCard->job_card_number)

@push('styles')
<style>
    .autocomplete-results {
        position: absolute;
        z-index: 1050;
        background: #fff;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        max-height: 250px;
        overflow-y: auto;
        width: 100%;
        box-shadow: 0 4px 6px rgba(0,0,0,.1);
    }
    .autocomplete-results .autocomplete-item {
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }
    .autocomplete-results .autocomplete-item:hover,
    .autocomplete-results .autocomplete-item.active {
        background-color: #e9ecef;
    }
    .autocomplete-results .autocomplete-item:last-child {
        border-bottom: none;
    }
    .selected-info {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.75rem;
        margin-top: 0.5rem;
    }
    .selected-info .remove-selection {
        cursor: pointer;
        color: #dc3545;
    }
    .line-item-row {
        background-color: #fafbfc;
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    .line-item-row:hover {
        border-color: #adb5bd;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Edit Job Card: {{ $jobCard->job_card_number }}</h4>
                <p class="mb-0">Update job card details</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.jobcards.index') }}">Job Cards</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
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
            <i class="fas fa-exclamation-triangle me-2"></i><strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 2-Column Layout --}}
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-xl-3 col-lg-4">
            @include('cimstyredash::partials.sidebar', ['activePage' => 'jobcards'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-lg-8">

            <form action="{{ route('cimstyredash.jobcards.update', $jobCard->id) }}" method="POST" id="jobCardForm">
                @csrf
                @method('PUT')

                {{-- Header --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Job Card Details
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label" for="job_card_number">Job Card Number</label>
                                <input type="text" class="form-control" id="job_card_number" name="job_card_number" value="{{ $jobCard->job_card_number }}" readonly>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label" for="job_date">Date</label>
                                <input type="date" class="form-control @error('job_date') is-invalid @enderror" id="job_date" name="job_date" value="{{ old('job_date', $jobCard->job_date ? \Carbon\Carbon::parse($jobCard->job_date)->format('Y-m-d') : '') }}" required>
                                @error('job_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label" for="branch_id">Branch</label>
                                <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id', $jobCard->branch_id) == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label class="form-label" for="technician_name">Technician Name</label>
                                <input type="text" class="form-control @error('technician_name') is-invalid @enderror" id="technician_name" name="technician_name" value="{{ old('technician_name', $jobCard->technician_name) }}" placeholder="Enter technician name">
                                @error('technician_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Customer --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Customer
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="customer_search">Search Customer</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="customer_search" placeholder="Type customer name, phone or email..." autocomplete="off" value="{{ $jobCard->customer->full_name ?? '' }}">
                                    <div id="customer_results" class="autocomplete-results" style="display:none;"></div>
                                </div>
                                <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id', $jobCard->customer_id) }}">
                                @error('customer_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Selected Customer</label>
                                <div id="customer_display" class="selected-info">
                                    @if($jobCard->customer)
                                        <strong>{{ $jobCard->customer->full_name }}</strong>
                                        @if($jobCard->customer->phone)
                                            <br><small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $jobCard->customer->phone }}</small>
                                        @endif
                                        @if($jobCard->customer->email)
                                            <br><small class="text-muted"><i class="fas fa-envelope me-1"></i>{{ $jobCard->customer->email }}</small>
                                        @endif
                                        <span class="remove-selection float-end" onclick="clearCustomer()"><i class="fas fa-times-circle"></i></span>
                                    @else
                                        <span class="text-muted">No customer selected</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Vehicle --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-car me-2"></i>Vehicle
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="vehicle_search">Search Vehicle</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="vehicle_search" placeholder="Type registration, make or model..." autocomplete="off" value="{{ $jobCard->vehicle->registration ?? '' }}">
                                    <div id="vehicle_results" class="autocomplete-results" style="display:none;"></div>
                                </div>
                                <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id', $jobCard->vehicle_id) }}">
                                @error('vehicle_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Selected Vehicle</label>
                                <div id="vehicle_display" class="selected-info">
                                    @if($jobCard->vehicle)
                                        <strong>{{ $jobCard->vehicle->registration }}</strong>
                                        <br><small class="text-muted">{{ $jobCard->vehicle->make }} {{ $jobCard->vehicle->model }}</small>
                                        <span class="remove-selection float-end" onclick="clearVehicle()"><i class="fas fa-times-circle"></i></span>
                                    @else
                                        <span class="text-muted">No vehicle selected</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label" for="odometer_in">Odometer In (km)</label>
                                <input type="number" class="form-control @error('odometer_in') is-invalid @enderror" id="odometer_in" name="odometer_in" value="{{ old('odometer_in', $jobCard->odometer_in) }}" min="0" placeholder="e.g. 45000">
                                @error('odometer_in')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="odometer_out">Odometer Out (km)</label>
                                <input type="number" class="form-control @error('odometer_out') is-invalid @enderror" id="odometer_out" name="odometer_out" value="{{ old('odometer_out', $jobCard->odometer_out) }}" min="0" placeholder="e.g. 45020">
                                @error('odometer_out')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Vehicle Condition Notes --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Vehicle Condition Notes
                        </h4>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control @error('vehicle_condition_notes') is-invalid @enderror" name="vehicle_condition_notes" rows="3" placeholder="Describe the condition of the vehicle upon arrival...">{{ old('vehicle_condition_notes', $jobCard->vehicle_condition_notes) }}</textarea>
                        @error('vehicle_condition_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Tyre Lines --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-circle-notch me-2"></i>Tyre Lines
                        </h4>
                        <button type="button" class="btn btn-success btn-sm" id="addTyreLine">
                            <i class="fas fa-plus me-1"></i>Add Tyre
                        </button>
                    </div>
                    <div class="card-body" id="tyreContainer">
                        {{-- Existing tyre lines pre-populated via JS --}}
                    </div>
                    <div class="card-footer text-muted text-center" id="noTyresMsg" style="{{ $jobCard->jobCardTyres->count() > 0 ? 'display:none;' : '' }}">
                        <i class="fas fa-info-circle me-1"></i>No tyre lines added yet. Click "Add Tyre" to begin.
                    </div>
                </div>

                {{-- Service Lines --}}
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-tools me-2"></i>Service Lines
                        </h4>
                        <button type="button" class="btn btn-success btn-sm" id="addServiceLine">
                            <i class="fas fa-plus me-1"></i>Add Service
                        </button>
                    </div>
                    <div class="card-body" id="serviceContainer">
                        {{-- Existing service lines pre-populated via JS --}}
                    </div>
                    <div class="card-footer text-muted text-center" id="noServicesMsg" style="{{ $jobCard->jobCardServices->count() > 0 ? 'display:none;' : '' }}">
                        <i class="fas fa-info-circle me-1"></i>No service lines added yet. Click "Add Service" to begin.
                    </div>
                </div>

                {{-- Work Notes --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-sticky-note me-2"></i>Work Notes
                        </h4>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control @error('work_notes') is-invalid @enderror" name="work_notes" rows="3" placeholder="Additional work notes or instructions...">{{ old('work_notes', $jobCard->work_notes) }}</textarea>
                        @error('work_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="card mb-4">
                    <div class="card-body d-flex justify-content-between">
                        <a href="{{ route('cimstyredash.jobcards.show', $jobCard->id) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Job Card
                        </button>
                    </div>
                </div>

            </form>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // =========================================================================
    // Product & Service data from server
    // =========================================================================
    const products = @json($products);
    const services = @json($services);

    // Existing lines from the job card
    const existingTyres = @json($jobCard->jobCardTyres);
    const existingServices = @json($jobCard->jobCardServices);

    // =========================================================================
    // Customer Autocomplete
    // =========================================================================
    const customerSearch = document.getElementById('customer_search');
    const customerResults = document.getElementById('customer_results');
    const customerIdInput = document.getElementById('customer_id');
    const customerDisplay = document.getElementById('customer_display');
    let customerTimeout = null;

    customerSearch.addEventListener('input', function() {
        clearTimeout(customerTimeout);
        const query = this.value.trim();
        if (query.length < 2) {
            customerResults.style.display = 'none';
            return;
        }
        customerTimeout = setTimeout(function() {
            fetch("{{ route('cimstyredash.ajax.search-customers') }}?q=" + encodeURIComponent(query))
                .then(r => r.json())
                .then(data => {
                    customerResults.innerHTML = '';
                    if (data.length === 0) {
                        customerResults.innerHTML = '<div class="autocomplete-item text-muted">No customers found</div>';
                    } else {
                        data.forEach(function(c) {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-item';
                            div.textContent = c.full_name + (c.phone ? ' - ' + c.phone : '') + (c.email ? ' (' + c.email + ')' : '');
                            div.addEventListener('click', function() {
                                customerIdInput.value = c.id;
                                customerSearch.value = c.full_name;
                                customerDisplay.innerHTML = '<strong>' + escapeHtml(c.full_name) + '</strong>' +
                                    (c.phone ? '<br><small class="text-muted"><i class="fas fa-phone me-1"></i>' + escapeHtml(c.phone) + '</small>' : '') +
                                    (c.email ? '<br><small class="text-muted"><i class="fas fa-envelope me-1"></i>' + escapeHtml(c.email) + '</small>' : '') +
                                    ' <span class="remove-selection float-end" onclick="clearCustomer()"><i class="fas fa-times-circle"></i></span>';
                                customerResults.style.display = 'none';
                            });
                            customerResults.appendChild(div);
                        });
                    }
                    customerResults.style.display = 'block';
                })
                .catch(() => { customerResults.style.display = 'none'; });
        }, 300);
    });

    window.clearCustomer = function() {
        customerIdInput.value = '';
        customerSearch.value = '';
        customerDisplay.innerHTML = '<span class="text-muted">No customer selected</span>';
    };

    // =========================================================================
    // Vehicle Autocomplete
    // =========================================================================
    const vehicleSearch = document.getElementById('vehicle_search');
    const vehicleResults = document.getElementById('vehicle_results');
    const vehicleIdInput = document.getElementById('vehicle_id');
    const vehicleDisplay = document.getElementById('vehicle_display');
    let vehicleTimeout = null;

    vehicleSearch.addEventListener('input', function() {
        clearTimeout(vehicleTimeout);
        const query = this.value.trim();
        if (query.length < 2) {
            vehicleResults.style.display = 'none';
            return;
        }
        vehicleTimeout = setTimeout(function() {
            fetch("{{ route('cimstyredash.ajax.search-vehicles') }}?q=" + encodeURIComponent(query))
                .then(r => r.json())
                .then(data => {
                    vehicleResults.innerHTML = '';
                    if (data.length === 0) {
                        vehicleResults.innerHTML = '<div class="autocomplete-item text-muted">No vehicles found</div>';
                    } else {
                        data.forEach(function(v) {
                            const div = document.createElement('div');
                            div.className = 'autocomplete-item';
                            div.textContent = v.registration + ' - ' + (v.make || '') + ' ' + (v.model || '');
                            div.addEventListener('click', function() {
                                vehicleIdInput.value = v.id;
                                vehicleSearch.value = v.registration;
                                vehicleDisplay.innerHTML = '<strong>' + escapeHtml(v.registration) + '</strong>' +
                                    '<br><small class="text-muted">' + escapeHtml((v.make || '') + ' ' + (v.model || '')) + '</small>' +
                                    ' <span class="remove-selection float-end" onclick="clearVehicle()"><i class="fas fa-times-circle"></i></span>';
                                vehicleResults.style.display = 'none';
                            });
                            vehicleResults.appendChild(div);
                        });
                    }
                    vehicleResults.style.display = 'block';
                })
                .catch(() => { vehicleResults.style.display = 'none'; });
        }, 300);
    });

    window.clearVehicle = function() {
        vehicleIdInput.value = '';
        vehicleSearch.value = '';
        vehicleDisplay.innerHTML = '<span class="text-muted">No vehicle selected</span>';
    };

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!customerSearch.contains(e.target) && !customerResults.contains(e.target)) {
            customerResults.style.display = 'none';
        }
        if (!vehicleSearch.contains(e.target) && !vehicleResults.contains(e.target)) {
            vehicleResults.style.display = 'none';
        }
    });

    // =========================================================================
    // Dynamic Tyre Lines
    // =========================================================================
    let tyreIndex = 0;
    const tyreContainer = document.getElementById('tyreContainer');
    const noTyresMsg = document.getElementById('noTyresMsg');

    function buildProductOptions(selectedId) {
        let html = '<option value="">Select Product</option>';
        products.forEach(function(p) {
            const label = (p.brand ? p.brand.name : '') + ' ' + (p.model || '') + ' - ' + (p.size ? p.size.label : '');
            const sel = (selectedId && p.id == selectedId) ? ' selected' : '';
            html += '<option value="' + p.id + '" data-price="' + (p.selling_price || 0) + '"' + sel + '>' + escapeHtml(label.trim()) + '</option>';
        });
        return html;
    }

    function addTyreRow(data) {
        const idx = tyreIndex++;
        const row = document.createElement('div');
        row.className = 'line-item-row';
        row.id = 'tyreRow_' + idx;

        const productId = data ? data.product_id : '';
        const qty = data ? data.quantity : 1;
        const position = data ? (data.position || '') : '';
        const unitPrice = data ? parseFloat(data.unit_price || 0).toFixed(2) : '0.00';
        const serialNew = data ? (data.serial_new || '') : '';
        const serialOld = data ? (data.serial_old || '') : '';

        const posOptions = ['', 'FL', 'FR', 'RL', 'RR', 'Spare'];
        let posHtml = '';
        posOptions.forEach(function(p) {
            const label = p || 'None';
            const sel = (position === p) ? ' selected' : '';
            posHtml += '<option value="' + p + '"' + sel + '>' + label + '</option>';
        });

        row.innerHTML =
            '<div class="row g-2 align-items-end">' +
                '<div class="col-md-3">' +
                    '<label class="form-label small">Product</label>' +
                    '<select class="form-select form-select-sm tyre-product-select" name="tyres[' + idx + '][product_id]" required>' +
                        buildProductOptions(productId) +
                    '</select>' +
                '</div>' +
                '<div class="col-md-1">' +
                    '<label class="form-label small">Qty</label>' +
                    '<input type="number" class="form-control form-control-sm" name="tyres[' + idx + '][quantity]" value="' + qty + '" min="1" required>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label class="form-label small">Position</label>' +
                    '<select class="form-select form-select-sm" name="tyres[' + idx + '][position]">' +
                        posHtml +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label class="form-label small">Unit Price</label>' +
                    '<input type="number" class="form-control form-control-sm tyre-unit-price" name="tyres[' + idx + '][unit_price]" step="0.01" min="0" value="' + unitPrice + '" required>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label class="form-label small">Serial New</label>' +
                    '<input type="text" class="form-control form-control-sm" name="tyres[' + idx + '][serial_new]" placeholder="Optional" value="' + escapeAttr(serialNew) + '">' +
                '</div>' +
                '<div class="col-md-2 d-flex align-items-end">' +
                    '<div class="flex-grow-1 me-2">' +
                        '<label class="form-label small">Serial Old</label>' +
                        '<input type="text" class="form-control form-control-sm" name="tyres[' + idx + '][serial_old]" placeholder="Optional" value="' + escapeAttr(serialOld) + '">' +
                    '</div>' +
                    '<button type="button" class="btn btn-outline-danger btn-sm remove-tyre-btn" data-row="tyreRow_' + idx + '" title="Remove">' +
                        '<i class="fas fa-trash"></i>' +
                    '</button>' +
                '</div>' +
            '</div>';
        tyreContainer.appendChild(row);
        noTyresMsg.style.display = 'none';

        // Auto-fill price on product select
        row.querySelector('.tyre-product-select').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const price = selected.getAttribute('data-price') || '0';
            row.querySelector('.tyre-unit-price').value = parseFloat(price).toFixed(2);
        });

        // Remove button
        row.querySelector('.remove-tyre-btn').addEventListener('click', function() {
            document.getElementById(this.getAttribute('data-row')).remove();
            if (tyreContainer.children.length === 0) {
                noTyresMsg.style.display = 'block';
            }
        });
    }

    document.getElementById('addTyreLine').addEventListener('click', function() {
        addTyreRow(null);
    });

    // Pre-populate existing tyre lines
    existingTyres.forEach(function(t) {
        addTyreRow(t);
    });

    // =========================================================================
    // Dynamic Service Lines
    // =========================================================================
    let serviceIndex = 0;
    const serviceContainer = document.getElementById('serviceContainer');
    const noServicesMsg = document.getElementById('noServicesMsg');

    function buildServiceOptions(selectedId) {
        let html = '<option value="">Select Service</option>';
        services.forEach(function(s) {
            const label = s.name + (s.code ? ' (' + s.code + ')' : '');
            const sel = (selectedId && s.id == selectedId) ? ' selected' : '';
            html += '<option value="' + s.id + '" data-price="' + (s.price || 0) + '"' + sel + '>' + escapeHtml(label) + '</option>';
        });
        return html;
    }

    function addServiceRow(data) {
        const idx = serviceIndex++;
        const row = document.createElement('div');
        row.className = 'line-item-row';
        row.id = 'serviceRow_' + idx;

        const serviceId = data ? data.service_id : '';
        const qty = data ? data.quantity : 1;
        const unitPrice = data ? parseFloat(data.unit_price || 0).toFixed(2) : '0.00';

        row.innerHTML =
            '<div class="row g-2 align-items-end">' +
                '<div class="col-md-4">' +
                    '<label class="form-label small">Service</label>' +
                    '<select class="form-select form-select-sm service-select" name="services[' + idx + '][service_id]" required>' +
                        buildServiceOptions(serviceId) +
                    '</select>' +
                '</div>' +
                '<div class="col-md-2">' +
                    '<label class="form-label small">Qty</label>' +
                    '<input type="number" class="form-control form-control-sm" name="services[' + idx + '][quantity]" value="' + qty + '" min="1" required>' +
                '</div>' +
                '<div class="col-md-3">' +
                    '<label class="form-label small">Unit Price</label>' +
                    '<input type="number" class="form-control form-control-sm service-unit-price" name="services[' + idx + '][unit_price]" step="0.01" min="0" value="' + unitPrice + '" required>' +
                '</div>' +
                '<div class="col-md-3 d-flex align-items-end">' +
                    '<button type="button" class="btn btn-outline-danger btn-sm remove-service-btn ms-auto" data-row="serviceRow_' + idx + '" title="Remove">' +
                        '<i class="fas fa-trash me-1"></i>Remove' +
                    '</button>' +
                '</div>' +
            '</div>';
        serviceContainer.appendChild(row);
        noServicesMsg.style.display = 'none';

        // Auto-fill price on service select
        row.querySelector('.service-select').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const price = selected.getAttribute('data-price') || '0';
            row.querySelector('.service-unit-price').value = parseFloat(price).toFixed(2);
        });

        // Remove button
        row.querySelector('.remove-service-btn').addEventListener('click', function() {
            document.getElementById(this.getAttribute('data-row')).remove();
            if (serviceContainer.children.length === 0) {
                noServicesMsg.style.display = 'block';
            }
        });
    }

    document.getElementById('addServiceLine').addEventListener('click', function() {
        addServiceRow(null);
    });

    // Pre-populate existing service lines
    existingServices.forEach(function(s) {
        addServiceRow(s);
    });

    // =========================================================================
    // Utility
    // =========================================================================
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text || ''));
        return div.innerHTML;
    }

    function escapeAttr(text) {
        return (text || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

});
</script>
@endpush
