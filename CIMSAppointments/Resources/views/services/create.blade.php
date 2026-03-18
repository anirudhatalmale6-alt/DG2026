@extends('layouts.default')

@section('title', 'Add Service')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.dashboard') }}">Appointments</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cimsappointments.services.index') }}">Services</a></li>
            <li class="breadcrumb-item active">Add</li>
        </ol>
    </div>

    <div class="row">
        <div class="col-xl-3">
            @include('cims_appointments::partials.sidebar', ['activePage' => 'services', 'counts' => ['pending' => 0]])
        </div>
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header"><h4 class="card-title mb-0"><i class="fas fa-plus me-2"></i>Add New Service</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('cimsappointments.services.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Service Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Color</label>
                                <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', '#17A2B8') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label fw-bold">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Default Duration</label>
                                <select name="default_duration_minutes" class="form-select">
                                    @for($m = 60; $m <= 480; $m += 60)
                                        <option value="{{ $m }}" {{ old('default_duration_minutes', 60) == $m ? 'selected' : '' }}>{{ $m/60 }} Hour{{ $m > 60 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Min Duration</label>
                                <select name="min_duration_minutes" class="form-select">
                                    @for($m = 60; $m <= 480; $m += 60)
                                        <option value="{{ $m }}" {{ old('min_duration_minutes', 60) == $m ? 'selected' : '' }}>{{ $m/60 }} Hour{{ $m > 60 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Max Duration</label>
                                <select name="max_duration_minutes" class="form-select">
                                    @for($m = 60; $m <= 480; $m += 60)
                                        <option value="{{ $m }}" {{ old('max_duration_minutes', 240) == $m ? 'selected' : '' }}>{{ $m/60 }} Hour{{ $m > 60 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_chargeable" value="1" id="isChargeable" {{ old('is_chargeable') ? 'checked' : '' }} onchange="document.getElementById('priceField').style.display = this.checked ? 'block' : 'none'">
                                    <label class="form-check-label fw-bold" for="isChargeable">Chargeable Service</label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3" id="priceField" style="{{ old('is_chargeable') ? '' : 'display:none;' }}">
                                <label class="form-label fw-bold">Price Per Hour (R)</label>
                                <input type="number" name="price_per_hour" class="form-control" value="{{ old('price_per_hour', 0) }}" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary me-2"><i class="fas fa-save me-1"></i>Create Service</button>
                            <a href="{{ route('cimsappointments.services.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
