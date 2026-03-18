<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 11px; color: #333; line-height: 1.6; }
    .page { padding: 40px 50px; }
    .header { text-align: center; margin-bottom: 30px; }
    .header h1 { font-size: 16px; font-weight: 700; color: #0d3d56; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .header h2 { font-size: 12px; font-weight: 600; color: #17A2B8; text-transform: uppercase; }
    .logo { text-align: center; margin-bottom: 15px; }
    .logo img { max-height: 50px; }
    .company-details {
        background: #f8fafb; border: 1px solid #eef2f7; border-radius: 6px;
        padding: 12px 18px; margin-bottom: 20px;
    }
    .company-details table { width: 100%; }
    .company-details td { padding: 3px 0; font-size: 10px; }
    .company-details td:first-child { font-weight: 600; color: #7f8c8d; width: 160px; text-transform: uppercase; font-size: 9px; }
    .company-details td:last-child { color: #333; }
    .resolution-body { margin: 20px 0; text-align: justify; }
    .resolution-body p { margin-bottom: 12px; }
    .resolution-body .clause { margin-left: 20px; margin-bottom: 10px; }
    .resolution-body .clause-num { font-weight: 700; color: #0d3d56; }
    .bold { font-weight: 700; }
    .signature-block { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 20px; }
    .sig-row { margin-bottom: 25px; }
    .sig-row .sig-label { font-size: 9px; text-transform: uppercase; color: #7f8c8d; font-weight: 600; }
    .sig-row .sig-line { border-bottom: 1px solid #333; height: 40px; margin-top: 5px; position: relative; }
    .sig-row .sig-line img { max-height: 35px; position: absolute; bottom: 2px; left: 0; }
    .sig-row .sig-name { font-size: 10px; margin-top: 4px; }
    .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
</style>
</head>
<body>
<div class="page">
    @if($logoBase64)
    <div class="logo"><img src="{{ $logoBase64 }}" alt="Logo"></div>
    @endif

    <div class="header">
        <h1>Ordinary Resolution</h1>
        <h2>Mandate to Lodge Beneficial Ownership</h2>
    </div>

    <div class="company-details">
        <table>
            <tr><td>Company Name:</td><td><strong>{{ $client->company_name ?? '' }}</strong></td></tr>
            <tr><td>Registration Number:</td><td>{{ $client->company_reg_number ?? 'N/A' }}</td></tr>
            <tr><td>Trading As:</td><td>{{ $client->trading_name ?? $client->company_name ?? '' }}</td></tr>
            <tr><td>Tax Number:</td><td>{{ $client->tax_number ?? 'N/A' }}</td></tr>
            <tr><td>CIPC Customer Code:</td><td><strong>{{ $agentCode }}</strong></td></tr>
        </table>
    </div>

    <div class="resolution-body">
        <p>
            <span class="bold">ORDINARY RESOLUTION</span> of the shareholders/members of
            <span class="bold">{{ $client->company_name ?? '' }}</span>
            (Registration Number: {{ $client->company_reg_number ?? 'N/A' }})
            ("the Company"), passed at a duly convened meeting of shareholders/members on
            <span class="bold">{{ $generatedAt->format('d F Y') }}</span>.
        </p>

        <p><span class="bold">RESOLVED THAT:</span></p>

        <div class="clause">
            <span class="clause-num">1.</span>
            The Company hereby authorises and mandates <span class="bold">{{ $companySettings['company_name'] }}</span>
            (CIPC Customer Code: <span class="bold">{{ $agentCode }}</span>) to act as the appointed agent
            for the purpose of filing the Company's beneficial ownership information with the
            Companies and Intellectual Property Commission (CIPC) in terms of section 56 of the
            Companies Act 71 of 2008, as amended.
        </div>

        <div class="clause">
            <span class="clause-num">2.</span>
            The appointed agent is authorised to:
            <br>&nbsp;&nbsp;&nbsp;(a) Lodge, update, amend or correct the beneficial ownership register on behalf of the Company;
            <br>&nbsp;&nbsp;&nbsp;(b) Communicate with CIPC on all matters relating to beneficial ownership filings;
            <br>&nbsp;&nbsp;&nbsp;(c) Access and submit information through the CIPC e-Services portal using the Company's credentials.
        </div>

        <div class="clause">
            <span class="clause-num">3.</span>
            The following persons are confirmed as the beneficial owners of the Company:
        </div>

        @foreach($directors as $i => $d)
        @php
            $shares = (int)($d->number_of_director_shares ?? 0);
            $pct = $totalShares > 0 ? round(($shares / $totalShares) * 100, 2) : 0;
        @endphp
        <div class="clause" style="margin-left:40px;">
            <span class="bold">{{ $i + 1 }}. {{ $d->firstname }} {{ $d->surname }}</span>
            — ID: {{ $d->identity_number ?? 'N/A' }}
            — {{ number_format($shares) }} {{ $shareType }} ({{ number_format($pct, 2) }}%)
        </div>
        @endforeach

        <div class="clause">
            <span class="clause-num">4.</span>
            This resolution shall remain in force until revoked by a subsequent resolution of the shareholders/members.
        </div>
    </div>

    <div class="signature-block">
        <p style="font-size:10px;font-weight:600;color:#0d3d56;margin-bottom:15px;">SIGNED BY THE SHAREHOLDERS/MEMBERS:</p>

        @foreach($directors as $d)
        <div class="sig-row">
            <div class="sig-label">Signature — {{ $d->firstname }} {{ $d->surname }}</div>
            <div class="sig-line">
                @if($d->signature_image)
                    @php
                        $sigPath = base_path('../storage/' . $d->signature_image);
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
            <div class="sig-name">
                {{ $d->firstname }} {{ $d->surname }}
                &nbsp;&middot;&nbsp; ID: {{ $d->identity_number ?? '' }}
                &nbsp;&middot;&nbsp; Date: {{ $generatedAt->format('d/m/Y') }}
            </div>
        </div>
        @endforeach
    </div>

    <div class="footer">
        {{ $companySettings['company_name'] }} &middot; CIPC Agent Code: {{ $agentCode }}
        &middot; Generated on {{ $generatedAt->format('d F Y') }}
    </div>
</div>
</body>
</html>
