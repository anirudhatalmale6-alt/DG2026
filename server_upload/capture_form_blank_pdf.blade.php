<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page {
        size: A4 portrait;
        margin: 8mm 10mm 8mm 10mm;
    }
    body {
        font-family: 'Helvetica', sans-serif;
        font-size: 10px;
        color: #333;
        margin: 0;
        padding: 0;
    }

    /* Page border - CIMS standard */
    .page-border {
        border: 2px solid #000;
        padding: 0;
        min-height: 275mm;
    }

    /* Section card - matches CIMS system */
    .card {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin: 10px 16px;
        overflow: hidden;
    }
    .card-header {
        background: linear-gradient(135deg, #0e6977 0%, #148f9f 100%);
        background-color: #148f9f;
        padding: 7px 14px;
        color: #fff;
        font-size: 11px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .card-body {
        padding: 10px 14px;
    }

    /* Field tables */
    .field-table {
        width: 100%;
        border-collapse: collapse;
    }
    .field-table td {
        vertical-align: top;
        padding: 3px 4px;
    }
    .field-label {
        font-size: 8px;
        font-weight: bold;
        color: #1a3c4d;
        padding-bottom: 2px;
    }
    .field-hint {
        font-size: 6px;
        color: #999;
        font-style: italic;
    }
    .field-value {
        border: 1.5px solid #148f9f;
        border-radius: 3px;
        padding: 6px 10px;
        font-size: 10px;
        color: #333;
        background: #fff;
        min-height: 18px;
    }
    .field-value-tall {
        min-height: 60px;
    }
    .field-value-write {
        min-height: 22px;
    }

    /* Checkbox row */
    .cb-row {
        border: 1.5px solid #148f9f;
        border-radius: 3px;
        padding: 5px 10px;
        background: #fff;
    }
    .cb-item { display: inline-block; margin-right: 12px; font-size: 8px; }
    .cb-box { display: inline-block; width: 3.5mm; height: 3.5mm; border: 1px solid #1a3c4d; margin-right: 2px; vertical-align: middle; }

    /* Director table */
    .dir-table { width: 100%; border-collapse: collapse; }
    .dir-table th {
        background-color: #148f9f;
        color: #fff;
        font-size: 7px;
        font-weight: bold;
        padding: 4px 4px;
        text-align: center;
        border: 1px solid #148f9f;
        text-transform: uppercase;
    }
    .dir-table td {
        border: 1px solid #ccc;
        min-height: 10mm;
        height: 10mm;
        padding: 2px 4px;
        font-size: 8px;
        background: #fff;
    }
    .dir-table tr:nth-child(even) td { background: #f5f5f5; }

    /* Checklist table */
    .check-table { width: 100%; border-collapse: collapse; }
    .check-table th {
        background-color: #148f9f;
        color: #fff;
        font-size: 7px;
        font-weight: bold;
        padding: 4px 5px;
        text-align: center;
        border: 1px solid #148f9f;
        text-transform: uppercase;
    }
    .check-table td {
        border: 1px solid #ccc;
        padding: 3px 5px;
        font-size: 8px;
        background: #fff;
        height: 7mm;
    }
    .check-table tr:nth-child(even) td { background: #f5f5f5; }
    .check-table .cb-center { text-align: center; }

    /* Totals */
    .totals-row { font-weight: bold; font-size: 9px; color: #1a3c4d; margin-top: 6px; padding: 0 4px; }
    .totals-box { display: inline-block; border: 1.5px solid #148f9f; min-width: 28mm; min-height: 8mm; background: #fff; margin-left: 4px; vertical-align: middle; border-radius: 3px; }

    /* Sub-header */
    .sub-header { font-weight: bold; font-size: 9px; color: #1a3c4d; margin: 8px 0 4px; }
    .separator { border-top: 1px dashed #148f9f; margin: 8px 0; opacity: 0.4; }
    .bank-sep { border-top: 1px dashed #148f9f; margin: 8px 0; opacity: 0.4; }

    /* Footer - CIMS standard */
    .footer-bar {
        background-color: #148f9f;
        padding: 8px 16px;
        color: #fff;
        font-size: 9px;
    }
    .footer-brand { font-weight: bold; font-size: 11px; }
    .footer-meta { font-size: 7px; opacity: 0.8; }

    /* Page break */
    .page-break { page-break-before: always; }

    /* Sign-off */
    .sign-row { margin-top: 12px; font-size: 8px; color: #1a3c4d; padding: 0 4px; }
    .sign-line { display: inline-block; border-bottom: 1px solid #ccc; width: 50mm; margin: 0 6px; }
</style>
</head>
<body>

{{-- ═══════════════════════════════════════ --}}
{{-- COVER PAGE --}}
{{-- ═══════════════════════════════════════ --}}
<div class="page-border">

    {{-- Cover header --}}
    <div style="background-color: #148f9f; padding: 30px 16px 20px; text-align: center;">
        <div style="font-size: 26px; font-weight: bold; color: #fff; letter-spacing: 1px;">CIM SOLUTIONS</div>
        <div style="font-size: 10px; color: rgba(255,255,255,0.75); margin-top: 4px; text-transform: uppercase; letter-spacing: 1px;">Practice Management System</div>
        <div style="height: 1px; background: rgba(255,255,255,0.3); margin: 16px 60px;"></div>
        <div style="font-size: 18px; font-weight: bold; color: #fff; margin-top: 10px;">CLIENT MASTER INPUT SHEET</div>
        <div style="font-size: 10px; color: rgba(255,255,255,0.7); margin-top: 4px;">Information Gathering Form</div>
    </div>

    {{-- Cover fields --}}
    <div style="padding: 30px 40px;">
        <div style="margin-bottom: 16px;">
            <div class="field-label" style="font-size: 10px; margin-bottom: 4px;">Client Name</div>
            <div class="field-value" style="min-height: 28px;">&nbsp;</div>
        </div>
        <div style="margin-bottom: 16px;">
            <div class="field-label" style="font-size: 10px; margin-bottom: 4px;">Client Code</div>
            <div class="field-value" style="min-height: 28px;">&nbsp;</div>
        </div>
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="width:50%; padding-right:8px;">
                    <div class="field-label" style="font-size: 10px; margin-bottom: 4px;">Date Prepared</div>
                    <div class="field-value" style="min-height: 28px;">&nbsp;</div>
                </td>
                <td style="width:50%; padding-left:8px;">
                    <div class="field-label" style="font-size: 10px; margin-bottom: 4px;">Prepared By</div>
                    <div class="field-value" style="min-height: 28px;">&nbsp;</div>
                </td>
            </tr>
        </table>
        <div style="text-align: center; font-style: italic; color: #666; font-size: 9px; margin-top: 30px; padding: 0 30px;">
            This form is used to gather all client information for capture into the CIMS Practice Management System.<br>
            Please complete all applicable sections. Leave blank if not applicable.
        </div>
    </div>

    {{-- Cover footer --}}
    <table style="width:100%; margin-top:auto;">
        <tr>
            <td class="footer-bar" style="width:60%;">
                <div class="footer-brand">CIM Solutions</div>
                <div class="footer-meta">Client Master Input Sheet</div>
            </td>
            <td class="footer-bar" style="width:40%; text-align:right;">
                <div style="font-size:18px; font-weight:bold;">{{ now()->format('Y') }}</div>
                <div class="footer-meta">Blank Capture Form</div>
            </td>
        </tr>
    </table>
</div>

{{-- ═══════════════════════════════════════ --}}
{{-- SECTION 1: COMPANY INFORMATION --}}
{{-- ═══════════════════════════════════════ --}}
<div class="page-break"></div>
<div class="page-border">

    <div class="card">
        <div class="card-header">:: 01 — Company Information</div>
        <div class="card-body">
            <div class="field-label">Registered Company Name <span class="field-hint">(As per CIPC records)</span></div>
            <div class="field-value field-value-write">&nbsp;</div>
            <div style="height:6px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:33%;">
                        <div class="field-label">Client Code</div>
                        <div class="field-value field-value-write">&nbsp;</div>
                    </td>
                    <td style="width:67%;">
                        <div class="field-label">Date of Company Registration <span class="field-hint">(DD/MM/YYYY)</span></div>
                        <div class="field-value field-value-write">&nbsp;</div>
                    </td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:25%;"><div class="field-label">Company Reg No. <span class="field-hint">(YYYY/NNNNNN/NN)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:25%;"><div class="field-label">Company Type</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:25%;"><div class="field-label">BizPortal No.</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:25%;"><div class="field-label">Fin. Year End <span class="field-hint">(Month)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <div class="field-label">Trading Name</div>
            <div class="field-value field-value-write">&nbsp;</div>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:33%;"><div class="field-label">Number of Directors</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:33%;"><div class="field-label">Number of Shares</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:33%;"><div class="field-label">Share Type <span class="field-hint">(e.g. Ordinary / Non Par)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">:: 02 — Income Tax Registration</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Company Income Tax Number</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">Date of IT Registration <span class="field-hint">(DD/MM/YYYY)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">:: 03 — Payroll Registration</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:25%;"><div class="field-label">PAYE Number</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:25%;"><div class="field-label">SDL Number</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:25%;"><div class="field-label">UIF Number</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:25%;"><div class="field-label">Date of Liability <span class="field-hint">(DD/MM/YYYY)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <div class="field-label">Payroll Status</div>
            <div class="cb-row">
                <span class="cb-item"><span class="cb-box"></span> Active</span>
                <span class="cb-item"><span class="cb-box"></span> Dormant</span>
                <span class="cb-item"><span class="cb-box"></span> Suspended</span>
                <span class="cb-item"><span class="cb-box"></span> Deregistered</span>
                <span class="cb-item"><span class="cb-box"></span> Pending</span>
                <span class="cb-item"><span class="cb-box"></span> Ceased Trading</span>
            </div>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">EMP201 Status</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">EMP501 Status</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Dept of Labour Number</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">WCA - COIDA Number</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">:: 04 — VAT Registration</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">VAT Number</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">Date of Registration <span class="field-hint">(DD/MM/YYYY)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <div class="field-label">Return Cycle</div>
            <div class="cb-row">
                <span class="cb-item"><span class="cb-box"></span> Monthly</span>
                <span class="cb-item"><span class="cb-box"></span> Bi-Monthly</span>
                <span class="cb-item"><span class="cb-box"></span> 4-Monthly</span>
                <span class="cb-item"><span class="cb-box"></span> 6-Monthly</span>
                <span class="cb-item"><span class="cb-box"></span> Annually</span>
            </div>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">With Effect From <span class="field-hint">(DD/MM/YYYY)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">Last VAT Return Date <span class="field-hint">(DD/MM/YYYY)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <div class="field-label">VAT Status</div>
            <div class="cb-row">
                <span class="cb-item"><span class="cb-box"></span> Active</span>
                <span class="cb-item"><span class="cb-box"></span> De-Registered</span>
                <span class="cb-item"><span class="cb-box"></span> Suspended</span>
            </div>
            <div style="height:4px;"></div>
            <div class="field-label">VAT Basis</div>
            <div class="cb-row">
                <span class="cb-item"><span class="cb-box"></span> Invoice Basis (Standard)</span>
                <span class="cb-item"><span class="cb-box"></span> Payment Basis (SARS Approved)</span>
            </div>
        </div>
    </div>

    <table style="width:100%; margin-top:auto;">
        <tr>
            <td class="footer-bar" style="width:60%;"><div class="footer-brand">CIM Solutions</div><div class="footer-meta">Client Information Management</div></td>
            <td class="footer-bar" style="width:40%;text-align:right;"><div style="font-size:18px;font-weight:bold;">{{ now()->format('Y') }}</div><div class="footer-meta">Page 2 | Blank Capture Form</div></td>
        </tr>
    </table>
</div>

{{-- ═══════════════════════════════════════ --}}
{{-- SECTION 5-6: CONTACT & ADDRESS --}}
{{-- ═══════════════════════════════════════ --}}
<div class="page-break"></div>
<div class="page-border">

    <div class="card">
        <div class="card-header">:: 05 — Contact Information</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:25%;"><div class="field-label">Business Phone</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:25%;"><div class="field-label">Direct</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:25%;"><div class="field-label">Mobile</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:25%;"><div class="field-label">WhatsApp</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Email Address (Compliance)</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">Email Address (Admin)</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <div class="field-label">Website <span class="field-hint">(https://)</span></div>
            <div class="field-value field-value-write">&nbsp;</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">:: 06 — Address Details</div>
        <div class="card-body">
            @for($a = 1; $a <= 3; $a++)
            <div class="sub-header">Address {{ $a }}</div>
            <div class="field-label">Address Type</div>
            <div class="cb-row">
                <span class="cb-item"><span class="cb-box"></span> Registered</span>
                <span class="cb-item"><span class="cb-box"></span> Physical</span>
                <span class="cb-item"><span class="cb-box"></span> Postal</span>
                <span class="cb-item"><span class="cb-box"></span> Other</span>
            </div>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:25%;"><div class="field-label">Unit No.</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:75%;"><div class="field-label">Complex / Building Name</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <table class="field-table">
                <tr>
                    <td style="width:25%;"><div class="field-label">Street No.</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:75%;"><div class="field-label">Street Name</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Suburb</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">City</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <table class="field-table">
                <tr>
                    <td style="width:33%;"><div class="field-label">Postal Code</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:34%;"><div class="field-label">Province</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:33%;"><div class="field-label">Country</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            @if($a < 3)<div class="separator"></div>@endif
            @endfor
        </div>
    </div>

    <table style="width:100%; margin-top:auto;">
        <tr>
            <td class="footer-bar" style="width:60%;"><div class="footer-brand">CIM Solutions</div><div class="footer-meta">Client Information Management</div></td>
            <td class="footer-bar" style="width:40%;text-align:right;"><div style="font-size:18px;font-weight:bold;">{{ now()->format('Y') }}</div><div class="footer-meta">Page 3 | Blank Capture Form</div></td>
        </tr>
    </table>
</div>

{{-- ═══════════════════════════════════════ --}}
{{-- SECTION 7-9: DIRECTORS, SARS, BANKING --}}
{{-- ═══════════════════════════════════════ --}}
<div class="page-break"></div>
<div class="page-border">

    <div class="card">
        <div class="card-header">:: 07 — Directors / Shareholders</div>
        <div class="card-body">
            <table class="dir-table">
                <thead>
                    <tr>
                        <th style="width:22%">Full Name</th>
                        <th style="width:14%">ID Number</th>
                        <th style="width:11%">Type</th>
                        <th style="width:9%">Status</th>
                        <th style="width:11%">Date Engaged</th>
                        <th style="width:11%">Date Resigned</th>
                        <th style="width:11%">Shares</th>
                        <th style="width:11%">% Share</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 5; $i++)
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                    @endfor
                </tbody>
            </table>
            <div class="totals-row">
                Totals: &nbsp;&nbsp; Total Shares: <span class="totals-box"></span> &nbsp;&nbsp; Total %: <span class="totals-box"></span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">:: 08 — SARS E-Filing Login Details</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">SARS Login</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">SARS Password</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Mobile for SARS OTP</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">Email for SARS OTP</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">:: 09 — Banking Details</div>
        <div class="card-body">
            @for($b = 1; $b <= 2; $b++)
            <div class="sub-header">Bank Account {{ $b }}</div>
            <div class="field-label">Account Holder</div>
            <div class="field-value field-value-write">&nbsp;</div>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:33%;"><div class="field-label">Bank Name</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:33%;"><div class="field-label">Account Number</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:33%;"><div class="field-label">Account Type</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <table class="field-table">
                <tr>
                    <td style="width:33%;"><div class="field-label">Branch Name</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:33%;"><div class="field-label">Branch Code</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:33%;"><div class="field-label">Swift Code</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            @if($b < 2)<div class="separator"></div>@endif
            @endfor
        </div>
    </div>

    <table style="width:100%; margin-top:auto;">
        <tr>
            <td class="footer-bar" style="width:60%;"><div class="footer-brand">CIM Solutions</div><div class="footer-meta">Client Information Management</div></td>
            <td class="footer-bar" style="width:40%;text-align:right;"><div style="font-size:18px;font-weight:bold;">{{ now()->format('Y') }}</div><div class="footer-meta">Page 4 | Blank Capture Form</div></td>
        </tr>
    </table>
</div>

{{-- ═══════════════════════════════════════ --}}
{{-- SECTION 10-11 + CHECKLIST --}}
{{-- ═══════════════════════════════════════ --}}
<div class="page-break"></div>
<div class="page-border">

    <div class="card">
        <div class="card-header">:: 10 — BEE Information</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">BEE Level <span class="field-hint">(1-8 or Non-Compliant)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">BEE Certificate Expiry Date <span class="field-hint">(DD/MM/YYYY)</span></div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">BEE Certificate Number</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">BEE Verification Agency</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Black Ownership %</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">Black Women Ownership %</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">:: 11 — General</div>
        <div class="card-body">
            <table class="field-table">
                <tr>
                    <td style="width:50%;"><div class="field-label">Client Status</div><div class="field-value field-value-write">&nbsp;</div></td>
                    <td style="width:50%;"><div class="field-label">Client Category</div><div class="field-value field-value-write">&nbsp;</div></td>
                </tr>
            </table>
            <div style="height:4px;"></div>
            <div class="field-label">Services Required</div>
            <div class="field-value field-value-tall">&nbsp;</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">:: Required Documents Checklist</div>
        <div class="card-body">
            <table class="check-table">
                <thead>
                    <tr>
                        <th style="width:6%">#</th>
                        <th style="width:50%">Document Description</th>
                        <th style="width:8%">Recv'd</th>
                        <th style="width:16%">Date Received</th>
                        <th style="width:20%">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $docs = [
                        'COR 14.3 Registration Certificate',
                        'Company Income Tax Registration Notice',
                        'Payroll Notice of Registration (EMP201)',
                        'VAT Registration Certificate',
                        'Confirmation of Banking Letter (per bank)',
                        'Certified ID Copy — Director 1',
                        'Certified ID Copy — Director 2',
                        'Certified ID Copy — Director 3',
                        'Proof of Address — Director 1',
                        'Proof of Address — Director 2',
                        'Proof of Address — Director 3',
                        'Passport Copy (if foreign national)',
                        'Signature Specimen — Director 1',
                        'Signature Specimen — Director 2',
                        'Signature Specimen — Director 3',
                        'Profile Photo — Director 1',
                        'Profile Photo — Director 2',
                        'Profile Photo — Director 3',
                        'BEE Certificate',
                        'Tax Clearance Certificate',
                        'CIPC Annual Return',
                        'Share Certificate(s)',
                    ];
                    @endphp
                    @foreach($docs as $idx => $doc)
                    <tr>
                        <td style="text-align:center">{{ $idx + 1 }}</td>
                        <td>{{ $doc }}</td>
                        <td class="cb-center"><span class="cb-box" style="display:inline-block"></span></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="padding: 0 16px;">
        <div class="sign-row">
            Prepared by: <span class="sign-line"></span> &nbsp;&nbsp; Date: <span class="sign-line" style="width:30mm"></span>
        </div>
        <div class="sign-row" style="margin-top: 8px;">
            Reviewed by: <span class="sign-line"></span> &nbsp;&nbsp; Date: <span class="sign-line" style="width:30mm"></span>
        </div>
    </div>

    <table style="width:100%; margin-top:auto;">
        <tr>
            <td class="footer-bar" style="width:60%;"><div class="footer-brand">CIM Solutions</div><div class="footer-meta">Client Information Management</div></td>
            <td class="footer-bar" style="width:40%;text-align:right;"><div style="font-size:18px;font-weight:bold;">{{ now()->format('Y') }}</div><div class="footer-meta">Page 5 | Blank Capture Form</div></td>
        </tr>
    </table>
</div>

</body>
</html>
