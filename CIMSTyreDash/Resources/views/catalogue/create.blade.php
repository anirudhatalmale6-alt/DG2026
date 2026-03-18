@extends('layouts.default')

@section('title', 'Add Product')

@push('styles')
<style>
    .form-label {
        font-weight: 600;
    }
    .required::after {
        content: ' *';
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Add Product</h4>
                <p class="mb-0">Create a new product in the catalogue</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.catalogue.index') }}">Catalogue</a></li>
                <li class="breadcrumb-item active">Add Product</li>
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

    <div class="row">
        {{-- Sidebar --}}
        <div class="col-xl-3 col-xxl-4 col-lg-4">
            @include('cimstyredash::partials.sidebar', ['activePage' => 'catalogue'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-xxl-8 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-plus-circle me-2"></i>New Product
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('cimstyredash.catalogue.store') }}" method="POST">
                        @csrf

                        {{-- Classification --}}
                        <h5 class="mb-3 text-primary"><i class="fas fa-tags me-2"></i>Classification</h5>
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <label for="brand_id" class="form-label required">Brand</label>
                                <select name="brand_id" id="brand_id" class="form-control form-select @error('brand_id') is-invalid @enderror">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="category_id" class="form-label required">Category</label>
                                <select name="category_id" id="category_id" class="form-control form-select @error('category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="size_id" class="form-label required">Size</label>
                                <select name="size_id" id="size_id" class="form-control form-select @error('size_id') is-invalid @enderror">
                                    <option value="">Select Size</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size->id }}" {{ old('size_id') == $size->id ? 'selected' : '' }}>
                                            {{ $size->full_size }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('size_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Product Details --}}
                        <h5 class="mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>Product Details</h5>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label for="model_name" class="form-label required">Model Name</label>
                                <input type="text" name="model_name" id="model_name" class="form-control @error('model_name') is-invalid @enderror" value="{{ old('model_name') }}" placeholder="e.g. Pilot Sport 5">
                                @error('model_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="product_code" class="form-label required">Product Code</label>
                                <input type="text" name="product_code" id="product_code" class="form-control @error('product_code') is-invalid @enderror" value="{{ old('product_code') }}" placeholder="e.g. MICH-PS5-2554018">
                                @error('product_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="full_description" class="form-label">Full Description</label>
                                <textarea name="full_description" id="full_description" class="form-control @error('full_description') is-invalid @enderror" rows="3" placeholder="Detailed product description...">{{ old('full_description') }}</textarea>
                                @error('full_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Specifications --}}
                        <h5 class="mb-3 text-primary"><i class="fas fa-cog me-2"></i>Specifications</h5>
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <label for="load_index" class="form-label">Load Index</label>
                                <input type="text" name="load_index" id="load_index" class="form-control @error('load_index') is-invalid @enderror" value="{{ old('load_index') }}" placeholder="e.g. 98">
                                @error('load_index')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="speed_rating" class="form-label">Speed Rating</label>
                                <input type="text" name="speed_rating" id="speed_rating" class="form-control @error('speed_rating') is-invalid @enderror" value="{{ old('speed_rating') }}" placeholder="e.g. Y">
                                @error('speed_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="pattern_type" class="form-label">Pattern Type</label>
                                <input type="text" name="pattern_type" id="pattern_type" class="form-control @error('pattern_type') is-invalid @enderror" value="{{ old('pattern_type') }}" placeholder="e.g. Asymmetric">
                                @error('pattern_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Pricing --}}
                        <h5 class="mb-3 text-primary"><i class="fas fa-dollar-sign me-2"></i>Pricing</h5>
                        <div class="row mb-4">
                            <div class="col-md-4 mb-3">
                                <label for="cost_price" class="form-label required">Cost Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">R</span>
                                    <input type="number" name="cost_price" id="cost_price" class="form-control @error('cost_price') is-invalid @enderror" value="{{ old('cost_price') }}" step="0.01" min="0" placeholder="0.00">
                                    @error('cost_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="sell_price" class="form-label required">Sell Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">R</span>
                                    <input type="number" name="sell_price" id="sell_price" class="form-control @error('sell_price') is-invalid @enderror" value="{{ old('sell_price') }}" step="0.01" min="0" placeholder="0.00">
                                    @error('sell_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="markup_pct" class="form-label">Markup %</label>
                                <div class="input-group">
                                    <input type="number" name="markup_pct" id="markup_pct" class="form-control @error('markup_pct') is-invalid @enderror" value="{{ old('markup_pct') }}" step="0.1" min="0" placeholder="0.0">
                                    <span class="input-group-text">%</span>
                                    @error('markup_pct')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="is_active" value="0">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        <i class="fas fa-toggle-on me-1"></i> Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('cimstyredash.catalogue.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Catalogue
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
