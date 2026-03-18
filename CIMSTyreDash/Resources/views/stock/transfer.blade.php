@extends('layouts.default')

@section('title', 'Stock Transfer')

@push('styles')
<style>
    .transfer-form .form-group label {
        font-weight: 500;
        color: #5a5c69;
        margin-bottom: 0.35rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Stock Transfer</h4>
                <p class="mb-0">Transfer stock between branches</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.stock.index') }}">Stock</a></li>
                <li class="breadcrumb-item active">Transfer</li>
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

            <div class="card mb-4">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-exchange-alt me-2"></i>Transfer Stock
                    </h4>
                </div>
                <div class="card-body transfer-form">
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

                    <form action="{{ route('cimstyredash.stock.process-transfer') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <label for="product_id" class="col-sm-3 col-form-label fw-bold">Product</label>
                            <div class="col-sm-9">
                                <select class="form-control form-select @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                                    <option value="">-- Select Product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->brand->name ?? '' }} {{ $product->model_name }} - {{ $product->size->full_size ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="from_branch_id" class="col-sm-3 col-form-label fw-bold">From Branch</label>
                            <div class="col-sm-9">
                                <select class="form-control form-select @error('from_branch_id') is-invalid @enderror" id="from_branch_id" name="from_branch_id" required>
                                    <option value="">-- Select Source Branch --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('from_branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="to_branch_id" class="col-sm-3 col-form-label fw-bold">To Branch</label>
                            <div class="col-sm-9">
                                <select class="form-control form-select @error('to_branch_id') is-invalid @enderror" id="to_branch_id" name="to_branch_id" required>
                                    <option value="">-- Select Destination Branch --</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('to_branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="quantity" class="col-sm-3 col-form-label fw-bold">Quantity</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="reason" class="col-sm-3 col-form-label fw-bold">Reason</label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" placeholder="Enter reason for transfer...">{{ old('reason') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-exchange-alt me-1"></i>Process Transfer
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
