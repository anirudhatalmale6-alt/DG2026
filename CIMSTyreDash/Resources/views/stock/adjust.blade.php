@extends('layouts.default')

@section('title', 'Adjust Stock')

@push('styles')
<style>
    .stock-info dt {
        font-weight: 600;
        color: #5a5c69;
    }
    .stock-info dd {
        margin-bottom: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Adjust Stock</h4>
                <p class="mb-0">Modify stock quantity for a product</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.stock.index') }}">Stock</a></li>
                <li class="breadcrumb-item active">Adjust</li>
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

    {{-- 2-Column Layout --}}
    <div class="row">

        {{-- Sidebar --}}
        <div class="col-xl-3 col-lg-4">
            @include('cimstyredash::partials.sidebar', ['activePage' => 'stock'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-lg-8">

            {{-- Current Stock Info --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Current Stock Information
                    </h4>
                </div>
                <div class="card-body">
                    <dl class="row stock-info mb-0">
                        <dt class="col-sm-3">Product</dt>
                        <dd class="col-sm-9">{{ $stock->product->product_code ?? '-' }} &mdash; {{ $stock->product->model_name ?? '-' }}</dd>

                        <dt class="col-sm-3">Brand</dt>
                        <dd class="col-sm-9">
                            <div class="d-flex align-items-center gap-2">
                                @if($stock->product && $stock->product->brand && $stock->product->brand->logo_url)
                                    <span class="brand-logo-box"><img src="{{ asset('modules/cimstyredash/brands/' . $stock->product->brand->logo_url) }}" alt="{{ $stock->product->brand->name }}" onerror="this.parentElement.style.display='none'"></span>
                                @endif
                                {{ $stock->product->brand->name ?? '-' }}
                            </div>
                        </dd>

                        <dt class="col-sm-3">Size</dt>
                        <dd class="col-sm-9">{{ $stock->product->size->full_size ?? '-' }}</dd>

                        <dt class="col-sm-3">Branch</dt>
                        <dd class="col-sm-9">{{ $stock->branch->name ?? '-' }}</dd>

                        <dt class="col-sm-3">Current Quantity</dt>
                        <dd class="col-sm-9"><strong>{{ $stock->quantity }}</strong></dd>

                        <dt class="col-sm-3">Min Quantity</dt>
                        <dd class="col-sm-9">{{ $stock->min_quantity }}</dd>

                        <dt class="col-sm-3">Reserved</dt>
                        <dd class="col-sm-9">{{ $stock->reserved_quantity }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Adjustment Form --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-sliders-h me-2"></i>Stock Adjustment
                    </h4>
                </div>
                <div class="card-body">
                    {{-- Validation Errors --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cimstyredash.stock.process-adjustment', $stock->id) }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label fw-bold">Adjustment Type</label>
                            <div class="col-sm-9">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="adjustment_type" id="type_add" value="add" {{ old('adjustment_type', 'add') == 'add' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_add">
                                        <i class="fas fa-plus text-success me-1"></i>Add
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="adjustment_type" id="type_subtract" value="subtract" {{ old('adjustment_type') == 'subtract' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_subtract">
                                        <i class="fas fa-minus text-danger me-1"></i>Subtract
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="adjustment_type" id="type_set" value="set" {{ old('adjustment_type') == 'set' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="type_set">
                                        <i class="fas fa-equals text-info me-1"></i>Set To
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="quantity" class="col-sm-3 col-form-label fw-bold">Quantity</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" min="0" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="min_quantity" class="col-sm-3 col-form-label fw-bold">Min Quantity</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control @error('min_quantity') is-invalid @enderror" id="min_quantity" name="min_quantity" value="{{ old('min_quantity', $stock->min_quantity) }}" min="0">
                                @error('min_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="reason" class="col-sm-3 col-form-label fw-bold">Reason</label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" placeholder="Enter reason for adjustment...">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Process Adjustment
                                </button>
                                <a href="{{ route('cimstyredash.stock.index') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Stock
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection
