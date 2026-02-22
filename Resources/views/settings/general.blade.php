@extends('smartdash::layouts.default')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-7">

            {{-- ── Page Header Card ─────────────────────────────────────────────── --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #f8f9fc 0%, #eef1f8 100%); border-bottom: 2px solid #e3e8f0;">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-cog text-primary me-2"></i> Document Generator Settings
                    </h4>
                    <div class="d-flex gap-2 align-items-center">
                        <a href="{{ route('docgen.smtp') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-envelope-open-text me-1"></i> SMTP Settings
                        </a>
                        <a href="{{ route('docgen.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-arrow-left me-1"></i> Back to Documents
                        </a>
                    </div>
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
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="fa fa-exclamation-triangle me-1"></i> Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- ── Settings Form ─────────────────────────────────────────────────── --}}
            <form id="settingsForm" method="POST" action="{{ route('docgen.settings.save') }}" enctype="multipart/form-data">
                @csrf

                {{-- ── Section 1: Default Font Settings ──────────────────────────── --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fa fa-font text-muted me-2"></i> Default Font Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Font Family --}}
                            <div class="col-md-6">
                                <label for="font_family" class="form-label fw-semibold">
                                    Font Family <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select @error('font_family') is-invalid @enderror"
                                    id="font_family"
                                    name="font_family"
                                    required
                                >
                                    <option value="Helvetica" {{ ($settings['default_font_family'] ?? old('font_family')) === 'Helvetica' ? 'selected' : '' }}>Helvetica</option>
                                    <option value="Times" {{ ($settings['default_font_family'] ?? old('font_family')) === 'Times' ? 'selected' : '' }}>Times</option>
                                    <option value="Courier" {{ ($settings['default_font_family'] ?? old('font_family')) === 'Courier' ? 'selected' : '' }}>Courier</option>
                                    <option value="Arial" {{ ($settings['default_font_family'] ?? old('font_family')) === 'Arial' ? 'selected' : '' }}>Arial</option>
                                </select>
                                @error('font_family')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">The default font used when generating new documents.</div>
                            </div>

                            {{-- Font Size --}}
                            <div class="col-md-6">
                                <label for="font_size" class="form-label fw-semibold">
                                    Font Size (pt) <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    class="form-control @error('font_size') is-invalid @enderror"
                                    id="font_size"
                                    name="font_size"
                                    value="{{ $settings['default_font_size'] ?? old('font_size', 12) }}"
                                    step="0.5"
                                    min="6"
                                    max="72"
                                    required
                                >
                                @error('font_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">Size in points (6 - 72). Default is typically 12pt.</div>
                            </div>

                            {{-- Font Color --}}
                            <div class="col-md-6">
                                <label for="font_color" class="form-label fw-semibold">
                                    Font Color <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input
                                        type="color"
                                        class="form-control form-control-color border"
                                        id="font_color_picker"
                                        value="{{ $settings['default_font_color'] ?? old('font_color', '#000000') }}"
                                        title="Choose font color"
                                    >
                                    <input
                                        type="text"
                                        class="form-control font-monospace @error('font_color') is-invalid @enderror"
                                        id="font_color"
                                        name="font_color"
                                        value="{{ $settings['default_font_color'] ?? old('font_color', '#000000') }}"
                                        placeholder="#000000"
                                        maxlength="7"
                                        pattern="^#[0-9A-Fa-f]{6}$"
                                        required
                                    >
                                    @error('font_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text text-muted">Hex color code for the default text color (e.g. #000000).</div>
                            </div>

                            {{-- Text Alignment --}}
                            <div class="col-md-6">
                                <label for="text_align" class="form-label fw-semibold">
                                    Text Alignment <span class="text-danger">*</span>
                                </label>
                                <select
                                    class="form-select @error('text_align') is-invalid @enderror"
                                    id="text_align"
                                    name="text_align"
                                    required
                                >
                                    <option value="left" {{ ($settings['default_text_align'] ?? old('text_align')) === 'left' ? 'selected' : '' }}>Left</option>
                                    <option value="center" {{ ($settings['default_text_align'] ?? old('text_align')) === 'center' ? 'selected' : '' }}>Center</option>
                                    <option value="right" {{ ($settings['default_text_align'] ?? old('text_align')) === 'right' ? 'selected' : '' }}>Right</option>
                                    <option value="justify" {{ ($settings['default_text_align'] ?? old('text_align')) === 'justify' ? 'selected' : '' }}>Justify</option>
                                </select>
                                @error('text_align')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">Default paragraph alignment for generated documents.</div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Section 2: Company Information ────────────────────────────── --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fa fa-building text-muted me-2"></i> Company Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Company Name --}}
                            <div class="col-12">
                                <label for="company_name" class="form-label fw-semibold">
                                    Company Name
                                </label>
                                <input
                                    type="text"
                                    class="form-control @error('company_name') is-invalid @enderror"
                                    id="company_name"
                                    name="company_name"
                                    value="{{ $settings['company_name'] ?? old('company_name') }}"
                                    placeholder="Enter your company or organisation name"
                                >
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">This name may appear on document headers and footers.</div>
                            </div>

                            {{-- Company Logo --}}
                            <div class="col-12">
                                <label for="company_logo" class="form-label fw-semibold">
                                    Company Logo
                                </label>

                                {{-- Current Logo Preview --}}
                                @if(!empty($settings['company_logo_path']))
                                    <div class="mb-3 p-3 border rounded-3 bg-light">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="flex-shrink-0">
                                                <img
                                                    src="{{ asset($settings['company_logo_path']) }}"
                                                    alt="Current Company Logo"
                                                    class="rounded border"
                                                    style="max-height: 60px; max-width: 200px; object-fit: contain;"
                                                    id="currentLogoPreview"
                                                >
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="text-muted small fw-semibold">Current Logo</div>
                                                <div class="text-muted small text-truncate" style="max-width: 300px;">
                                                    {{ basename($settings['company_logo_path']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <input
                                    type="file"
                                    class="form-control @error('company_logo') is-invalid @enderror"
                                    id="company_logo"
                                    name="company_logo"
                                    accept="image/*"
                                >
                                @error('company_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    Upload a logo image (PNG, JPG, SVG). Recommended max height: 80px.
                                    @if(!empty($settings['company_logo_path']))
                                        Leave blank to keep the current logo.
                                    @endif
                                </div>

                                {{-- New Logo Preview --}}
                                <div id="newLogoPreviewWrapper" class="mt-3 p-3 border rounded-3 bg-light" style="display: none;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <img
                                                src="#"
                                                alt="New Logo Preview"
                                                class="rounded border"
                                                style="max-height: 60px; max-width: 200px; object-fit: contain;"
                                                id="newLogoPreview"
                                            >
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-muted small fw-semibold">New Logo Preview</div>
                                            <div class="text-muted small" id="newLogoFilename"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Section 3: Storage ────────────────────────────────────────── --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fa fa-folder-open text-muted me-2"></i> Storage</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Document Storage Path --}}
                            <div class="col-12">
                                <label for="document_storage_path" class="form-label fw-semibold">
                                    Document Storage Path
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa fa-hdd text-muted"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control font-monospace @error('document_storage_path') is-invalid @enderror"
                                        id="document_storage_path"
                                        name="document_storage_path"
                                        value="{{ $settings['document_storage_path'] ?? old('document_storage_path') }}"
                                        placeholder="e.g. storage/app/documents"
                                    >
                                    @error('document_storage_path')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text text-muted">
                                    The file system path where generated documents (PDFs) will be stored.
                                    This path is relative to your application root. Ensure the directory exists and is writable by the web server.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ── Save Button ───────────────────────────────────────────────── --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body text-center py-4">
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('docgen.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fa fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="btnSaveSettings">
                                <i class="fa fa-save me-2"></i> Save Settings
                            </button>
                        </div>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Color Picker Sync ──────────────────────────────────────────────
    var colorPicker = document.getElementById('font_color_picker');
    var colorText   = document.getElementById('font_color');

    // Sync: picker -> text input
    colorPicker.addEventListener('input', function () {
        colorText.value = this.value.toUpperCase();
    });

    // Sync: text input -> picker
    colorText.addEventListener('input', function () {
        var val = this.value.trim();
        // Auto-prepend # if missing
        if (val.length > 0 && val.charAt(0) !== '#') {
            val = '#' + val;
            this.value = val;
        }
        // Update picker when we have a valid 7-char hex code
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            colorPicker.value = val;
        }
    });

    // ── Company Logo Preview ───────────────────────────────────────────
    var logoInput          = document.getElementById('company_logo');
    var newPreviewWrapper  = document.getElementById('newLogoPreviewWrapper');
    var newPreviewImg      = document.getElementById('newLogoPreview');
    var newPreviewFilename = document.getElementById('newLogoFilename');

    logoInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            var file   = this.files[0];
            var reader = new FileReader();

            reader.onload = function (e) {
                newPreviewImg.src = e.target.result;
                newPreviewFilename.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                newPreviewWrapper.style.display = 'block';
            };

            reader.readAsDataURL(file);
        } else {
            newPreviewWrapper.style.display = 'none';
            newPreviewImg.src = '#';
            newPreviewFilename.textContent = '';
        }
    });

    // ── Prevent Double Submission ──────────────────────────────────────
    document.getElementById('settingsForm').addEventListener('submit', function () {
        var btn = document.getElementById('btnSaveSettings');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i> Saving...';
    });

});
</script>
@endpush

@endsection
