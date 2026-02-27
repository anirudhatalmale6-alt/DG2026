@extends('layouts.default')

@section('content')
<div class="container-fluid">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="fa fa-envelope text-primary me-2"></i> SMTP Email Configuration
        </h4>
        <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('cimsdocgen.settings') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-sliders-h me-1"></i> General Settings
            </a>
            <a href="{{ route('cimsdocgen.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left me-1"></i> Back to Documents
            </a>
        </div>
    </div>

    {{-- ── Flash Messages ────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── Validation Errors ─────────────────────────────────────────────── --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fa fa-exclamation-triangle me-1"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ── SMTP Configuration Form ───────────────────────────────────────── --}}
    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('cimsdocgen.smtp.save') }}" id="smtpForm">
                @csrf

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fa fa-server text-muted me-2"></i> SMTP Server Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- SMTP Host --}}
                            <div class="col-md-6">
                                <label for="smtp_host" class="form-label fw-semibold small text-uppercase text-muted">
                                    SMTP Host <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('smtp_host') is-invalid @enderror"
                                       id="smtp_host"
                                       name="smtp_host"
                                       value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                                       placeholder="smtp.gmail.com"
                                       required>
                                @error('smtp_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- SMTP Port --}}
                            <div class="col-md-3">
                                <label for="smtp_port" class="form-label fw-semibold small text-uppercase text-muted">
                                    SMTP Port <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                       class="form-control @error('smtp_port') is-invalid @enderror"
                                       id="smtp_port"
                                       name="smtp_port"
                                       value="{{ old('smtp_port', $settings['smtp_port'] ?? 587) }}"
                                       min="1"
                                       max="65535"
                                       required>
                                @error('smtp_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Encryption --}}
                            <div class="col-md-3">
                                <label for="smtp_encryption" class="form-label fw-semibold small text-uppercase text-muted">
                                    Encryption
                                </label>
                                <select class="form-select @error('smtp_encryption') is-invalid @enderror"
                                        id="smtp_encryption"
                                        name="smtp_encryption">
                                    <option value="" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') == '' ? 'selected' : '' }}>None</option>
                                    <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                                @error('smtp_encryption')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- SMTP Username --}}
                            <div class="col-md-6">
                                <label for="smtp_username" class="form-label fw-semibold small text-uppercase text-muted">
                                    SMTP Username <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('smtp_username') is-invalid @enderror"
                                       id="smtp_username"
                                       name="smtp_username"
                                       value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}"
                                       placeholder="your-email@gmail.com"
                                       required>
                                @error('smtp_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- SMTP Password --}}
                            <div class="col-md-6">
                                <label for="smtp_password" class="form-label fw-semibold small text-uppercase text-muted">
                                    SMTP Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password"
                                           class="form-control @error('smtp_password') is-invalid @enderror"
                                           id="smtp_password"
                                           name="smtp_password"
                                           value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}"
                                           placeholder="Enter SMTP password or app password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword"
                                            data-bs-toggle="tooltip" title="Show/Hide Password">
                                        <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                    </button>
                                    @error('smtp_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- From Address --}}
                            <div class="col-md-6">
                                <label for="smtp_from_address" class="form-label fw-semibold small text-uppercase text-muted">
                                    From Address <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                       class="form-control @error('smtp_from_address') is-invalid @enderror"
                                       id="smtp_from_address"
                                       name="smtp_from_address"
                                       value="{{ old('smtp_from_address', $settings['smtp_from_address'] ?? '') }}"
                                       placeholder="noreply@yourcompany.com"
                                       required>
                                @error('smtp_from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- From Name --}}
                            <div class="col-md-6">
                                <label for="smtp_from_name" class="form-label fw-semibold small text-uppercase text-muted">
                                    From Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control @error('smtp_from_name') is-invalid @enderror"
                                       id="smtp_from_name"
                                       name="smtp_from_name"
                                       value="{{ old('smtp_from_name', $settings['smtp_from_name'] ?? '') }}"
                                       placeholder="Your Company Name"
                                       required>
                                @error('smtp_from_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Save Button --}}
                    <div class="card-footer bg-light text-end">
                        <button type="submit" class="btn btn-primary px-4" id="btnSaveSmtp">
                            <i class="fa fa-save me-1"></i> Save SMTP Settings
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    {{-- ── Test Connection Section ────────────────────────────────────────── --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fa fa-paper-plane text-muted me-2"></i> Test SMTP Connection
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Send a test email to verify your SMTP settings are working correctly. Make sure you save your settings above before testing.
                    </p>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="test_email" class="form-label fw-semibold small text-uppercase text-muted">
                                Test Recipient Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                   class="form-control"
                                   id="test_email"
                                   placeholder="test@example.com"
                                   required>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-primary px-4" id="btnTestSmtp">
                                <i class="fa fa-paper-plane me-1"></i> Send Test Email
                            </button>
                        </div>
                    </div>

                    {{-- Test Result Display --}}
                    <div id="testResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Initialize Bootstrap Tooltips ──────────────────────────────────
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    // ── Password Visibility Toggle ─────────────────────────────────────
    var toggleBtn = document.getElementById('togglePassword');
    var passwordInput = document.getElementById('smtp_password');
    var toggleIcon = document.getElementById('togglePasswordIcon');

    toggleBtn.addEventListener('click', function () {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    });

    // ── Prevent Double Submission on Save ───────────────────────────────
    document.getElementById('smtpForm').addEventListener('submit', function () {
        var btn = document.getElementById('btnSaveSmtp');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
    });

    // ── Test SMTP Connection ───────────────────────────────────────────
    var btnTest = document.getElementById('btnTestSmtp');
    var testEmailInput = document.getElementById('test_email');
    var testResultDiv = document.getElementById('testResult');

    btnTest.addEventListener('click', function () {
        var testEmail = testEmailInput.value.trim();

        if (!testEmail) {
            testEmailInput.focus();
            testEmailInput.classList.add('is-invalid');
            return;
        }

        // Basic email validation
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(testEmail)) {
            testEmailInput.classList.add('is-invalid');
            testResultDiv.style.display = 'block';
            testResultDiv.innerHTML = '<div class="alert alert-warning mb-0">' +
                '<i class="fa fa-exclamation-triangle me-2"></i> Please enter a valid email address.' +
                '</div>';
            return;
        }

        testEmailInput.classList.remove('is-invalid');

        // Show loading state
        btnTest.disabled = true;
        btnTest.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Sending Test...';
        testResultDiv.style.display = 'block';
        testResultDiv.innerHTML = '<div class="d-flex align-items-center text-muted">' +
            '<div class="spinner-border spinner-border-sm me-2" role="status">' +
            '<span class="visually-hidden">Sending...</span>' +
            '</div> Sending test email, please wait...' +
            '</div>';

        fetch('{{ route("cimsdocgen.smtp.test") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                test_email: testEmail
            })
        })
        .then(function (response) { return response.json(); })
        .then(function (data) {
            if (data.success) {
                testResultDiv.innerHTML = '<div class="alert alert-success mb-0">' +
                    '<i class="fa fa-check-circle me-2"></i> ' +
                    (data.message || 'Test email sent successfully! Check your inbox.') +
                    '</div>';
            } else {
                testResultDiv.innerHTML = '<div class="alert alert-danger mb-0">' +
                    '<i class="fa fa-times-circle me-2"></i> ' +
                    (data.message || 'Failed to send test email. Please check your SMTP settings.') +
                    '</div>';
            }
        })
        .catch(function (error) {
            testResultDiv.innerHTML = '<div class="alert alert-danger mb-0">' +
                '<i class="fa fa-times-circle me-2"></i> An error occurred while sending the test email. Please try again.' +
                '</div>';
            console.error('SMTP test error:', error);
        })
        .finally(function () {
            btnTest.disabled = false;
            btnTest.innerHTML = '<i class="fa fa-paper-plane me-1"></i> Send Test Email';
        });
    });

    // ── Remove invalid class on input ──────────────────────────────────
    testEmailInput.addEventListener('input', function () {
        this.classList.remove('is-invalid');
    });

});
</script>
@endpush

@endsection
