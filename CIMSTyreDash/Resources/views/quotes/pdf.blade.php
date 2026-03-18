<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        /* ---- Reset & Base ---- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }

        /* ---- Page Layout ---- */
        .page {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px 40px;
        }

        /* ---- Company Header ---- */
        .company-header {
            border-bottom: 3px solid #17A2B8;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-header h1 {
            font-size: 24px;
            color: #0d3d56;
            margin-bottom: 3px;
        }
        .company-header p {
            font-size: 11px;
            color: #666;
        }

        /* ---- Quote Title ---- */
        .quote-title {
            text-align: right;
            margin-bottom: 20px;
        }
        .quote-title h2 {
            font-size: 22px;
            color: #17A2B8;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .quote-title .quote-number {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }
        .quote-title .quote-status {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #fff;
        }
        .status-draft { background: #ffc107; color: #333; }
        .status-sent { background: #17a2b8; }
        .status-accepted { background: #28a745; }
        .status-declined { background: #dc3545; }
        .status-expired { background: #6c757d; }
        .status-invoiced { background: #0d6efd; }

        /* ---- Info Grid ---- */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .info-col:last-child { padding-right: 0; padding-left: 20px; }
        .info-block {
            margin-bottom: 12px;
        }
        .info-block h4 {
            font-size: 11px;
            text-transform: uppercase;
            color: #17A2B8;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 4px;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        .info-block p {
            font-size: 12px;
            margin-bottom: 2px;
        }
        .info-block .label {
            display: inline-block;
            width: 100px;
            color: #666;
            font-weight: 600;
        }

        /* ---- Tables ---- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background: #0d3d56;
            color: #fff;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
        }
        table th.text-right { text-align: right; }
        table th.text-center { text-align: center; }
        table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 11px;
        }
        table td.text-right { text-align: right; }
        table td.text-center { text-align: center; }
        table tr.selected-row {
            background: #d4edda;
            font-weight: 600;
        }
        table tr.selected-row td {
            border-bottom: 1px solid #28a745;
        }

        /* ---- Section Headers ---- */
        .section-header {
            font-size: 13px;
            font-weight: 700;
            color: #0d3d56;
            border-bottom: 2px solid #17A2B8;
            padding-bottom: 5px;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        /* ---- Totals ---- */
        .totals-table {
            width: 300px;
            margin-left: auto;
            margin-bottom: 20px;
        }
        .totals-table td {
            padding: 5px 10px;
            border-bottom: none;
        }
        .totals-table .grand-total td {
            font-size: 14px;
            font-weight: 700;
            border-top: 2px solid #0d3d56;
            padding-top: 8px;
        }

        /* ---- Notes ---- */
        .notes-section {
            margin-top: 20px;
            padding: 12px 15px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #17A2B8;
        }
        .notes-section h4 {
            font-size: 11px;
            text-transform: uppercase;
            color: #17A2B8;
            margin-bottom: 5px;
        }
        .notes-section p {
            font-size: 11px;
            color: #555;
        }

        /* ---- Footer ---- */
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #0d3d56;
            font-size: 10px;
            color: #888;
        }
        .footer h4 {
            font-size: 11px;
            font-weight: 700;
            color: #0d3d56;
            margin-bottom: 5px;
        }
        .footer ul {
            list-style: none;
            padding: 0;
        }
        .footer ul li {
            margin-bottom: 2px;
        }

        /* ---- Print Styles ---- */
        @media print {
            body { font-size: 11px; }
            .page { padding: 15px 20px; max-width: none; }
            .no-print { display: none !important; }
            table th { background: #0d3d56 !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            table tr.selected-row { background: #d4edda !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .quote-status { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="page">
    {{-- Print Button (hidden in print) --}}
    <div class="no-print" style="text-align: right; margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 20px; background: #17A2B8; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">
            Print / Save PDF
        </button>
    </div>

    {{-- Company Header --}}
    <div class="company-header">
        @php
            $companyName = \Modules\CIMSTyreDash\Models\TyreDashSetting::getValue('company_name', 'TyreDash');
            $companyAddress = \Modules\CIMSTyreDash\Models\TyreDashSetting::getValue('company_address', '');
            $companyPhone = \Modules\CIMSTyreDash\Models\TyreDashSetting::getValue('company_phone', '');
            $companyEmail = \Modules\CIMSTyreDash\Models\TyreDashSetting::getValue('company_email', '');
            $companyVat = \Modules\CIMSTyreDash\Models\TyreDashSetting::getValue('company_vat_number', '');
        @endphp
        <h1>{{ $companyName }}</h1>
        @if($companyAddress)<p>{{ $companyAddress }}</p>@endif
        @if($companyPhone || $companyEmail)
            <p>
                @if($companyPhone)Tel: {{ $companyPhone }}@endif
                @if($companyPhone && $companyEmail) | @endif
                @if($companyEmail)Email: {{ $companyEmail }}@endif
            </p>
        @endif
        @if($companyVat)<p>VAT No: {{ $companyVat }}</p>@endif
    </div>

    {{-- Quote Title & Status --}}
    <div class="quote-title">
        <h2>Quotation</h2>
        <div class="quote-number">{{ $quote->quote_number }}</div>
        <span class="quote-status status-{{ $quote->status }}">{{ ucfirst($quote->status) }}</span>
    </div>

    {{-- Quote & Customer/Vehicle Info --}}
    <div class="info-grid">
        <div class="info-col">
            <div class="info-block">
                <h4>Quote Details</h4>
                <p><span class="label">Date:</span> {{ $quote->quote_date->format('d M Y') }}</p>
                <p><span class="label">Valid Until:</span> {{ $quote->valid_until ? $quote->valid_until->format('d M Y') : 'N/A' }}</p>
                <p><span class="label">Branch:</span> {{ $quote->branch->name ?? 'N/A' }}</p>
                <p><span class="label">Salesman:</span> {{ $quote->salesman_name ?? 'N/A' }}</p>
                @if($quote->customer_order_ref)
                    <p><span class="label">Order Ref:</span> {{ $quote->customer_order_ref }}</p>
                @endif
            </div>
        </div>
        <div class="info-col">
            <div class="info-block">
                <h4>Customer</h4>
                @if($quote->customer)
                    <p><strong>{{ $quote->customer->full_name }}</strong></p>
                    @if($quote->customer->company_name)<p>{{ $quote->customer->company_name }}</p>@endif
                    @if($quote->customer->phone)<p>Tel: {{ $quote->customer->phone }}</p>@endif
                    @if($quote->customer->cell)<p>Cell: {{ $quote->customer->cell }}</p>@endif
                    @if($quote->customer->email)<p>Email: {{ $quote->customer->email }}</p>@endif
                    @if($quote->customer->address)<p>{{ $quote->customer->address }}</p>@endif
                @else
                    <p>Walk-in Customer</p>
                @endif
            </div>
            @if($quote->vehicle)
                <div class="info-block">
                    <h4>Vehicle</h4>
                    <p><span class="label">Registration:</span> <strong>{{ strtoupper($quote->vehicle->registration ?? 'N/A') }}</strong></p>
                    <p><span class="label">Make/Model:</span> {{ $quote->vehicle->make }} {{ $quote->vehicle->model }}</p>
                    @if($quote->vehicle->year)<p><span class="label">Year:</span> {{ $quote->vehicle->year }}</p>@endif
                </div>
            @endif
        </div>
    </div>

    {{-- Tyre Options --}}
    @if($quote->quoteOptions->count())
        <div class="section-header">Tyre Options</div>
        <table>
            <thead>
                <tr>
                    <th>Opt #</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Size</th>
                    <th>Load/Speed</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Line Total</th>
                    <th class="text-center">Selected</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->quoteOptions as $option)
                    <tr class="{{ $option->is_selected ? 'selected-row' : '' }}">
                        <td>{{ $option->option_number }}</td>
                        <td>
                            @if($option->product && $option->product->brand && $option->product->brand->logo_url)
                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 50px; height: 22px; background: #fff; border: 1px solid #e8e8e8; border-radius: 3px; padding: 2px 4px; overflow: hidden; vertical-align: middle; margin-right: 5px;"><img src="{{ asset('modules/cimstyredash/brands/' . $option->product->brand->logo_url) }}" alt="{{ $option->product->brand->name }}" style="max-width: 100%; max-height: 100%; object-fit: contain;" onerror="this.parentElement.style.display='none'"></span>
                            @endif
                            {{ $option->product->brand->name ?? 'N/A' }}
                        </td>
                        <td>{{ $option->product->model_name ?? '-' }}</td>
                        <td>{{ $option->product->size->full_size ?? '-' }}</td>
                        <td>{{ $option->product->load_index ?? '-' }} / {{ $option->product->speed_rating ?? '-' }}</td>
                        <td class="text-center">{{ $option->quantity }}</td>
                        <td class="text-right">{{ $currencySymbol }}{{ number_format($option->unit_price, 2) }}</td>
                        <td class="text-right"><strong>{{ $currencySymbol }}{{ number_format($option->line_total, 2) }}</strong></td>
                        <td class="text-center">{{ $option->is_selected ? 'YES' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Services --}}
    @if($quote->quoteServices->count())
        <div class="section-header">Services</div>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Code</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Line Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->quoteServices as $qs)
                    <tr>
                        <td>{{ $qs->service->name ?? 'N/A' }}</td>
                        <td>{{ $qs->service->code ?? '-' }}</td>
                        <td class="text-center">{{ $qs->quantity }}</td>
                        <td class="text-right">{{ $currencySymbol }}{{ number_format($qs->unit_price, 2) }}</td>
                        <td class="text-right"><strong>{{ $currencySymbol }}{{ number_format($qs->line_total, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Totals --}}
    @php
        $selectedOption = $quote->quoteOptions->firstWhere('is_selected', true) ?? $quote->quoteOptions->first();
        $optionTotal = $selectedOption ? (float) $selectedOption->line_total : 0;
        $servicesTotal = (float) $quote->quoteServices->sum('line_total');
        $grandTotal = $optionTotal + $servicesTotal;
        $vatDivisor = 1 + ($vatRate / 100);
        $subtotalExcl = $grandTotal / $vatDivisor;
        $vatAmount = $grandTotal - $subtotalExcl;
    @endphp

    <table class="totals-table">
        <tbody>
            <tr>
                <td>Selected Option Total:</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format($optionTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Services Total:</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format($servicesTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Subtotal (excl VAT):</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format($subtotalExcl, 2) }}</td>
            </tr>
            <tr>
                <td>VAT ({{ $vatRate }}%):</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format($vatAmount, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td>Grand Total:</td>
                <td class="text-right">{{ $currencySymbol }}{{ number_format($grandTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Notes --}}
    @if($quote->customer_comment)
        <div class="notes-section">
            <h4>Customer Notes</h4>
            <p>{{ $quote->customer_comment }}</p>
        </div>
    @endif

    {{-- Footer / Terms --}}
    <div class="footer">
        <h4>Terms & Conditions</h4>
        <ul>
            <li>This quotation is valid until {{ $quote->valid_until ? $quote->valid_until->format('d M Y') : 'the date specified above' }}.</li>
            <li>All prices are quoted in {{ $currencySymbol }} and include VAT at {{ $vatRate }}%.</li>
            <li>Stock availability is subject to change without notice.</li>
            <li>Prices may vary if the vehicle requires a different tyre size upon inspection.</li>
            <li>A deposit may be required for special-order tyres.</li>
            <li>Warranty on tyres is provided by the manufacturer as per their warranty policy.</li>
        </ul>
        <p style="margin-top: 10px; text-align: center; color: #aaa;">
            Generated on {{ now()->format('d M Y H:i') }} | {{ $companyName }}
        </p>
    </div>
</div>

</body>
</html>
