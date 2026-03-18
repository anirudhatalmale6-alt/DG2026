@extends('layouts.default')

@section('title', isset($person) ? 'Edit Person' : 'New Person')

@push('styles')
<link href="/public/smartdash/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
<link href="/public/smartdash/css/smartdash-forms.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ============================================
   PERSON FORM — PREMIUM RESTYLED
   SmartWeigh Brand: #009688 / #4DB6AC / #00796B / #004D40
   ============================================ */

/* ---------- Section Card Enhancements ---------- */
.person-section-card {
    border: none !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06) !important;
    margin-bottom: 28px !important;
    overflow: hidden;
    transition: box-shadow 0.4s ease, transform 0.4s ease;
    animation: slideUp 0.6s ease forwards;
    opacity: 0;
}

.person-section-card:nth-child(1) { animation-delay: 0.05s; }
.person-section-card:nth-child(2) { animation-delay: 0.15s; }
.person-section-card:nth-child(3) { animation-delay: 0.25s; }
.person-section-card:nth-child(4) { animation-delay: 0.35s; }
.person-section-card:nth-child(5) { animation-delay: 0.45s; }
.person-section-card:nth-child(6) { animation-delay: 0.55s; }
.person-section-card:nth-child(7) { animation-delay: 0.65s; }

@keyframes slideUp {
    from { opacity: 0; transform: translateY(24px); }
    to { opacity: 1; transform: translateY(0); }
}

.person-section-card:hover {
    box-shadow: 0 8px 32px rgba(0, 150, 136, 0.15) !important;
}

/* Card Header — Gradient Banner */
.person-section-card > .card-header {
    background: linear-gradient(135deg, #004D40 0%, #009688 60%, #4DB6AC 100%) !important;
    border: none !important;
    padding: 18px 24px !important;
    position: relative;
    overflow: hidden;
}

.person-section-card > .card-header::before {
    content: '';
    position: absolute;
    top: -30px;
    right: -30px;
    width: 120px;
    height: 120px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
}

.person-section-card > .card-header h4 {
    color: #fff !important;
    font-family: 'Poppins', sans-serif !important;
    font-size: 18px !important;
    font-weight: 600 !important;
    margin: 0 !important;
    letter-spacing: 0.5px;
    text-shadow: 0 1px 3px rgba(0,0,0,0.15);
    position: relative;
    z-index: 1;
}

.person-section-card > .card-header h4 i {
    margin-right: 10px;
    opacity: 0.9;
}

/* Form Section Titles (sub-headers) */
.person-section-card .form-section-title {
    background: linear-gradient(to right, #E0F2F1, #fff) !important;
    border-left: 4px solid #009688 !important;
    border-bottom: none !important;
    color: #004D40 !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    padding: 12px 18px !important;
    margin: 20px 0 16px !important;
    border-radius: 0 10px 10px 0 !important;
    font-family: 'Poppins', sans-serif !important;
    letter-spacing: 0.8px;
}

.person-section-card .form-section-title i {
    color: #009688 !important;
    margin-right: 8px;
}

/* Card Body */
.person-section-card > .card-body {
    padding: 24px !important;
}

/* ---------- Form Controls Brand Override ---------- */
.person-section-card .form-control:focus {
    border-color: #009688 !important;
    box-shadow: 0 0 0 3px rgba(0, 150, 136, 0.12) !important;
}

.person-section-card .input-group-text {
    background: linear-gradient(135deg, #009688, #00796B) !important;
    border-color: #009688 !important;
    color: #fff !important;
}

.person-section-card .input-group-text i {
    color: #fff !important;
}

/* ---------- Spouse section hidden by default ---------- */
#spouse_section {
    display: none;
}

/* Date of death hidden by default */
#date_of_death_container,
#sp_date_of_death_container {
    display: none;
}

/* Passport row hidden by default */
#passportRow,
#passportRow2 {
    display: none;
}

/* ---------- Photo Carousel ---------- */
#imageCarousel .carousel-item img {
    height: 300px;
    object-fit: contain;
}

.image-title-btn {
    margin: 2px;
    background: linear-gradient(135deg, #009688 0%, #004D40 100%);
    border: none;
    font-size: 13px;
    padding: 8px 12px;
    border-radius: 8px;
    color: #fff;
    font-weight: 500;
    transition: all 0.3s ease;
}

.image-title-btn:hover {
    background: linear-gradient(135deg, #004D40 0%, #009688 100%);
    transform: translateY(-1px);
}

/* Signature placeholder */
.signature-placeholder {
    border: 2px dashed #4DB6AC;
    padding: 30px;
    text-align: center;
    margin-top: 12px;
    border-radius: 12px;
    background: linear-gradient(135deg, #E0F2F1, #f8f9fa);
}

.signature-placeholder p {
    color: #004D40;
    font-weight: 600;
    font-size: 16px;
    margin: 0;
    font-family: 'Poppins', sans-serif;
}

/* Carousel controls */
.carousel-control-prev,
.carousel-control-next {
    background: linear-gradient(135deg, #009688, #004D40);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.9;
    transition: all 0.3s ease;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    opacity: 1;
    box-shadow: 0 4px 12px rgba(0, 150, 136, 0.4);
}

.carousel-control-prev {
    left: 10px;
}

.carousel-control-next {
    right: 10px;
}

/* Photo Gallery Section - Full Width Grid */
.photo-gallery-section {
    background: #fff;
    border: 1px solid #4DB6AC;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.photo-gallery-row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -8px;
    padding: 0;
}

.photo-frame {
    padding: 8px;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.photo-frame .photo-frame-box,
.photo-frame .smart-card-frames {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
}

.photo-frame .smart-card-frames .photo-frame-box {
    flex-grow: 0;
}

/* Jumbo Photo 10x15cm = 2:3 ratio (portrait) - Full width responsive */
.photo-frame-box {
    border: 4px solid #4DB6AC;
    border-radius: 12px;
    width: 100%;
    aspect-ratio: 2/3;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.25);
}

.photo-frame-box:hover {
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.45);
    transform: scale(1.02);
    border-color: #009688;
}

.photo-frame-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.photo-frame-box .no-photo {
    color: #4DB6AC;
    font-size: 13px;
    text-align: center;
}

.photo-frame-box .no-photo i {
    font-size: 50px;
    display: block;
    margin-bottom: 10px;
    color: #4DB6AC;
    opacity: 0.6;
}

.photo-frame-label {
    margin-top: 10px;
    font-weight: 700;
    color: #004D40;
    font-size: 13px;
    text-transform: uppercase;
}

.smart-card-frames {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* ID Card ratio 3:2 landscape (width:height = 3:2) */
.smart-card-frames .photo-frame-box {
    width: 100%;
    aspect-ratio: 3/2;
}

/* Photo Zoom Modal */
.photo-zoom-modal .modal-body {
    text-align: center;
    padding: 20px;
}

.photo-zoom-modal .modal-body img {
    max-width: 100%;
    max-height: 70vh;
    object-fit: contain;
}

/* Signature Section */
.signature-section {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 2px dashed #4DB6AC;
}

.signature-display {
    min-height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 10px;
}

.signature-display img {
    max-height: 80px;
    max-width: 100%;
}

.signature-display .no-signature {
    color: #999;
    font-style: italic;
}

/* Signature Pad Modal */
#signaturePadModal .modal-body {
    padding: 20px;
}

#signatureCanvas {
    border: 2px solid #009688;
    border-radius: 8px;
    background: #fff;
    cursor: crosshair;
}

/* Webcam Modal */
#webcamModal .modal-body {
    text-align: center;
}

#my_camera {
    border: 2px solid #009688;
    border-radius: 8px;
    overflow: hidden;
    margin: 0 auto;
}

#my_camera video {
    border-radius: 8px;
}

/* Pill-style buttons for photo actions */
.photo-pill-btn {
    background: linear-gradient(135deg, #009688 0%, #00796B 100%);
    color: #fff;
    border: none;
    border-radius: 25px;
    padding: 10px 28px;
    font-size: 14px;
    font-weight: 600;
    text-transform: capitalize;
    box-shadow: 0 3px 8px rgba(23, 162, 184, 0.3);
    transition: all 0.3s ease;
}

.photo-pill-btn:hover {
    background: linear-gradient(135deg, #00796B 0%, #009688 100%);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
}

.upload-photo-link {
    color: #4DB6AC;
    font-size: 15px;
    font-weight: 600;
    text-decoration: underline;
    cursor: pointer;
}

.upload-photo-link:hover {
    color: #0d6b7a;
}

/* Signature frame - landscape ratio */
.signature-frame-box {
    aspect-ratio: 3/2 !important;
}

/* Signature image must contain fully, not crop */
.signature-frame-box img {
    object-fit: contain !important;
    background: #fff;
    padding: 10px;
}

/* Photo Slider */
.photo-slider-container {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;
    padding: 10px 0;
}

.photo-slider-wrapper {
    flex: 1;
    overflow: hidden;
}

.photo-slider-track {
    display: flex;
    gap: 15px;
    transition: transform 0.4s ease;
}

.photo-slide {
    flex: 0 0 calc(25% - 12px);
    min-width: calc(25% - 12px);
    text-align: center;
}

.photo-slide .photo-frame-box {
    width: 100%;
    aspect-ratio: 2/3;
}

.photo-slide .smart-card-box {
    aspect-ratio: 3/2;
}

.slider-arrow {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #009688 0%, #00796B 100%);
    color: #fff;
    border: none;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 3px 10px rgba(23, 162, 184, 0.3);
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.slider-arrow:hover {
    background: linear-gradient(135deg, #00796B 0%, #009688 100%);
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
}

.slider-arrow:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.photo-frame-box .no-photo span {
    display: block;
    font-size: 10px;
    margin-top: 5px;
}

/* ---------- Save/Cancel Buttons ---------- */
.person-section-card .sd_btn,
.person-section-card .btn-outline-secondary {
    border-radius: 12px !important;
    font-family: 'Poppins', sans-serif !important;
    font-weight: 600 !important;
    padding: 12px 28px !important;
    transition: all 0.3s ease !important;
}

/* ---------- Google Maps Link ---------- */
#googleMapLink {
    border-color: #009688 !important;
    color: #009688 !important;
    border-radius: 10px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
}

#googleMapLink:hover {
    background: #009688 !important;
    color: #fff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 150, 136, 0.3);
}

/* ---------- Upload section ---------- */
.person-section-card #photoType {
    border-color: #4DB6AC !important;
    border-radius: 8px !important;
}

/* ---------- Photo frame border radius ---------- */
.photo-frame-box {
    border-radius: 14px;
}

/* ---------- Breadcrumb Enhancement ---------- */
.page-titles .breadcrumb-item.active a {
    color: #009688 !important;
    font-weight: 600;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row page-titles">
        <div class="d-flex align-items-center justify-content-between">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="fs-2" style="color:#000" href="javascript:void(0)">Processing</a></li>
                <li class="breadcrumb-item active"><a class="fs-2" style="color:#009688" href="javascript:void(0)">Persons</a></li>
            </ol>
            <a href="{{ route('cimspersons.index') }}" class="btn sd_btn">
                <i class="fa fa-list"></i> All Persons
            </a>
        </div>
    </div>

    @php
        $is_edit = isset($person) && $person;
        $id = $is_edit ? $person->id : '';
        $readonly = isset($view_mode) && $view_mode;

        // Get reference data - hardcoded ethnic groups since tblrace doesn't exist
        $races = collect([
            (object)['race_description' => 'African'],
            (object)['race_description' => 'Coloured'],
            (object)['race_description' => 'Indian'],
            (object)['race_description' => 'White'],
            (object)['race_description' => 'Other'],
        ]);

        // Countries - check if table exists, otherwise use hardcoded
        try {
            $countries = \DB::table('tblcountries')->where('active', 1)->orderBy('short_name')->get();
        } catch (\Exception $e) {
            $countries = collect([
                (object)['short_name' => 'South Africa', 'nationality' => 'South African', 'iso3' => 'ZAF'],
            ]);
        }

        // Person Information
        $citizenship = $is_edit ? ($person->citizenship ?? 'SOUTH AFRICAN') : 'SOUTH AFRICAN';
        $identity_type = $is_edit ? ($person->identity_type ?? 'GREEN BOOK') : 'GREEN BOOK';
        $identity_number = $is_edit ? ($person->identity_number ?? '') : '';
        $date_of_birth = $is_edit ? ($person->date_of_birth ?? '') : '';
        $date_of_issue = $is_edit ? ($person->date_of_issue ?? '') : '';
        $person_status = $is_edit ? ($person->person_status ?? 'Alive') : 'Alive';
        $gender = $is_edit ? ($person->gender ?? '') : '';
        $ethnic_group = $is_edit ? ($person->ethnic_group ?? '') : '';
        $disability = $is_edit ? ($person->disability ?? '0') : '0';
        $date_of_death = $is_edit ? ($person->date_of_death ?? '') : '';
        $passport_number = $is_edit ? ($person->passport_number ?? '') : '';
        $passport_expiry = $is_edit ? ($person->passport_expiry ?? '') : '';
        $country = $is_edit ? ($person->country ?? '') : '';
        $country_code = $is_edit ? ($person->country_code ?? '') : '';
        $nationality = $is_edit ? ($person->nationality ?? '') : '';

        // Personal Information
        $title = $is_edit ? ($person->title ?? 'Mr') : 'Mr';
        $initials = $is_edit ? ($person->initials ?? '') : '';
        $tax_number = $is_edit ? ($person->tax_number ?? '') : '';
        $surname = $is_edit ? ($person->surname ?? '') : '';
        $firstname = $is_edit ? ($person->firstname ?? '') : '';
        $middlename = $is_edit ? ($person->middlename ?? '') : '';
        $known_as = $is_edit ? ($person->known_as ?? '') : '';
        $mobile_phone = $is_edit ? ($person->mobile_phone ?? '') : '';
        $whatsapp_number = $is_edit ? ($person->whatsapp_number ?? '') : '';
        $office_phone = $is_edit ? ($person->office_phone ?? '') : '';
        $other_phone = $is_edit ? ($person->other_phone ?? '') : '';
        $email = $is_edit ? ($person->email ?? '') : '';
        $accounts_email = $is_edit ? ($person->accounts_email ?? '') : '';
        $marital_status = $is_edit ? ($person->marital_status ?? 'Single') : 'Single';
        $marital_status_date = $is_edit ? ($person->marital_status_date ?? '') : '';

        // Spouse Details
        $sp_citizenship = $is_edit ? ($person->sp_citizenship ?? 'SOUTH AFRICAN') : 'SOUTH AFRICAN';
        $sp_identity_type = $is_edit ? ($person->sp_identity_type ?? 'GREEN BOOK') : 'GREEN BOOK';
        $sp_identity_number = $is_edit ? ($person->sp_identity_number ?? '') : '';
        $sp_date_of_birth = $is_edit ? ($person->sp_date_of_birth ?? '') : '';
        $sp_date_of_issue = $is_edit ? ($person->sp_date_of_issue ?? '') : '';
        $sp_person_status = $is_edit ? ($person->sp_person_status ?? 'Alive') : 'Alive';
        $sp_gender = $is_edit ? ($person->sp_gender ?? '') : '';
        $sp_ethnic_group = $is_edit ? ($person->sp_ethnic_group ?? '') : '';
        $sp_disability = $is_edit ? ($person->sp_disability ?? '0') : '0';
        $sp_date_of_death = $is_edit ? ($person->sp_date_of_death ?? '') : '';
        $sp_country = $is_edit ? ($person->sp_country ?? '') : '';
        $sp_country_code = $is_edit ? ($person->sp_country_code ?? '') : '';
        $sp_nationality = $is_edit ? ($person->sp_nationality ?? '') : '';
        $sp_title = $is_edit ? ($person->sp_title ?? 'Mr') : 'Mr';
        $sp_initials = $is_edit ? ($person->sp_initials ?? '') : '';
        $sp_tax_number = $is_edit ? ($person->sp_tax_number ?? '') : '';
        $sp_firstname = $is_edit ? ($person->sp_firstname ?? '') : '';
        $sp_middlename = $is_edit ? ($person->sp_middlename ?? '') : '';
        $sp_surname = $is_edit ? ($person->sp_surname ?? '') : '';
        $sp_known_as = $is_edit ? ($person->sp_known_as ?? '') : '';
        $sp_mobile_phone = $is_edit ? ($person->sp_mobile_phone ?? '') : '';
        $sp_whatsapp_number = $is_edit ? ($person->sp_whatsapp_number ?? '') : '';
        $sp_office_phone = $is_edit ? ($person->sp_office_phone ?? '') : '';
        $sp_other_phone = $is_edit ? ($person->sp_other_phone ?? '') : '';
        $sp_email = $is_edit ? ($person->sp_email ?? '') : '';
        $sp_accounts_email = $is_edit ? ($person->sp_accounts_email ?? '') : '';

        // Address
        $complex_name = $is_edit ? ($person->complex_name ?? '') : '';
        $address_line = $is_edit ? ($person->address_line ?? '') : '';
        $address_line_2 = $is_edit ? ($person->address_line_2 ?? '') : '';
        $suburb = $is_edit ? ($person->suburb ?? '') : '';
        $city = $is_edit ? ($person->city ?? '') : '';
        $postal_code = $is_edit ? ($person->postal_code ?? '') : '';
        $province = $is_edit ? ($person->province ?? '') : '';
        $address_country = $is_edit ? ($person->address_country ?? 'South Africa') : 'South Africa';
        $longitude = $is_edit ? ($person->longitude ?? '') : '';
        $latitude = $is_edit ? ($person->latitude ?? '') : '';

        // Banking Details
        $bank_account_holder = $is_edit ? ($person->bank_account_holder ?? '') : '';
        $bank_name = $is_edit ? ($person->bank_name ?? '') : '';
        $bank_branch = $is_edit ? ($person->bank_branch ?? '') : '';
        $bank_account_number = $is_edit ? ($person->bank_account_number ?? '') : '';
        $bank_account_type = $is_edit ? ($person->bank_account_type ?? '') : '';
        $bank_swift_code = $is_edit ? ($person->bank_swift_code ?? '') : '';
        $bank_account_status = $is_edit ? ($person->bank_account_status ?? '') : '';
        $bank_date_opened = $is_edit ? ($person->bank_date_opened ?? '') : '';

        // Notes
        $notes = $is_edit ? ($person->notes ?? '') : '';

        // Photos & Documents
        $profile_picture = $is_edit ? ($person->profile_photo ?? $person->profile_picture ?? '') : '';
        $id_front_image = $is_edit ? ($person->id_front_image ?? '') : '';
        $id_back_image = $is_edit ? ($person->id_back_image ?? '') : '';
        $green_book_image = $is_edit ? ($person->green_book_image ?? '') : '';
        $update_image = $is_edit ? ($person->update_image ?? '') : '';
        $passport_image = $is_edit ? ($person->passport_image ?? '') : '';
        $poa_image = $is_edit ? ($person->poa_image ?? '') : '';
        $banking_image = $is_edit ? ($person->banking_image ?? '') : '';
        $signature_image = $is_edit ? ($person->signature_image ?? '') : '';

        // Provinces list
        $provinces = ['Eastern Cape', 'Free State', 'Gauteng', 'KwaZulu-Natal', 'Limpopo', 'Mpumalanga', 'Northern Cape', 'North West', 'Western Cape'];
    @endphp

    <form id="personForm" novalidate autocomplete="off">
        @csrf
        <input type="hidden" name="person_id" value="{{ $id }}">

        <!-- PERSON INFORMATION WORKPAGE -->
        <div class="card smartdash-form-card person-section-card">
            <div class="card-header">
                <h4><i class="fa fa-id-card"></i> PERSON INFORMATION WORKPAGE</h4>
            </div>
            <div class="card-body">
                <!-- Citizenship Section -->
                <div class="form-section-title">
                    <i class="fa fa-flag"></i> CITIZENSHIP DETAILS
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="citizenship" class="form-label">Citizenship</label>
                            <select class="sd_drop_class" style="width: 100%" name="citizenship" id="citizenship" {{ $readonly ? 'disabled' : '' }}>
                                <option value="SOUTH AFRICAN" {{ $citizenship == 'SOUTH AFRICAN' ? 'selected' : '' }}>SOUTH AFRICAN</option>
                                <option value="NON S A CITIZEN" {{ $citizenship == 'NON S A CITIZEN' ? 'selected' : '' }}>NON S A CITIZEN</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="identity_type" class="form-label">Identity Type</label>
                            <select class="sd_drop_class" style="width: 100%" name="identity_type" id="identity_type" {{ $readonly ? 'disabled' : '' }}>
                                <option value="GREEN BOOK" {{ $identity_type == 'GREEN BOOK' ? 'selected' : '' }}>GREEN BOOK</option>
                                <option value="SMART CARD" {{ $identity_type == 'SMART CARD' ? 'selected' : '' }}>SMART CARD</option>
                                <option value="PASSPORT" {{ $identity_type == 'PASSPORT' ? 'selected' : '' }} style="display:none;">PASSPORT</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Identity Details Section (changes to PASSPORT DETAILS for non-citizens) -->
                <div class="form-section-title" id="identityDetailsTitle">
                    <i class="fa fa-id-card" id="identityDetailsIcon"></i> <span id="identityDetailsTitleText">IDENTITY DETAILS</span>
                </div>
                <div class="row" id="identityRow">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="identity_number" class="form-label">Identity Number</label>
                            <input type="text" name="identity_number" id="identity_number"
                                   class="form-control identity_number id-number-display"
                                   value="{{ e($identity_number) }}"
                                   data-parent="identity_section"
                                   data-parsley-errors-container="#identity_number_error"
                                   {{ $readonly ? 'readonly' : '' }}>
                            <div id="identity_number_error"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date_of_birth" class="form-label">Date Of Birth</label>
                            <input type="text" name="date_of_birth" id="date_of_birth"
                                   class="form-control" value="{{ e($date_of_birth) }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date_of_issue" class="form-label">Date Of Issue</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker-past" name="date_of_issue" id="date_of_issue"
                                       class="form-control" value="{{ $date_of_issue }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="person_status" class="form-label">Person Status</label>
                            <select class="sd_drop_class" style="width: 100%" name="person_status" id="person_status" {{ $readonly ? 'disabled' : '' }}>
                                <option value="Alive" {{ $person_status == 'Alive' ? 'selected' : '' }}>Alive</option>
                                <option value="Deceased" {{ $person_status == 'Deceased' ? 'selected' : '' }}>Deceased</option>
                                <option value="Other" {{ $person_status == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Passport Row (for Non-SA Citizens) - appears right after IDENTITY/PASSPORT DETAILS section -->
                <div id="passportRow" style="display:none;">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="passport_number" class="form-label">Passport Number</label>
                                <input type="text" name="passport_number" id="passport_number"
                                       class="form-control id-number-display" value="{{ e($passport_number) }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="date_of_birth_passport" class="form-label">Date Of Birth</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control datepicker-past" name="date_of_birth_passport" id="date_of_birth_passport"
                                           class="form-control" {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="date_of_issue_passport" class="form-label">Date Of Issue</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control datepicker-past" name="date_of_issue_passport" id="date_of_issue_passport"
                                           class="form-control" {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="passport_expiry" class="form-label">Date Of Expiry</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                    <input type="text" class="form-control datepicker-future" name="passport_expiry" id="passport_expiry"
                                           class="form-control" value="{{ $passport_expiry }}"
                                           {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Demographics Section -->
                <div class="form-section-title" id="demographicsTitle">
                    <i class="fa fa-users"></i> DEMOGRAPHICS
                </div>
                <div class="row" id="saGenderRow">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="sd_drop_class" style="width: 100%" name="gender" id="gender" {{ $readonly ? 'disabled' : '' }}>
                                <option value="Male" {{ $gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $gender == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ $gender == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ethnic_group" class="form-label">Ethnic Group</label>
                            <select class="sd_drop_class" style="width: 100%" name="ethnic_group" id="ethnic_group" {{ $readonly ? 'disabled' : '' }}>
                                @foreach($races as $race)
                                    <option value="{{ $race->race_description }}" {{ $ethnic_group == $race->race_description ? 'selected' : '' }}>{{ $race->race_description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="disability" class="form-label">Disability</label>
                            <select class="sd_drop_class" style="width: 100%" name="disability" id="disability" {{ $readonly ? 'disabled' : '' }}>
                                <option value="0" {{ $disability == '0' ? 'selected' : '' }}>Not Disabled</option>
                                <option value="1" {{ $disability == '1' ? 'selected' : '' }}>Disabled</option>
                                <option value="other" {{ $disability == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Passport Demographics (for Non-SA Citizens) -->
                <div class="row" id="passportDemographicsRow" style="display:none;">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="gender_passport" class="form-label">Gender</label>
                            <select class="sd_drop_class" style="width: 100%" name="gender_passport" id="gender_passport" {{ $readonly ? 'disabled' : '' }}>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="ethnic_group_passport" class="form-label">Ethnic Group</label>
                            <select class="sd_drop_class" style="width: 100%" name="ethnic_group_passport" id="ethnic_group_passport" {{ $readonly ? 'disabled' : '' }}>
                                @foreach($races as $race)
                                    <option value="{{ $race->race_description }}">{{ $race->race_description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="disability_passport" class="form-label">Disability</label>
                            <select class="sd_drop_class" style="width: 100%" name="disability_passport" id="disability_passport" {{ $readonly ? 'disabled' : '' }}>
                                <option value="0">Not Disabled</option>
                                <option value="1">Disabled</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row" id="date_of_death_container" style="display: {{ $person_status == 'Deceased' ? 'flex' : 'none' }};">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date_of_death" class="form-label">Date Of Death</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker-past" name="date_of_death" id="date_of_death"
                                       class="form-control" value="{{ $date_of_death }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Country Section -->
                <div class="form-section-title">
                    <i class="fa fa-globe"></i> COUNTRY & NATIONALITY
                </div>
                <div class="row">
                    <div class="col-md-4" id="country_list_container">
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <select class="sd_drop_class" style="width: 100%" name="country" id="country" {{ $readonly ? 'disabled' : '' }}>
                                <option value="">Select a Country</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->short_name }}"
                                            data-nlt="{{ $c->nationality }}"
                                            data-code="{{ $c->iso3 }}"
                                            {{ $country == $c->short_name ? 'selected' : '' }}>{{ $c->short_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="country_code" class="form-label">Country Code</label>
                            <input type="text" name="country_code" id="country_code"
                                   class="form-control" value="{{ e($country_code) }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="nationality" class="form-label">Nationality</label>
                            <input type="text" name="nationality" id="nationality"
                                   class="form-control" value="{{ e($nationality) }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PERSONAL INFORMATION -->
        <div class="card smartdash-form-card person-section-card">
            <div class="card-header">
                <h4><i class="fa fa-user"></i> PERSONAL INFORMATION</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-7">
                        <!-- Name & Title Section -->
                        <div class="form-section-title">
                            <i class="fa fa-user-tag"></i> NAME & TITLE
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <select class="sd_drop_class" style="width: 100%" name="title" id="title" {{ $readonly ? 'disabled' : '' }}>
                                        <option value="Mr" {{ $title == 'Mr' ? 'selected' : '' }}>Mr</option>
                                        <option value="Ms" {{ $title == 'Ms' ? 'selected' : '' }}>Ms</option>
                                        <option value="Mrs" {{ $title == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                        <option value="Dr" {{ $title == 'Dr' ? 'selected' : '' }}>Dr</option>
                                        <option value="Prof" {{ $title == 'Prof' ? 'selected' : '' }}>Prof</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="initials" class="form-label">Initials</label>
                                    <input type="text" name="initials" id="initials"
                                           class="form-control" value="{{ e($initials) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tax_number" class="form-label">Personal Income Tax Number</label>
                                    <input type="text" name="tax_number" id="tax_number"
                                           class="form-control sd_highlight num10digit" value="{{ e($tax_number) }}"
                                           maxlength="12"                                            {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="surname" class="form-label">Surname</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                        <input type="text" name="surname" id="surname"
                                               class="form-control" value="{{ e($surname) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="firstname" class="form-label">First Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                        <input type="text" name="firstname" id="firstname"
                                               class="form-control" value="{{ e($firstname) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="middlename" class="form-label">Middle Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                        <input type="text" name="middlename" id="middlename"
                                               class="form-control" value="{{ e($middlename) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="known_as" class="form-label">Known As</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                        <input type="text" name="known_as" id="known_as"
                                               class="form-control" value="{{ e($known_as) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details Section -->
                        <div class="form-section-title">
                            <i class="fa fa-phone"></i> CONTACT DETAILS
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="mobile_phone" class="form-label">Mobile</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-mobile-alt"></i></span>
                                        <input type="tel" name="mobile_phone" id="mobile_phone"
                                               class="form-control num10digit" value="{{ e($mobile_phone) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="whatsapp_number" class="form-label">Whatsapp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                        <input type="text" name="whatsapp_number" id="whatsapp_number"
                                               class="form-control num10digit" value="{{ e($whatsapp_number) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="office_phone" class="form-label">Office</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                        <input type="tel" name="office_phone" id="office_phone"
                                               class="form-control num10digit" value="{{ e($office_phone) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="other_phone" class="form-label">Other</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                        <input type="tel" name="other_phone" id="other_phone"
                                               class="form-control num10digit" value="{{ e($other_phone) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                        <input type="email" name="email" id="email"
                                               class="form-control email_validation" value="{{ e($email) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="accounts_email" class="form-label">Accounts Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                        <input type="email" name="accounts_email" id="accounts_email"
                                               class="form-control email_validation" value="{{ e($accounts_email) }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Marital Status Section -->
                        <div class="form-section-title">
                            <i class="fa fa-heart"></i> MARITAL STATUS
                        </div>
                        <div class="row">
                            <div class="col-md-6" id="marital_status_container">
                                <div class="mb-3">
                                    <label for="marital_status" class="form-label">Marital Status</label>
                                    <select class="sd_drop_class" style="width: 100%" name="marital_status" id="marital_status" {{ $readonly ? 'disabled' : '' }}>
                                        <option value="Single" {{ $marital_status == 'Single' ? 'selected' : '' }}>Single</option>
                                        <option value="Married COP" {{ $marital_status == 'Married COP' ? 'selected' : '' }}>Married Community of Property</option>
                                        <option value="Married ANC" {{ $marital_status == 'Married ANC' ? 'selected' : '' }}>Married Antenuptial Contract</option>
                                        <option value="Divorced" {{ $marital_status == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="Widowed" {{ $marital_status == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="Separated" {{ $marital_status == 'Separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="Civil Partnership/Domestic Partnership" {{ $marital_status == 'Civil Partnership/Domestic Partnership' ? 'selected' : '' }}>Civil Partnership/Domestic Partnership</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" id="marital_status_date_container" style="display: {{ in_array($marital_status, ['Married COP', 'Married ANC']) ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label for="marital_status_date" class="form-label">Marital Status Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                        <input type="text" class="form-control datepicker-past" name="marital_status_date" id="marital_status_date"
                                               class="form-control" value="{{ $marital_status_date }}"
                                               {{ $readonly ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Photo Section -->
                    <div class="col-md-5 text-center">
                        <div class="form-section-title">
                            <i class="fa fa-camera"></i> PHOTOS & SIGNATURE
                        </div>
                        <div id="result" class="mb-3">
                            <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner" id="carouselSlides">
                                    <div class="carousel-item active" data-title="No Image">
                                        <img src="/public/smartdash/images/user.jpg" class="d-block w-100" style="height: 300px; object-fit: contain;">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                                    <i class="fa fa-chevron-left" aria-hidden="true"></i>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>

                        <div id="image-titles" class="mb-3">
                            <button type="button" class="btn btn-primary image-title-btn" data-title="Profile">Profile</button>
                            <button type="button" class="btn btn-primary image-title-btn" id="btnIdentity" data-title="Identity">Identity</button>
                            <button type="button" class="btn btn-primary image-title-btn" id="btnIdFront" data-title="ID Front">ID Front</button>
                            <button type="button" class="btn btn-primary image-title-btn" id="btnIdBack" data-title="ID Back">ID Back</button>
                            <button type="button" class="btn btn-primary image-title-btn" data-title="Update">Update</button>
                            <button type="button" class="btn btn-primary image-title-btn" data-title="Signature">Signature</button>
                        </div>

                        <!-- Simple Upload Section -->
                        <div class="mt-3 p-3" style="background: #f8f9fa; border-radius: 8px;">
                            <label class="form-label fw-bold">Upload Photo</label>
                            <div class="d-flex gap-2 align-items-center">
                                <select id="photoType" class="form-control" style="width: 150px;">
                                    <option value="Profile">Profile</option>
                                    <option value="Identity">Identity</option>
                                    <option value="ID Front">ID Front</option>
                                    <option value="ID Back">ID Back</option>
                                    <option value="Update">Update</option>
                                    <option value="Signature">Signature</option>
                                </select>
                                <input type="file" id="photoFile" name="photo" accept="image/*" class="form-control">
                                <button type="button" id="btnUploadPhoto" class="btn sd_btn">
                                    <i class="fa fa-upload"></i> Upload
                                </button>
                            </div>
                            <div id="uploadStatus" class="mt-2"></div>
                        </div>

                        <div class="signature-placeholder mt-3">
                            <p class="text-muted">SPECIMEN SIGNATURE</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SPOUSE DETAILS (hidden by default, shown when married) -->
        <div class="card smartdash-form-card person-section-card" id="spouse_section" style="display: {{ in_array($marital_status, ['Married COP', 'Married ANC']) ? 'block' : 'none' }};">
            <div class="card-header">
                <h4><i class="fa fa-heart"></i> SPOUSE DETAILS</h4>
            </div>
            <div class="card-body">
                <!-- Spouse citizenship and identity type -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sp_citizenship" class="form-label">CITIZENSHIP</label>
                            <select class="sd_drop_class" style="width: 100%" name="sp_citizenship" id="sp_citizenship" {{ $readonly ? 'disabled' : '' }}>
                                <option value="SOUTH AFRICAN" {{ $sp_citizenship == 'SOUTH AFRICAN' ? 'selected' : '' }}>SOUTH AFRICAN</option>
                                <option value="NON S A CITIZEN" {{ $sp_citizenship == 'NON S A CITIZEN' ? 'selected' : '' }}>NON S A CITIZEN</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sp_identity_type" class="form-label">IDENTITY TYPE</label>
                            <select class="sd_drop_class" style="width: 100%" name="sp_identity_type" id="sp_identity_type" {{ $readonly ? 'disabled' : '' }}>
                                <option value="GREEN BOOK" {{ $sp_identity_type == 'GREEN BOOK' ? 'selected' : '' }}>GREEN BOOK</option>
                                <option value="SMART CARD" {{ $sp_identity_type == 'SMART CARD' ? 'selected' : '' }}>SMART CARD</option>
                                <option value="PASSPORT" {{ $sp_identity_type == 'PASSPORT' ? 'selected' : '' }} style="display:none;">PASSPORT</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Spouse identity row -->
                <div class="row" id="identity_section2">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_identity_number" class="form-label">Identity Number</label>
                            <input type="text" name="sp_identity_number" id="sp_identity_number"
                                   class="form-control identity_number id-number-display"
                                   value="{{ e($sp_identity_number) }}"
                                   data-parent="identity_spouse"
                                   data-parsley-errors-container="#identity_number_spouse_error"
                                   {{ $readonly ? 'readonly' : '' }}>
                            <div id="identity_number_spouse_error"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_date_of_birth" class="form-label">Date Of Birth</label>
                            <input type="text" name="sp_date_of_birth" id="sp_date_of_birth"
                                   class="form-control" value="{{ e($sp_date_of_birth) }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_date_of_issue" class="form-label">Date Of Issue</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker-past" name="sp_date_of_issue" id="sp_date_of_issue"
                                       class="form-control" value="{{ $sp_date_of_issue }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_person_status" class="form-label">Person Status</label>
                            <select class="sd_drop_class" style="width: 100%" name="sp_person_status" id="sp_person_status" {{ $readonly ? 'disabled' : '' }}>
                                <option value="Alive" {{ $sp_person_status == 'Alive' ? 'selected' : '' }}>Alive</option>
                                <option value="Deceased" {{ $sp_person_status == 'Deceased' ? 'selected' : '' }}>Deceased</option>
                                <option value="Other" {{ $sp_person_status == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sp_gender" class="form-label">Gender</label>
                            <select class="sd_drop_class" style="width: 100%" name="sp_gender" id="sp_gender" {{ $readonly ? 'disabled' : '' }}>
                                <option value="Male" {{ $sp_gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ $sp_gender == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ $sp_gender == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sp_ethnic_group" class="form-label">Ethnic Group</label>
                            <select class="sd_drop_class" style="width: 100%" name="sp_ethnic_group" id="sp_ethnic_group" {{ $readonly ? 'disabled' : '' }}>
                                @foreach($races as $race)
                                    <option value="{{ $race->race_description }}" {{ $sp_ethnic_group == $race->race_description ? 'selected' : '' }}>{{ $race->race_description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sp_disability" class="form-label">Disability</label>
                            <select class="sd_drop_class" style="width: 100%" name="sp_disability" id="sp_disability" {{ $readonly ? 'disabled' : '' }}>
                                <option value="0" {{ $sp_disability == '0' ? 'selected' : '' }}>Not Disabled</option>
                                <option value="1" {{ $sp_disability == '1' ? 'selected' : '' }}>Disabled</option>
                                <option value="other" {{ $sp_disability == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Spouse personal details -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_title" class="form-label">Title</label>
                            <select class="sd_drop_class" style="width: 100%" name="sp_title" id="sp_title" {{ $readonly ? 'disabled' : '' }}>
                                <option value="Mr" {{ $sp_title == 'Mr' ? 'selected' : '' }}>Mr</option>
                                <option value="Ms" {{ $sp_title == 'Ms' ? 'selected' : '' }}>Ms</option>
                                <option value="Mrs" {{ $sp_title == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                <option value="Dr" {{ $sp_title == 'Dr' ? 'selected' : '' }}>Dr</option>
                                <option value="Prof" {{ $sp_title == 'Prof' ? 'selected' : '' }}>Prof</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_initials" class="form-label">Initials</label>
                            <input type="text" name="sp_initials" id="sp_initials"
                                   class="form-control" value="{{ e($sp_initials) }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_tax_number" class="form-label">Income Tax Number</label>
                            <input type="text" name="sp_tax_number" id="sp_tax_number"
                                   class="form-control sd_highlight num10digit" value="{{ e($sp_tax_number) }}"
                                   maxlength="12"                                    {{ $readonly ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_known_as" class="form-label">Known As</label>
                            <input type="text" name="sp_known_as" id="sp_known_as"
                                   class="form-control" value="{{ e($sp_known_as) }}"
                                   {{ $readonly ? 'readonly' : '' }}>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sp_firstname" class="form-label">First Name</label>
                            <input type="text" name="sp_firstname" id="sp_firstname"
                                   class="form-control sp_first_init" value="{{ e($sp_firstname) }}"
                                   {{ $readonly ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sp_middlename" class="form-label">Middle Name</label>
                            <input type="text" name="sp_middlename" id="sp_middlename"
                                   class="form-control sp_middle_init" value="{{ e($sp_middlename) }}"
                                   {{ $readonly ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="sp_surname" class="form-label">Surname</label>
                            <input type="text" name="sp_surname" id="sp_surname"
                                   class="form-control" value="{{ e($sp_surname) }}"
                                   {{ $readonly ? 'readonly' : '' }}>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_mobile_phone" class="form-label">Mobile</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                <input type="tel" name="sp_mobile_phone" id="sp_mobile_phone"
                                       class="form-control num10digit" value="{{ e($sp_mobile_phone) }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_whatsapp_number" class="form-label">Whatsapp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                <input type="text" name="sp_whatsapp_number" id="sp_whatsapp_number"
                                       class="form-control num10digit" value="{{ e($sp_whatsapp_number) }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_office_phone" class="form-label">Office</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                <input type="tel" name="sp_office_phone" id="sp_office_phone"
                                       class="form-control num10digit" value="{{ e($sp_office_phone) }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="sp_other_phone" class="form-label">Other</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                <input type="tel" name="sp_other_phone" id="sp_other_phone"
                                       class="form-control num10digit" value="{{ e($sp_other_phone) }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sp_email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                <input type="email" name="sp_email" id="sp_email"
                                       class="form-control email_validation" value="{{ e($sp_email) }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sp_accounts_email" class="form-label">Accounts Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                <input type="email" name="sp_accounts_email" id="sp_accounts_email"
                                       class="form-control email_validation" value="{{ e($sp_accounts_email) }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADDRESS DETAILS -->
        <div class="card smartdash-form-card person-section-card">
            <div class="card-header">
                <h4><i class="fa fa-map-marker-alt"></i> ADDRESS DETAILS</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="autocompleteInput" class="form-label">Search Address (Google)</label>
                                    <input type="text" id="autocompleteInput" class="form-control"
                                           placeholder="Type to search for an address..."
                                           {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="complex_name" class="form-label">Complex / Building Name</label>
                                    <input type="text" name="complex_name" id="complex_name"
                                           class="form-control" value="{{ e($complex_name) }}"
                                           {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address_line" class="form-label">Street Address</label>
                                    <input type="text" name="address_line" id="address_line"
                                           class="form-control" value="{{ e($address_line) }}"
                                           {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="suburb" class="form-label">Suburb</label>
                                    <input type="text" name="suburb" id="suburb"
                                           class="form-control" value="{{ e($suburb) }}"
                                           {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" name="city" id="city"
                                           class="form-control" value="{{ e($city) }}"
                                           {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" name="postal_code" id="postal_code"
                                           class="form-control" value="{{ e($postal_code) }}"
                                           {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="province" class="form-label">Province</label>
                                    <select name="province" id="province" class="form-control" {{ $readonly ? 'disabled' : '' }}>
                                        <option value="">-- Select Province --</option>
                                        @foreach($provinces as $p)
                                            <option value="{{ $p }}" {{ $province == $p ? 'selected' : '' }}>{{ $p }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="address_country" class="form-label">Country</label>
                                    <input type="text" name="address_country" id="address_country"
                                           class="form-control" value="{{ e($address_country) }}"
                                           {{ $readonly ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="text" name="latitude" id="latitude"
                                           class="form-control" value="{{ e($latitude) }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="text" name="longitude" id="longitude"
                                           class="form-control" value="{{ e($longitude) }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div id="map" style="height: 300px; border: 1px solid #ddd; border-radius: 8px;"></div>
                        <a href="#" id="googleMapLink" target="_blank" class="btn btn-outline-primary mt-2">
                            <i class="fa fa-map"></i> Open in Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- BANKING DETAILS -->
        <div class="card smartdash-form-card person-section-card">
            <div class="card-header">
                <h4><i class="fa fa-university"></i> BANKING DETAILS</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bank_account_holder" class="form-label">Account Holder Name</label>
                            <input type="text" name="bank_account_holder" id="bank_account_holder"
                                   class="form-control" value="{{ e($bank_account_holder) }}"
                                   {{ $readonly ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <select name="bank_name" id="bank_name" class="form-control" {{ $readonly ? 'disabled' : '' }}>
                                <option value="">-- Select Bank --</option>
                                @foreach($banks ?? [] as $bank)
                                    <option value="{{ $bank->bank_name }}"
                                            data-branch="{{ $bank->branch_code ?? '' }}"
                                            data-swift="{{ $bank->swift_code ?? '' }}"
                                            {{ $bank_name == $bank->bank_name ? 'selected' : '' }}>{{ $bank->bank_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bank_branch" class="form-label">Branch Code</label>
                            <input type="text" name="bank_branch" id="bank_branch"
                                   class="form-control" value="{{ e($bank_branch) }}"
                                   {{ $readonly ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bank_account_number" class="form-label">Account Number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number"
                                   class="form-control" value="{{ e($bank_account_number) }}"
                                   {{ $readonly ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bank_account_type" class="form-label">Account Type</label>
                            <select name="bank_account_type" id="bank_account_type" class="form-control" {{ $readonly ? 'disabled' : '' }}>
                                <option value="">-- Select --</option>
                                <option value="Cheque" {{ $bank_account_type == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                <option value="Savings" {{ $bank_account_type == 'Savings' ? 'selected' : '' }}>Savings</option>
                                <option value="Transmission" {{ $bank_account_type == 'Transmission' ? 'selected' : '' }}>Transmission</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bank_swift_code" class="form-label">Swift Code</label>
                            <input type="text" name="bank_swift_code" id="bank_swift_code"
                                   class="form-control" value="{{ e($bank_swift_code) }}"
                                   {{ $readonly ? 'readonly' : '' }}>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bank_account_status" class="form-label">Account Status</label>
                            <select name="bank_account_status" id="bank_account_status" class="form-control" {{ $readonly ? 'disabled' : '' }}>
                                <option value="">-- Select --</option>
                                <option value="Active" {{ $bank_account_status == 'Active' ? 'selected' : '' }}>Active</option>
                                <option value="Closed" {{ $bank_account_status == 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bank_date_opened" class="form-label">Date Opened</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <input type="text" class="form-control datepicker-past" name="bank_date_opened" id="bank_date_opened"
                                       class="form-control" value="{{ $bank_date_opened }}"
                                       {{ $readonly ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PHOTO GALLERY & SIGNATURE -->
        <div class="card smartdash-form-card person-section-card">
            <div class="card-header">
                <h4><i class="fa fa-images"></i> DOCUMENTS & SIGNATURE</h4>
            </div>
            <div class="card-body">
                <!-- Photo Gallery Slider -->
                <div class="photo-slider-container">
                    <button type="button" class="slider-arrow slider-prev" onclick="slidePhotos(-1)">
                        <i class="fa fa-chevron-left"></i>
                    </button>

                    <div class="photo-slider-wrapper">
                        <div class="photo-slider-track" id="photoSliderTrack">
                            <!-- Profile Photo -->
                            <div class="photo-slide">
                                <div class="photo-frame-box" data-photo-type="profile" onclick="zoomPhoto(this)">
                                    @if(!empty($profile_picture))
                                        <img src="/storage/{{ $profile_picture }}" alt="Profile">
                                    @else
                                        <div class="no-photo"><i class="fa fa-user"></i><span>No image available</span></div>
                                    @endif
                                </div>
                                <div class="photo-frame-label">PROFILE</div>
                            </div>

                            <!-- Smart Card Front -->
                            <div class="photo-slide">
                                <div class="photo-frame-box smart-card-box" data-photo-type="id_front" onclick="zoomPhoto(this)">
                                    @if(!empty($id_front_image))
                                        <img src="/storage/{{ $id_front_image }}" alt="ID Front">
                                    @else
                                        <div class="no-photo"><i class="fa fa-id-card"></i><span>Front</span></div>
                                    @endif
                                </div>
                                <div class="photo-frame-label">ID FRONT</div>
                            </div>

                            <!-- Smart Card Back -->
                            <div class="photo-slide">
                                <div class="photo-frame-box smart-card-box" data-photo-type="id_back" onclick="zoomPhoto(this)">
                                    @if(!empty($id_back_image))
                                        <img src="/storage/{{ $id_back_image }}" alt="ID Back">
                                    @else
                                        <div class="no-photo"><i class="fa fa-id-card"></i><span>Back</span></div>
                                    @endif
                                </div>
                                <div class="photo-frame-label">ID BACK</div>
                            </div>

                            <!-- Green Book -->
                            <div class="photo-slide">
                                <div class="photo-frame-box" data-photo-type="green_book" onclick="zoomPhoto(this)">
                                    @if(!empty($green_book_image))
                                        <img src="/storage/{{ $green_book_image }}" alt="Green Book">
                                    @else
                                        <div class="no-photo"><i class="fa fa-book"></i><span>No image available</span></div>
                                    @endif
                                </div>
                                <div class="photo-frame-label">GREEN BOOK</div>
                            </div>

                            <!-- Update -->
                            <div class="photo-slide">
                                <div class="photo-frame-box" data-photo-type="update_sars" onclick="zoomPhoto(this)">
                                    @if(!empty($update_image))
                                        <img src="/storage/{{ $update_image }}" alt="Update">
                                    @else
                                        <div class="no-photo"><i class="fa fa-file-alt"></i><span>No image available</span></div>
                                    @endif
                                </div>
                                <div class="photo-frame-label">UPDATE</div>
                            </div>

                            <!-- Passport -->
                            <div class="photo-slide">
                                <div class="photo-frame-box" data-photo-type="passport" onclick="zoomPhoto(this)">
                                    @if(!empty($passport_image))
                                        <img src="/storage/{{ $passport_image }}" alt="Passport">
                                    @else
                                        <div class="no-photo"><i class="fa fa-passport"></i><span>No image available</span></div>
                                    @endif
                                </div>
                                <div class="photo-frame-label">PASSPORT</div>
                            </div>

                            <!-- POA (Proof of Address) -->
                            <div class="photo-slide">
                                <div class="photo-frame-box" data-photo-type="poa" onclick="zoomPhoto(this)">
                                    @if(!empty($poa_image))
                                        <img src="/storage/{{ $poa_image }}" alt="POA">
                                    @else
                                        <div class="no-photo"><i class="fa fa-home"></i><span>No image available</span></div>
                                    @endif
                                </div>
                                <div class="photo-frame-label">POA</div>
                            </div>

                            <!-- Banking -->
                            <div class="photo-slide">
                                <div class="photo-frame-box" data-photo-type="banking" onclick="zoomPhoto(this)">
                                    @if(!empty($banking_image))
                                        <img src="/storage/{{ $banking_image }}" alt="Banking">
                                    @else
                                        <div class="no-photo"><i class="fa fa-university"></i><span>No image available</span></div>
                                    @endif
                                </div>
                                <div class="photo-frame-label">BANKING</div>
                            </div>

                            <!-- Signature -->
                            <div class="photo-slide">
                                <div class="photo-frame-box signature-frame-box" data-photo-type="signature" onclick="zoomPhoto(this)">
                                    @if(!empty($signature_image))
                                        <img src="/storage/{{ $signature_image }}" alt="Signature" id="signatureImg">
                                    @else
                                        <div class="no-photo"><i class="fa fa-signature"></i><span>No signature</span></div>
                                    @endif
                                </div>
                                <div class="photo-frame-label">SIGNATURE</div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="slider-arrow slider-next" onclick="slidePhotos(1)">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                </div>

                <!-- Pill Buttons Row 1 - Photos (JPG) -->
                <div class="photo-buttons-row d-flex flex-wrap justify-content-center gap-2 mb-2 mt-3">
                    <button type="button" class="btn photo-pill-btn" onclick="triggerPhotoUpload('profile')">Profile</button>
                    <button type="button" class="btn photo-pill-btn" onclick="triggerPhotoUpload('id_front')">Smart Card Front</button>
                    <button type="button" class="btn photo-pill-btn" onclick="triggerPhotoUpload('id_back')">Smart Card Back</button>
                    <button type="button" class="btn photo-pill-btn" onclick="triggerPhotoUpload('green_book')">Green Book ID</button>
                    <button type="button" class="btn photo-pill-btn" onclick="triggerPhotoUpload('update_sars')">Update My Details</button>
                    <button type="button" class="btn photo-pill-btn" onclick="openSignaturePad()">Draw Signature</button>
                    <button type="button" class="btn photo-pill-btn" onclick="document.getElementById('upload_signature').click()">Upload Signature</button>
                </div>
                <!-- Pill Buttons Row 2 - Documents (PDF) -->
                <div class="photo-buttons-row d-flex flex-wrap justify-content-center gap-2 mb-4">
                    <button type="button" class="btn photo-pill-btn" onclick="triggerDocUpload('passport')">Passport</button>
                    <button type="button" class="btn photo-pill-btn" onclick="triggerDocUpload('poa')">POA</button>
                    <button type="button" class="btn photo-pill-btn" onclick="triggerDocUpload('banking')">Banking</button>
                </div>

                <!-- Hidden file inputs for photos (images only) -->
                <input type="file" id="upload_profile" accept="image/*" style="display:none;" onchange="previewPhoto(this, 'profile')">
                <input type="file" id="upload_id_front" accept="image/*" style="display:none;" onchange="previewPhoto(this, 'id_front')">
                <input type="file" id="upload_id_back" accept="image/*" style="display:none;" onchange="previewPhoto(this, 'id_back')">
                <input type="file" id="upload_green_book" accept="image/*" style="display:none;" onchange="previewPhoto(this, 'green_book')">
                <input type="file" id="upload_update_sars" accept="image/*" style="display:none;" onchange="previewPhoto(this, 'update_sars')">

                <!-- Hidden file inputs for documents (PDF or images) -->
                <input type="file" id="upload_passport" accept="image/*,.pdf" style="display:none;" onchange="previewDocument(this, 'passport')">
                <input type="file" id="upload_poa" accept="image/*,.pdf" style="display:none;" onchange="previewDocument(this, 'poa')">
                <input type="file" id="upload_banking" accept="image/*,.pdf" style="display:none;" onchange="previewDocument(this, 'banking')">

                <!-- Hidden file input for signature upload -->
                <input type="file" id="upload_signature" accept="image/*" style="display:none;" onchange="uploadSignature(this)">

                <!-- Hidden signature data field -->
                <input type="hidden" name="signature_data" id="signatureData" value="">
            </div>
        </div>

        <!-- NOTES -->
        <div class="card smartdash-form-card person-section-card">
            <div class="card-header">
                <h4><i class="fa fa-sticky-note"></i> NOTES</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <textarea name="notes" id="notes" class="form-control" rows="4"
                                      {{ $readonly ? 'readonly' : '' }}>{{ e($notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        @if(!$readonly)
            <div class="card smartdash-form-card">
                <div class="card-body">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn sd_btn" id="submitForm">
                            <i class="fa fa-save"></i> Save Person
                        </button>
                        <a href="{{ route('cimspersons.index') }}" class="btn sd_btn_secondary">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="card smartdash-form-card">
                <div class="card-body">
                    <a href="{{ route('cimspersons.edit', $id) }}" class="btn sd_btn">
                        <i class="fa fa-pencil"></i> Edit Person
                    </a>
                </div>
            </div>
        @endif
    </form>
</div>

<!-- Photo Zoom Modal -->
<div class="modal fade photo-zoom-modal" id="photoZoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoZoomTitle">Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img id="photoZoomImage" src="" alt="Zoomed Photo">
            </div>
        </div>
    </div>
</div>

<!-- Signature Pad Modal -->
<div class="modal fade" id="signaturePadModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-pen"></i> Draw Your Signature</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <canvas id="signatureCanvas" width="400" height="200"></canvas>
                <p class="text-muted mt-2"><small>Draw your signature above using mouse or finger</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="clearSignatureCanvas()">
                    <i class="fa fa-eraser"></i> Clear
                </button>
                <button type="button" class="btn sd_btn" onclick="saveSignature()">
                    <i class="fa fa-check"></i> Save Signature
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Webcam Modal -->
<div class="modal fade" id="webcamModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-camera"></i> <span id="webcamTitle">Capture Photo</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="resetWebcam()"></button>
            </div>
            <div class="modal-body text-center">
                <div id="my_camera" style="width: 400px; height: 300px; margin: 0 auto;"></div>
                <p class="text-muted mt-2"><small>For portrait photos, position person/document vertically in frame or use Upload</small></p>
                <input type="file" id="webcamUploadInput" accept="image/*" style="display: none;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="uploadPhotoInstead()">
                    <i class="fa fa-upload"></i> Upload Instead
                </button>
                <button type="button" class="btn sd_btn" onclick="snapPhoto()">
                    <i class="fa fa-camera"></i> Snap Photo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/public/smartdash/vendor/sweetalert2/sweetalert2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js"></script>
<script src="/public/smartdash/js/smartdash-dates.js"></script>
<script src="/public/smartdash/js/smartdash-utils.js"></script>
<script>
$(document).ready(function() {
    var isEdit = {{ $is_edit ? 'true' : 'false' }};
    var personId = '{{ $id }}';

    // Initialize global date configuration (SmartDashDates handles class-based initialization)
    // Classes used: .datepicker-past, .datepicker-future, .datepicker-any
    SmartDashDates.init();

    // Initialize Bootstrap-Select on all dropdowns with sd_drop_class
    if ($.fn.selectpicker) {
        $('select.sd_drop_class').selectpicker({
            liveSearch: true,
            size: 10
        });
    }

    // Initialize Parsley on the form
    $('#personForm').parsley({
        triggerAfterFailure: 'change keyup focusout'
    });

    // ========== CITIZENSHIP / IDENTITY TYPE TOGGLE ==========
    $('#citizenship').on('change', function() {
        var val = $(this).val();
        if (val === 'NON S A CITIZEN') {
            // Hide Green Book and Smart Card options, show only Passport
            $('#identity_type option[value="GREEN BOOK"]').hide();
            $('#identity_type option[value="SMART CARD"]').hide();
            $('#identity_type option[value="PASSPORT"]').show();
            $('#identity_type').val('PASSPORT').trigger('change');

            // Country: Enable dropdown, hide South Africa option
            $('#country').prop('disabled', false);
            $('#country option').each(function() {
                if ($(this).val().toLowerCase() === 'south africa') {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
            // Clear country if it was South Africa
            if ($('#country').val() && $('#country').val().toLowerCase() === 'south africa') {
                $('#country').val('').trigger('change');
            }
            // Refresh selectpicker if used
            if ($.fn.selectpicker) {
                $('#country').selectpicker('refresh');
            }
            $('#nationality').val('').prop('readonly', false);
            $('#country_code').val('');
        } else {
            // South African: Set country to South Africa and make readonly
            $('#identity_type option[value="GREEN BOOK"]').show();
            $('#identity_type option[value="SMART CARD"]').show();
            $('#identity_type option[value="PASSPORT"]').hide();
            if ($('#identity_type').val() === 'PASSPORT') {
                $('#identity_type').val('GREEN BOOK').trigger('change');
            }

            // Country: Set to South Africa and disable
            $('#country option').show(); // Show all first
            $('#country').val('South Africa');
            $('#country').prop('disabled', true);
            // Refresh selectpicker if used
            if ($.fn.selectpicker) {
                $('#country').selectpicker('refresh');
            }
            $('#nationality').val('South African').prop('readonly', true);
            $('#country_code').val('ZAF');
        }
    });

    // Trigger on page load to set initial state
    $('#citizenship').trigger('change');

    $('#identity_type').on('change', function() {
        var val = $(this).val();
        if (val === 'PASSPORT') {
            $('#identityRow').hide();
            $('#passportRow').show();
            $('#saGenderRow').hide();
            $('#passportDemographicsRow').show();
            // Update section title to PASSPORT DETAILS
            $('#identityDetailsTitleText').text('PASSPORT DETAILS');
            $('#identityDetailsIcon').removeClass('fa-id-card').addClass('fa-passport');
            // Hide all ID-related buttons for passport
            $('#btnIdentity').hide();
            $('#btnIdFront').hide();
            $('#btnIdBack').hide();
        } else if (val === 'GREEN BOOK') {
            $('#identityRow').show();
            $('#passportRow').hide();
            $('#saGenderRow').show();
            $('#passportDemographicsRow').hide();
            // Update section title back to IDENTITY DETAILS
            $('#identityDetailsTitleText').text('IDENTITY DETAILS');
            $('#identityDetailsIcon').removeClass('fa-passport').addClass('fa-id-card');
            // GREEN BOOK: Show Identity button, hide ID Front and ID Back
            $('#btnIdentity').show();
            $('#btnIdFront').hide();
            $('#btnIdBack').hide();
        } else if (val === 'SMART CARD') {
            $('#identityRow').show();
            $('#passportRow').hide();
            $('#saGenderRow').show();
            $('#passportDemographicsRow').hide();
            // Update section title back to IDENTITY DETAILS
            $('#identityDetailsTitleText').text('IDENTITY DETAILS');
            $('#identityDetailsIcon').removeClass('fa-passport').addClass('fa-id-card');
            // SMART CARD: Hide Identity button, show ID Front and ID Back
            $('#btnIdentity').hide();
            $('#btnIdFront').show();
            $('#btnIdBack').show();
        }
    });

    // Trigger identity_type change on page load to set correct button visibility
    $('#identity_type').trigger('change');

    // ========== PERSON STATUS - SHOW/HIDE DATE OF DEATH ==========
    $('#person_status').on('change', function() {
        if ($(this).val() === 'Deceased') {
            $('#date_of_death_container').show();
        } else {
            $('#date_of_death_container').hide();
        }
    });

    // ========== MARITAL STATUS - SHOW/HIDE SPOUSE SECTION ==========
    $('#marital_status').on('change', function() {
        var val = $(this).val();
        if (val === 'Married COP' || val === 'Married ANC') {
            $('#spouse_section').show();
            $('#marital_status_date_container').show();
        } else {
            $('#spouse_section').hide();
            $('#marital_status_date_container').hide();
        }
    });

    // ========== COUNTRY SELECT - AUTO-FILL CODE AND NATIONALITY ==========
    $('#country').on('change', function() {
        var selected = $(this).find('option:selected');
        $('#country_code').val(selected.data('code') || '');
        $('#nationality').val(selected.data('nlt') || '');
    });

    // ========== IDENTITY NUMBER VALIDATION (SA ID) ==========
    var debounceTimer;

    $(document).on('input focusout', '.identity_number', function() {
        var idNumber = $(this).val().replace(/\D/g, ''); // Remove non-numeric

        // Limit to 13 digits and format
        if (idNumber.length > 13) {
            idNumber = idNumber.substring(0, 13);
        }
        var formattedValue = idNumber.replace(/(\d{6})(\d{4})(\d{2})(\d{1})/, '$1 $2 $3 $4');
        $(this).val(formattedValue);

        var SavedThis = $(this);
        var p = $(this).data('parent');

        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            SavedThis.parsley().removeError('checkAuthentic');
            SavedThis.parsley().removeError('checkExists');

            var dob = false;

            if (idNumber.length == 13) {
                // Extract date from ID
                var tempDate = new Date(idNumber.substring(0, 2), idNumber.substring(2, 4) - 1, idNumber.substring(4, 6));
                dob = true;
                var id_date = tempDate.getDate();
                var id_month = tempDate.getMonth();
                var id_year = tempDate.getFullYear();

                if (id_year < (new Date()).getFullYear() - 100) {
                    id_year += 100;
                }

                var fullDate = id_year + '-' + ('0' + (id_month + 1)).slice(-2) + '-' + ('0' + id_date).slice(-2);

                // Validate date part
                if (!((tempDate.getYear() == idNumber.substring(0, 2)) && (id_month == idNumber.substring(2, 4) - 1) &&
                        (id_date == idNumber.substring(4, 6)))) {
                    SavedThis.parsley().addError('checkAuthentic', {
                        message: 'ID number does not appear to be authentic - date part not valid'
                    });
                    dob = false;
                }
            }

            if (dob) {
                const date_of_birth = new Date(fullDate);
                const options = {
                    weekday: 'short',
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                };
                const formattedDate = date_of_birth.toLocaleDateString('en-GB', options);

                // Extract gender from ID (digits 7-10)
                var genderCode = idNumber.substring(6, 10);
                var gender = parseInt(genderCode) < 5000 ? "Female" : "Male";

                if (p === "identity_section") {
                    $('#date_of_birth').val(formattedDate);
                    $('#gender').val(gender).change();
                }

                if (p === "identity_spouse") {
                    $('#sp_date_of_birth').val(formattedDate);
                    $('#sp_gender').val(gender).change();
                }

                // Check for duplicate
                $.ajax({
                    url: '{{ route("cimspersons.checkDuplicate") }}',
                    type: 'GET',
                    data: { identity_number: idNumber, exclude_id: personId },
                    dataType: 'json',
                    success: function(response) {
                        SavedThis.parsley().removeError('checkExists');
                        if (response.duplicate && response.person) {
                            SavedThis.parsley().addError('checkExists', {
                                message: 'This identity number already exists for: ' + response.person.name
                            });
                        }
                    }
                });
            } else if (idNumber.length > 0) {
                SavedThis.parsley().addError('checkAuthentic', {
                    message: 'ID number does not appear to be authentic - date part not valid'
                });

                if (p === "identity_section") {
                    $('#date_of_birth').val("");
                }
                if (p === "identity_spouse") {
                    $('#sp_date_of_birth').val("");
                }
            }
        }, 300);
    });

    // ========== SENTENCE CASE FOR NAME FIELDS ==========
    function toSentenceCase(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }

    // Apply sentence case on blur (when user leaves the field)
    $('#surname, #firstname, #middlename, #known_as, #sp_surname, #sp_firstname, #sp_middlename, #sp_known_as').on('blur', function() {
        var val = $(this).val();
        if (val) {
            $(this).val(toSentenceCase(val));
        }
    });

    // ========== AUTO-GENERATE INITIALS (with space between) ==========
    function updateInitials() {
        var first = $('#firstname').val() || '';
        var middle = $('#middlename').val() || '';
        var initialsArr = [];
        if (first) initialsArr.push(first.charAt(0).toUpperCase());
        if (middle) initialsArr.push(middle.charAt(0).toUpperCase());
        $('#initials').val(initialsArr.join(' '));
    }

    $('#firstname, #middlename').on('input', updateInitials);

    function updateSpouseInitials() {
        var first = $('#sp_firstname').val() || '';
        var middle = $('#sp_middlename').val() || '';
        var initialsArr = [];
        if (first) initialsArr.push(first.charAt(0).toUpperCase());
        if (middle) initialsArr.push(middle.charAt(0).toUpperCase());
        $('#sp_initials').val(initialsArr.join(' '));
    }

    $('#sp_firstname, #sp_middlename').on('input', updateSpouseInitials);

    // ========== BANK ACCOUNT NUMBER FORMATTING ==========
    $('#bank_account_number').on('input', function() {
        var numericValue = $(this).val().replace(/\D/g, '');
        if (numericValue.length > 14) {
            numericValue = numericValue.substring(0, 14);
        }
        var formattedValue = numericValue.replace(/(\d{2})/g, '$1 ').trim();
        $(this).val(formattedValue);
    });

    // ========== BANK NAME - AUTO-FILL BRANCH CODE & SWIFT CODE ==========
    $('#bank_name').on('change', function() {
        var selected = $(this).find('option:selected');
        var branchCode = selected.data('branch') || '';
        var swiftCode = selected.data('swift') || '';
        $('#bank_branch').val(branchCode);
        $('#bank_swift_code').val(swiftCode);
    });

    // ========== FORM SUBMISSION ==========
    $('#submitForm').on('click', function(e) {
        e.preventDefault();

        var form = $('#personForm');
        form.parsley().validate();

        if (form.parsley().isValid()) {
            // Temporarily enable disabled fields so they serialize
            var disabledFields = form.find(':disabled');
            disabledFields.prop('disabled', false);

            // Strip formatting (spaces) before submitting
            form.find('.num10digit').each(function() {
                var raw = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(raw);
            });
            // Strip identity number formatting
            form.find('.identity_number').each(function() {
                var raw = $(this).val().replace(/\D/g, '');
                $(this).val(raw);
            });
            // Strip bank account number formatting
            var bankAccNum = form.find('#bank_account_number');
            if (bankAccNum.length) {
                bankAccNum.val(bankAccNum.val().replace(/\D/g, ''));
            }

            var formData = form.serialize();

            // Re-disable fields and re-format values
            disabledFields.prop('disabled', true);
            if ($.fn.selectpicker) {
                disabledFields.filter('select.sd_drop_class').selectpicker('refresh');
            }
            form.find('.num10digit').each(function() {
                var raw = $(this).val();
                if (raw) {
                    $(this).val(SmartDashUtils.formatNum10Digit(raw));
                }
            });
            // Re-format identity numbers
            form.find('.identity_number').each(function() {
                var raw = $(this).val();
                if (raw) {
                    $(this).val(raw.replace(/(\d{6})(\d{4})(\d{2})(\d{1})/, '$1 $2 $3 $4'));
                }
            });
            // Re-format bank account number
            if (bankAccNum.length && bankAccNum.val()) {
                bankAccNum.val(bankAccNum.val().replace(/(\d{2})/g, '$1 ').trim());
            }

            var url = isEdit
                ? '{{ route("cimspersons.update", ":id") }}'.replace(':id', personId)
                : '{{ route("cimspersons.store") }}';
            var method = isEdit ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: method,
                data: formData,
                dataType: 'json',
                success: function(result) {
                    if (result.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Saved!',
                            text: 'Person has been saved successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = '{{ route("cimspersons.index") }}';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: result.error || 'Failed to save person.'
                        });
                    }
                },
                error: function(xhr) {
                    var msg = 'An error occurred. Please try again.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            msg = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            var errs = xhr.responseJSON.errors;
                            var errList = [];
                            for (var field in errs) {
                                errList.push(field + ': ' + errs[field].join(', '));
                            }
                            msg = errList.join('\n');
                        }
                    } else if (xhr.status === 419) {
                        msg = 'Session expired. Please refresh the page and try again.';
                    } else if (xhr.status === 500) {
                        msg = 'Server error (500). Please check the server logs.';
                    }
                    console.error('Save error:', xhr.status, xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error (' + xhr.status + ')',
                        text: msg
                    });
                }
            });
        } else {
            Swal.fire('Missing Information', 'Please complete all required fields.', 'info');
        }
    });

    // ========== SIMPLE PHOTO UPLOAD (using existing GrowCRM upload) ==========
    $('#btnUploadPhoto').on('click', function() {
        var fileInput = document.getElementById('photoFile');
        var file = fileInput.files[0];
        var photoType = $('#photoType').val();
        var statusDiv = $('#uploadStatus');

        if (!file) {
            statusDiv.html('<span class="text-danger">Please select a file first</span>');
            return;
        }

        // Show uploading status
        statusDiv.html('<span class="text-info"><i class="fa fa-spinner fa-spin"></i> Uploading...</span>');

        var formData = new FormData();
        formData.append('file', file);  // GrowCRM uses 'file' not 'image'
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            url: '/upload-general-image',  // Use general image upload - no restrictions
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    var imageUrl = '/storage/temp/' + response.directory + '/' + response.filename;
                    statusDiv.html('<span class="text-success"><i class="fa fa-check"></i> ' + photoType + ' uploaded!</span>');

                    // Add to carousel
                    var slideHtml = '<div class="carousel-item" data-title="' + photoType + '">' +
                        '<img src="' + imageUrl + '" class="d-block w-100" style="height: 300px; object-fit: contain;">' +
                        '</div>';
                    $('#carouselSlides').append(slideHtml);

                    // Store the path for saving with the person record
                    if (!window.personImages) window.personImages = {};
                    window.personImages[photoType] = response.directory + '/' + response.filename;

                    // Clear file input
                    fileInput.value = '';
                } else {
                    statusDiv.html('<span class="text-danger"><i class="fa fa-times"></i> Upload failed</span>');
                }
            },
            error: function(xhr) {
                var msg = 'Upload failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.status === 409) {
                    msg = 'Invalid file type or dimensions';
                }
                statusDiv.html('<span class="text-danger"><i class="fa fa-times"></i> ' + msg + '</span>');
            }
        });
    });
});

// ========== GOOGLE MAPS AUTOCOMPLETE ==========
function initAutocomplete() {
    var centerCoords = { lat: -26.2041, lng: 28.0473 };

    var map = new google.maps.Map(document.getElementById('map'), {
        center: centerCoords,
        zoom: 12
    });

    var marker = new google.maps.Marker({
        position: centerCoords,
        map: map,
        title: 'Location'
    });

    var autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('autocompleteInput'),
        { types: ['geocode'] }
    );

    autocomplete.addListener('place_changed', function() {
        var place = autocomplete.getPlace();
        if (!place.geometry) return;

        // Clear all address fields before filling with new data
        $('#complex_name').val('');
        $('#address_line').val('');
        $('#suburb').val('');
        $('#city').val('');
        $('#postal_code').val('');
        $('#province').val('');
        $('#address_country').val('');
        $('#latitude').val('');
        $('#longitude').val('');

        var addressComponents = place.address_components;

        addressComponents.forEach(function(component) {
            if (component.types.includes('street_number') || component.types.includes('route')) {
                var current = $('#address_line').val();
                $('#address_line').val((current ? current + ' ' : '') + component.long_name);
            } else if (component.types.includes('sublocality')) {
                $('#suburb').val(component.long_name);
            } else if (component.types.includes('locality')) {
                $('#city').val(component.long_name);
            } else if (component.types.includes('administrative_area_level_1')) {
                $('#province').val(component.long_name);
            } else if (component.types.includes('postal_code')) {
                $('#postal_code').val(component.long_name);
            } else if (component.types.includes('country')) {
                $('#address_country').val(component.long_name);
            }
        });

        var lat = place.geometry.location.lat();
        var lng = place.geometry.location.lng();
        $('#latitude').val(lat);
        $('#longitude').val(lng);
        $('#googleMapLink').attr('href', 'https://www.google.com/maps?q=' + lat + ',' + lng);

        map.setCenter(place.geometry.location);
        map.setZoom(15);
        marker.setPosition(place.geometry.location);
    });
}

function loadGoogleMapsScript() {
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDlFzdbBe7bMPm9jrCo6C8340ELKtsZjEw&libraries=places&callback=initAutocomplete';
    document.body.appendChild(script);
}

window.onload = loadGoogleMapsScript;

// ========== PHOTO SLIDER FUNCTIONALITY ==========
var sliderPosition = 0;
var slidesVisible = 4; // Show 4 slides at a time
var totalSlides = 7; // Total number of slides

function slidePhotos(direction) {
    var track = document.getElementById('photoSliderTrack');
    if (!track) return;

    var slides = track.querySelectorAll('.photo-slide');
    totalSlides = slides.length;

    // Calculate max position (how far we can slide)
    var maxPosition = Math.max(0, totalSlides - slidesVisible);

    // Update position
    sliderPosition += direction;

    // Clamp position within bounds
    if (sliderPosition < 0) sliderPosition = 0;
    if (sliderPosition > maxPosition) sliderPosition = maxPosition;

    // Get the actual width of a slide including gap
    var wrapper = document.querySelector('.photo-slider-wrapper');
    var firstSlide = slides[0];
    if (wrapper && firstSlide) {
        var slideWidth = firstSlide.offsetWidth + 15; // 15px gap
        var translateX = sliderPosition * slideWidth;
        track.style.transform = 'translateX(-' + translateX + 'px)';
    }

    // Update arrow states
    updateSliderArrows();
}

function updateSliderArrows() {
    var prevBtn = document.querySelector('.slider-prev');
    var nextBtn = document.querySelector('.slider-next');
    var maxPosition = Math.max(0, totalSlides - slidesVisible);

    if (prevBtn) {
        prevBtn.disabled = sliderPosition <= 0;
    }
    if (nextBtn) {
        nextBtn.disabled = sliderPosition >= maxPosition;
    }
}

// Initialize slider arrows on page load
// ========== PHOTO UPLOAD FUNCTIONALITY ==========
var currentPhotoType = '';

function triggerPhotoUpload(photoType) {
    currentPhotoType = photoType;
    var input = document.getElementById('upload_' + photoType);
    if (input) {
        input.click();
    }
}

function previewPhoto(input, photoType) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            // Find the frame box for this photo type
            var frameBox = document.querySelector('.photo-frame-box[data-photo-type="' + photoType + '"]');
            if (frameBox) {
                // Check if there's already an image, or create one
                var img = frameBox.querySelector('img');
                if (!img) {
                    // Remove the no-photo placeholder
                    var noPhoto = frameBox.querySelector('.no-photo');
                    if (noPhoto) {
                        noPhoto.remove();
                    }
                    // Create new image
                    img = document.createElement('img');
                    frameBox.appendChild(img);
                }
                img.src = e.target.result;
                img.alt = photoType.replace(/_/g, ' ');

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Photo Added',
                    text: 'Photo has been added. Click Save Person to save it.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Trigger document upload (for PDF documents)
function triggerDocUpload(docType) {
    currentPhotoType = docType;
    var input = document.getElementById('upload_' + docType);
    if (input) {
        input.click();
    }
}

// Store PDF blob URLs for viewing
var pdfBlobUrls = {};

// Preview document (handles both images and PDFs)
function previewDocument(input, docType) {
    if (input.files && input.files[0]) {
        var file = input.files[0];
        var isPdf = file.type === 'application/pdf';
        var frameBox = document.querySelector('.photo-frame-box[data-photo-type="' + docType + '"]');

        if (frameBox) {
            // Remove existing content
            var noPhoto = frameBox.querySelector('.no-photo');
            if (noPhoto) noPhoto.remove();
            var existingImg = frameBox.querySelector('img');
            if (existingImg) existingImg.remove();
            var existingPdf = frameBox.querySelector('.pdf-indicator');
            if (existingPdf) existingPdf.remove();

            if (isPdf) {
                // Create blob URL for viewing
                var blobUrl = URL.createObjectURL(file);
                pdfBlobUrls[docType] = blobUrl;

                // Show clickable PDF indicator
                var pdfDiv = document.createElement('div');
                pdfDiv.className = 'no-photo pdf-indicator';
                pdfDiv.style.cursor = 'pointer';
                pdfDiv.innerHTML = '<i class="fa fa-file-pdf" style="color:#dc3545; font-size: 50px;"></i><span style="font-size:11px; word-break:break-all;">' + file.name + '</span><br><small style="color:#17A2B8;">Click to view</small>';
                pdfDiv.onclick = function(e) {
                    e.stopPropagation();
                    window.open(blobUrl, '_blank');
                };
                frameBox.appendChild(pdfDiv);

                // Override the zoom click for PDF
                frameBox.onclick = function(e) {
                    window.open(blobUrl, '_blank');
                };

                Swal.fire({
                    icon: 'success',
                    title: 'PDF Added',
                    text: file.name + ' has been added. Click the frame to view it.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                // It's an image, preview it
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = docType.replace(/_/g, ' ');
                    frameBox.appendChild(img);

                    // Restore normal zoom behavior for images
                    frameBox.onclick = function() { zoomPhoto(frameBox); };

                    Swal.fire({
                        icon: 'success',
                        title: 'Document Added',
                        text: 'Document has been added. Click Save Person to save it.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                };
                reader.readAsDataURL(file);
            }
        }
    }
}
$(document).ready(function() {
    updateSliderArrows();
});

// ========== PHOTO ZOOM FUNCTIONALITY ==========
function zoomPhoto(element) {
    var img = element.querySelector('img');
    if (!img) {
        Swal.fire('No Photo', 'No photo available to zoom.', 'info');
        return;
    }

    var photoType = element.getAttribute('data-photo-type') || 'Photo';
    var title = photoType.replace(/_/g, ' ').toUpperCase();

    $('#photoZoomTitle').text(title);
    $('#photoZoomImage').attr('src', img.src);

    var modal = new bootstrap.Modal(document.getElementById('photoZoomModal'));
    modal.show();
}

// ========== SIGNATURE PAD FUNCTIONALITY ==========
var signatureCanvas, signatureCtx, isDrawing = false;

function initSignaturePad() {
    signatureCanvas = document.getElementById('signatureCanvas');
    if (!signatureCanvas) return;

    signatureCtx = signatureCanvas.getContext('2d');
    signatureCtx.strokeStyle = '#000';
    signatureCtx.lineWidth = 2;
    signatureCtx.lineCap = 'round';
    signatureCtx.lineJoin = 'round';

    // Mouse events
    signatureCanvas.addEventListener('mousedown', startDrawing);
    signatureCanvas.addEventListener('mousemove', draw);
    signatureCanvas.addEventListener('mouseup', stopDrawing);
    signatureCanvas.addEventListener('mouseout', stopDrawing);

    // Touch events for mobile
    signatureCanvas.addEventListener('touchstart', function(e) {
        e.preventDefault();
        var touch = e.touches[0];
        var mouseEvent = new MouseEvent('mousedown', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        signatureCanvas.dispatchEvent(mouseEvent);
    });

    signatureCanvas.addEventListener('touchmove', function(e) {
        e.preventDefault();
        var touch = e.touches[0];
        var mouseEvent = new MouseEvent('mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        signatureCanvas.dispatchEvent(mouseEvent);
    });

    signatureCanvas.addEventListener('touchend', function(e) {
        var mouseEvent = new MouseEvent('mouseup', {});
        signatureCanvas.dispatchEvent(mouseEvent);
    });
}

function startDrawing(e) {
    isDrawing = true;
    var rect = signatureCanvas.getBoundingClientRect();
    signatureCtx.beginPath();
    signatureCtx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
}

function draw(e) {
    if (!isDrawing) return;
    var rect = signatureCanvas.getBoundingClientRect();
    signatureCtx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    signatureCtx.stroke();
}

function stopDrawing() {
    isDrawing = false;
}

function openSignaturePad() {
    var modal = new bootstrap.Modal(document.getElementById('signaturePadModal'));
    modal.show();

    // Initialize canvas after modal is shown
    setTimeout(initSignaturePad, 300);
}

function clearSignatureCanvas() {
    if (signatureCtx) {
        signatureCtx.clearRect(0, 0, signatureCanvas.width, signatureCanvas.height);
    }
}

function saveSignature() {
    if (!signatureCanvas) return;

    // Check if canvas is empty
    var blank = document.createElement('canvas');
    blank.width = signatureCanvas.width;
    blank.height = signatureCanvas.height;

    if (signatureCanvas.toDataURL() === blank.toDataURL()) {
        Swal.fire('Empty Signature', 'Please draw your signature first.', 'warning');
        return;
    }

    // Get signature as base64 PNG
    var signatureData = signatureCanvas.toDataURL('image/png');

    // Update hidden field
    $('#signatureData').val(signatureData);

    // Update the signature frame in the slider
    var frameBox = document.querySelector('.photo-frame-box[data-photo-type="signature"]');
    if (frameBox) {
        // Remove existing content
        var noPhoto = frameBox.querySelector('.no-photo');
        if (noPhoto) noPhoto.remove();
        var existingImg = frameBox.querySelector('img');
        if (existingImg) existingImg.remove();

        // Create new image
        var img = document.createElement('img');
        img.src = signatureData;
        img.alt = 'Signature';
        img.id = 'signatureImg';
        frameBox.appendChild(img);
    }

    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('signaturePadModal')).hide();

    Swal.fire({
        icon: 'success',
        title: 'Signature Saved',
        text: 'Your signature has been captured. Click Save Person to save it.',
        timer: 2000,
        showConfirmButton: false
    });
}

function uploadSignature(input) {
    if (!input.files || !input.files[0]) return;

    var file = input.files[0];

    // Validate file type
    if (!file.type.startsWith('image/')) {
        Swal.fire('Invalid File', 'Please select an image file.', 'error');
        return;
    }

    var reader = new FileReader();
    reader.onload = function(e) {
        var signatureData = e.target.result;

        // Update hidden field
        $('#signatureData').val(signatureData);

        // Update the signature frame in the slider
        var frameBox = document.querySelector('.photo-frame-box[data-photo-type="signature"]');
        if (frameBox) {
            var noPhoto = frameBox.querySelector('.no-photo');
            if (noPhoto) noPhoto.remove();
            var existingImg = frameBox.querySelector('img');
            if (existingImg) existingImg.remove();

            var img = document.createElement('img');
            img.src = signatureData;
            img.alt = 'Signature';
            img.id = 'signatureImg';
            frameBox.appendChild(img);
        }

        Swal.fire({
            icon: 'success',
            title: 'Signature Uploaded',
            text: 'Signature image has been loaded. Click Save Person to save it.',
            timer: 2000,
            showConfirmButton: false
        });
    };
    reader.readAsDataURL(file);

    // Clear input for next upload
    input.value = '';
}

function clearSignature() {
    Swal.fire({
        title: 'Clear Signature?',
        text: 'Are you sure you want to remove the signature?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, clear it'
    }).then(function(result) {
        if (result.isConfirmed) {
            $('#signatureData').val('');
            $('#signatureDisplay').html('<span class="no-signature">No signature available</span>');
        }
    });
}

// ========== WEBCAM FUNCTIONALITY ==========
var currentPhotoType = '';
var currentPhotoTitle = '';
var webcamInitialized = false;

function openWebcam(photoType, title) {
    currentPhotoType = photoType;
    currentPhotoTitle = title;
    $('#webcamTitle').text('Capture ' + title);

    var modal = new bootstrap.Modal(document.getElementById('webcamModal'));
    modal.show();

    // Initialize webcam after modal opens
    setTimeout(function() {
        if (typeof Webcam !== 'undefined') {
            // Standard webcam capture - landscape mode (webcams don't support portrait natively)
            Webcam.set({
                width: 400,
                height: 300,
                image_format: 'jpeg',
                jpeg_quality: 90
            });
            Webcam.attach('#my_camera');
            webcamInitialized = true;
        } else {
            $('#my_camera').html('<p class="text-danger">Webcam not available.<br>Use Upload Instead.</p>');
        }
    }, 500);
}

function snapPhoto() {
    if (!webcamInitialized || typeof Webcam === 'undefined') {
        Swal.fire('Webcam Error', 'Webcam is not available. Please use Upload Instead.', 'error');
        return;
    }

    Webcam.snap(function(data_uri) {
        // Update the photo frame
        updatePhotoFrame(currentPhotoType, data_uri, currentPhotoTitle);

        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('webcamModal')).hide();
        resetWebcam();

        Swal.fire({
            icon: 'success',
            title: 'Photo Captured!',
            text: currentPhotoTitle + ' has been captured.',
            timer: 1500,
            showConfirmButton: false
        });
    });
}

function uploadPhotoInstead() {
    $('#webcamUploadInput').click();
}

// Handle upload from webcam modal
$('#webcamUploadInput').on('change', function() {
    var file = this.files[0];
    if (!file) return;

    var reader = new FileReader();
    reader.onload = function(e) {
        updatePhotoFrame(currentPhotoType, e.target.result, currentPhotoTitle);

        bootstrap.Modal.getInstance(document.getElementById('webcamModal')).hide();
        resetWebcam();

        Swal.fire({
            icon: 'success',
            title: 'Photo Uploaded!',
            text: currentPhotoTitle + ' has been uploaded.',
            timer: 1500,
            showConfirmButton: false
        });
    };
    reader.readAsDataURL(file);
    this.value = '';
});

function updatePhotoFrame(photoType, dataUri, title) {
    var frameBox = $('[data-photo-type="' + photoType + '"]');
    frameBox.html('<img src="' + dataUri + '" alt="' + title + '">');

    // Store in hidden field or global object for saving
    if (!window.capturedPhotos) window.capturedPhotos = {};
    window.capturedPhotos[photoType] = dataUri;
}

function resetWebcam() {
    if (webcamInitialized && typeof Webcam !== 'undefined') {
        Webcam.reset();
        webcamInitialized = false;
    }
}

// Reset webcam when modal is closed
$('#webcamModal').on('hidden.bs.modal', function() {
    resetWebcam();
});
</script>
@endpush
