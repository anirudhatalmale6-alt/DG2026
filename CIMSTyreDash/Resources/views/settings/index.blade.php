@extends('layouts.default')

@section('title', 'TyreDash Settings')

@push('styles')
<style>
    .settings-card .card-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
    }
    .settings-card .card-header .fa,
    .settings-card .card-header .fas {
        color: #4e73df;
    }
    .settings-card .form-group label {
        font-weight: 500;
        color: #5a5c69;
        margin-bottom: 0.35rem;
    }
    .settings-card .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- Breadcrumbs --}}
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>TyreDash Settings</h4>
                <p class="mb-0">Configure your tyre module preferences</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('cimstyredash.dashboard') }}">TyreDash</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Settings</a></li>
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
            @include('cimstyredash::partials.sidebar', ['activePage' => 'settings'])
        </div>

        {{-- Main Content --}}
        <div class="col-xl-9 col-lg-8">

            <form action="{{ route('cimstyredash.settings.update') }}" method="POST">
                @csrf

                @foreach($groupLabels as $groupKey => $groupLabel)
                    @if(isset($settingsGrouped[$groupKey]) && $settingsGrouped[$groupKey]->count())
                        <div class="card settings-card mb-4">
                            <div class="card-header">
                                <h4 class="card-title mb-0">
                                    <i class="fas fa-sliders-h me-2"></i>{{ $groupLabel }}
                                </h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($settingsGrouped[$groupKey] as $setting)
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="setting_{{ $setting->setting_key }}">
                                                    {{ ucwords(str_replace('_', ' ', $setting->setting_key)) }}
                                                </label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="setting_{{ $setting->setting_key }}"
                                                    name="settings[{{ $setting->setting_key }}]"
                                                    value="{{ old('settings.' . $setting->setting_key, $setting->setting_value) }}"
                                                    placeholder="Enter {{ strtolower(str_replace('_', ' ', $setting->setting_key)) }}"
                                                >
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

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

                {{-- Submit Button --}}
                <div class="card">
                    <div class="card-body text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Settings
                        </button>
                    </div>
                </div>

            </form>

        </div>{{-- /.col-xl-9 --}}
    </div>{{-- /.row --}}
</div>{{-- /.container-fluid --}}
@endsection
