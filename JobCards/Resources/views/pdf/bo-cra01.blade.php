<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #333; }
    .page { padding: 30px 40px; }
    .sars-header {
        background: #1a1a2e; color: #fff; padding: 15px 20px; text-align: center;
        border-radius: 6px; margin-bottom: 20px;
    }
    .sars-header h1 { font-size: 16px; font-weight: 700; letter-spacing: 1px; }
    .sars-header h2 { font-size: 11px; font-weight: 400; margin-top: 4px; opacity: 0.8; }
    .sars-header .form-ref { font-size: 9px; margin-top: 6px; opacity: 0.6; }
    .section-title {
        background: #0d3d56; color: #fff; padding: 8px 14px; font-size: 11px;
        font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        border-radius: 4px; margin: 15px 0 8px;
    }
    .field-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
    .field-table td { padding: 5px 10px; border: 1px solid #dde4ec; font-size: 10px; vertical-align: middle; }
    .field-table td.label {
        background: #f8fafb; font-weight: 600; font-size: 9px; text-transform: uppercase;
        letter-spacing: 0.3px; color: #7f8c8d; width: 200px;
    }
    .field-table td.value { color: #333; }
    .notice {
        background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;
        padding: 10px 14px; margin: 15px 0; font-size: 9px; color: #856404;
    }
    .declaration {
        border: 2px solid #0d3d56; border-radius: 6px; padding: 15px 20px; margin: 20px 0;
    }
    .declaration p { font-size: 10px; line-height: 1.6; margin-bottom: 8px; }
    .sig-area { margin-top: 20px; }
    .sig-line { border-bottom: 1px solid #333; height: 40px; width: 300px; display: inline-block; position: relative; }
    .sig-line img { max-height: 35px; position: absolute; bottom: 2px; left: 0; }
    .sig-label { font-size: 9px; color: #7f8c8d; text-transform: uppercase; margin-top: 3px; }
    .date-field { display: inline-block; margin-left: 60px; }
    .page-break { page-break-before: always; }
    .footer { margin-top: 25px; text-align: center; font-size: 8px; color: #999; border-top: 1px solid #ddd; padding-top: 8px; }
</style>
</head>
<body>
@php
    $address = trim(implode(', ', array_filter([
        $director->person_address_line ?? $director->address_line ?? '',
        $director->person_address_line_2 ?? $director->address_line_2 ?? '',
    ])));
    $suburb = $director->person_suburb ?? $director->suburb ?? '';
    $city = $director->person_city ?? $director->city ?? '';
    $postalCode = $director->person_postal_code ?? $director->postal_code ?? '';
    $province = $director->person_province ?? $director->province ?? '';
    $country = $director->person_country ?? $director->address_country ?? 'South Africa';
    $mobile = $director->person_mobile ?? $director->mobile_phone ?? '';
    $email = $director->person_email ?? $director->email ?? '';
    $phone = $director->person_office_phone ?? $director->office_phone ?? '';
    $taxNum = $director->tax_number ?? '';
    $idNum = $director->identity_number ?? '';
@endphp

<!-- PAGE 1: Third Party Details (The Director) -->
<div class="page">
    <div class="sars-header">
        <h1>CRA01</h1>
        <h2>Confirmation of Residential or Business Address</h2>
        <div class="form-ref">South African Revenue Service — External Form</div>
    </div>

    <div class="notice">
        <strong>NOTE:</strong> This form is completed by the third party (the director/beneficial owner themselves)
        to confirm their residential address for the purposes of Proof of Address as required by CIPC
        for Beneficial Ownership filing.
    </div>

    <div class="section-title">Section A — Third Party Details (Confirming Person)</div>

    <table class="field-table">
        <tr><td class="label">Surname</td><td class="value"><strong>{{ $director->surname }}</strong></td></tr>
        <tr><td class="label">First Names</td><td class="value"><strong>{{ $director->firstname }}</strong></td></tr>
        <tr><td class="label">ID / Passport Number</td><td class="value">{{ $idNum }}</td></tr>
        <tr><td class="label">Income Tax Reference Number</td><td class="value">{{ $taxNum }}</td></tr>
        <tr><td class="label">Contact Number</td><td class="value">{{ $mobile ?: $phone }}</td></tr>
        <tr><td class="label">Email Address</td><td class="value">{{ $email }}</td></tr>
    </table>

    <div class="section-title">Section B — Address Being Confirmed</div>

    <table class="field-table">
        <tr><td class="label">Street / Unit Address</td><td class="value">{{ $address }}</td></tr>
        <tr><td class="label">Suburb</td><td class="value">{{ $suburb }}</td></tr>
        <tr><td class="label">City / Town</td><td class="value">{{ $city }}</td></tr>
        <tr><td class="label">Province / State</td><td class="value">{{ $province }}</td></tr>
        <tr><td class="label">Postal / Zip Code</td><td class="value">{{ $postalCode }}</td></tr>
        <tr><td class="label">Country</td><td class="value">{{ $country }}</td></tr>
    </table>

    <div class="section-title">Section C — Declaration</div>

    <div class="declaration">
        <p>
            I, <strong>{{ $director->firstname }} {{ $director->surname }}</strong>
            (ID No: <strong>{{ $idNum }}</strong>), hereby confirm that the address stated above
            is my true and correct residential/business address as at the date of this declaration.
        </p>
        <p>
            I understand that furnishing false information is an offence and may result in
            prosecution in terms of the Tax Administration Act, 2011 (Act No. 28 of 2011).
        </p>

        <div class="sig-area">
            <div style="display:inline-block;">
                <div class="sig-line">
                    @if($director->signature_image)
                        @php
                            $sigPath = base_path('../storage/' . $director->signature_image);
                            $sigBase64 = '';
                            if (file_exists($sigPath)) {
                                $ext = pathinfo($sigPath, PATHINFO_EXTENSION);
                                $sigBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($sigPath));
                            }
                        @endphp
                        @if($sigBase64)
                            <img src="{{ $sigBase64 }}" alt="Signature">
                        @endif
                    @endif
                </div>
                <div class="sig-label">Signature of Third Party</div>
            </div>
            <div class="date-field" style="display:inline-block;">
                <div class="sig-line" style="width:150px;">
                    <span style="position:absolute;bottom:5px;left:5px;font-size:11px;">{{ $today->format('d/m/Y') }}</span>
                </div>
                <div class="sig-label">Date</div>
            </div>
        </div>
    </div>

    <div class="footer">
        CRA01 — Confirmation of Residential or Business Address — Generated {{ $today->format('d F Y') }}
    </div>
</div>

<!-- PAGE 2: Taxpayer Details -->
<div class="page page-break">
    <div class="sars-header">
        <h1>CRA01 — Page 2</h1>
        <h2>Taxpayer Details</h2>
    </div>

    <div class="section-title">Section D — Taxpayer Details (Person whose address is being confirmed)</div>

    <table class="field-table">
        <tr><td class="label">Surname</td><td class="value"><strong>{{ $director->surname }}</strong></td></tr>
        <tr><td class="label">First Names</td><td class="value"><strong>{{ $director->firstname }}</strong></td></tr>
        <tr><td class="label">ID / Passport Number</td><td class="value">{{ $idNum }}</td></tr>
        <tr><td class="label">Income Tax Reference Number</td><td class="value">{{ $taxNum }}</td></tr>
        <tr><td class="label">Date of Birth</td><td class="value">{{ $director->date_of_birth ? \Carbon\Carbon::parse($director->date_of_birth)->format('d/m/Y') : '-' }}</td></tr>
    </table>

    <div class="section-title">Section E — Confirmed Address for Taxpayer</div>

    <table class="field-table">
        <tr><td class="label">Street / Unit Address</td><td class="value">{{ $address }}</td></tr>
        <tr><td class="label">Suburb</td><td class="value">{{ $suburb }}</td></tr>
        <tr><td class="label">City / Town</td><td class="value">{{ $city }}</td></tr>
        <tr><td class="label">Province / State</td><td class="value">{{ $province }}</td></tr>
        <tr><td class="label">Postal / Zip Code</td><td class="value">{{ $postalCode }}</td></tr>
        <tr><td class="label">Country</td><td class="value">{{ $country }}</td></tr>
    </table>

    <div class="section-title">Section F — Declaration by Taxpayer</div>

    <div class="declaration">
        <p>
            I, <strong>{{ $director->firstname }} {{ $director->surname }}</strong>,
            hereby confirm that the above address is my correct residential/business address
            and that the information contained herein is true and accurate.
        </p>

        <div class="sig-area">
            <div style="display:inline-block;">
                <div class="sig-line">
                    @if($director->signature_image)
                        @if($sigBase64)
                            <img src="{{ $sigBase64 }}" alt="Signature">
                        @endif
                    @endif
                </div>
                <div class="sig-label">Signature of Taxpayer</div>
            </div>
            <div class="date-field" style="display:inline-block;">
                <div class="sig-line" style="width:150px;">
                    <span style="position:absolute;bottom:5px;left:5px;font-size:11px;">{{ $today->format('d/m/Y') }}</span>
                </div>
                <div class="sig-label">Date</div>
            </div>
        </div>
    </div>

    <div class="footer">
        CRA01 — Confirmation of Residential or Business Address — Generated {{ $today->format('d F Y') }}
    </div>
</div>
</body>
</html>
