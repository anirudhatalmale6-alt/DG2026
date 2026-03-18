<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10px; color: #333; }
    .page { padding: 30px 40px; }
    .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #0d3d56; padding-bottom: 15px; }
    .header h1 { font-size: 18px; font-weight: 700; color: #0d3d56; text-transform: uppercase; letter-spacing: 1px; }
    .header .company-name { font-size: 14px; color: #17A2B8; font-weight: 600; margin-top: 6px; }
    .header .sub { font-size: 10px; color: #666; margin-top: 4px; }
    .logo { text-align: center; margin-bottom: 10px; }
    .logo img { max-height: 50px; }
    .owner-block { margin-bottom: 20px; border: 1px solid #dde4ec; border-radius: 6px; overflow: hidden; }
    .owner-header {
        background: #0d3d56; color: #fff; padding: 8px 14px; font-weight: 700;
        font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;
    }
    .owner-body { padding: 0; }
    .owner-row { display: table; width: 100%; border-bottom: 1px solid #eef2f7; }
    .owner-label {
        display: table-cell; width: 180px; padding: 6px 14px; background: #f8fafb;
        font-weight: 600; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px;
        color: #7f8c8d; vertical-align: middle; border-right: 1px solid #eef2f7;
    }
    .owner-value {
        display: table-cell; padding: 6px 14px; font-size: 10px; color: #333; vertical-align: middle;
    }
    .total-block {
        background: #0d3d56; color: #fff; padding: 12px 20px; border-radius: 6px;
        margin-top: 15px; text-align: center;
    }
    .total-block .total-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
    .total-block .total-value { font-size: 22px; font-weight: 700; margin-top: 4px; }
    .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
</style>
</head>
<body>
<div class="page">
    @if($logoBase64)
    <div class="logo"><img src="{{ $logoBase64 }}" alt="Logo"></div>
    @endif

    <div class="header">
        <h1>Register of Beneficial Owners</h1>
        <div class="company-name">{{ $client->company_name ?? '' }}</div>
        <div class="sub">
            Registration No: {{ $client->company_reg_number ?? 'N/A' }}
            &nbsp;&middot;&nbsp; Date: {{ $generatedAt->format('d F Y') }}
        </div>
    </div>

    @php $totalOwnership = 0; @endphp
    @foreach($directors as $i => $d)
    @php
        $shares = (int)($d->number_of_director_shares ?? 0);
        $pct = $totalShares > 0 ? round(($shares / $totalShares) * 100, 2) : 0;
        $totalOwnership += $pct;
        $address = trim(implode(', ', array_filter([
            $d->person_address_line ?? $d->address_line ?? '',
            $d->person_suburb ?? $d->suburb ?? '',
            $d->person_city ?? $d->city ?? '',
            $d->person_postal_code ?? $d->postal_code ?? '',
            $d->person_province ?? $d->province ?? '',
        ])));
        $country = $d->person_country ?? $d->address_country ?? 'South Africa';
    @endphp
    <div class="owner-block">
        <div class="owner-header">
            Beneficial Owner {{ $i + 1 }}: {{ $d->firstname }} {{ $d->surname }}
        </div>
        <div class="owner-body">
            <div class="owner-row">
                <div class="owner-label">Full Name</div>
                <div class="owner-value"><strong>{{ $d->firstname }} {{ $d->surname }}</strong></div>
            </div>
            <div class="owner-row">
                <div class="owner-label">Type</div>
                <div class="owner-value">{{ $d->director_type_name ?? 'Director' }}</div>
            </div>
            <div class="owner-row">
                <div class="owner-label">ID Number</div>
                <div class="owner-value">{{ $d->identity_number ?? '-' }}</div>
            </div>
            <div class="owner-row">
                <div class="owner-label">Income Tax Number</div>
                <div class="owner-value">{{ $d->tax_number ?? '-' }}</div>
            </div>
            <div class="owner-row">
                <div class="owner-label">Date Received</div>
                <div class="owner-value">{{ $d->appointment_date ? \Carbon\Carbon::parse($d->appointment_date)->format('d/m/Y') : '-' }}</div>
            </div>
            <div class="owner-row">
                <div class="owner-label">Email</div>
                <div class="owner-value">{{ $d->person_email ?? $d->email ?? '-' }}</div>
            </div>
            <div class="owner-row">
                <div class="owner-label">Interest Value</div>
                <div class="owner-value">{{ number_format($shares) }} {{ $shareType }} ({{ number_format($pct, 2) }}%)</div>
            </div>
            <div class="owner-row">
                <div class="owner-label">Phone</div>
                <div class="owner-value">{{ $d->person_mobile ?? $d->mobile_phone ?? '-' }}</div>
            </div>
            <div class="owner-row">
                <div class="owner-label">Interest Type</div>
                <div class="owner-value">{{ $shareType }}</div>
            </div>
            <div class="owner-row">
                <div class="owner-label">Residential Address</div>
                <div class="owner-value">{{ $address ?: '-' }}</div>
            </div>
            <div class="owner-row">
                <div class="owner-label">Country</div>
                <div class="owner-value">{{ $country }}</div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="total-block">
        <div class="total-label">Total Ownership</div>
        <div class="total-value">{{ number_format($totalOwnership, 2) }}%</div>
    </div>

    <div class="footer">
        {{ $companySettings['company_name'] }} &middot; CIPC Agent Code: {{ $agentCode }}
        &middot; Generated on {{ $generatedAt->format('d F Y') }}
    </div>
</div>
</body>
</html>
