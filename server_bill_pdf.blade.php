<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" id="meta-csrf" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ config('system.settings_company_name') }}</title>


    <!--
        web preview example
        http://example.com/invoices/29/pdf?view=preview
        {{ BASE_DIR.'/' }}
    -->

    @if(request('view') == 'preview')
    <base href="{{ url('/') }}" target="_self">
    <link href="public/vendor/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    <!--GOOGLE FONTS-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" rel="stylesheet"
        type="text/css">
    @else
    <base href="" target="_self">
    <link href="{{ BASE_DIR }}/public/vendor/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    @endif

    <!--[start] PDF FRONT-->
    @if(request('view') != 'preview' || !request()->filled('view'))

    <!-- dompdf font - regular latin characters-->
    @if(config('system.settings2_dompdf_fonts') == 'default' || config('system.settings2_dompdf_fonts') == '')
    <style>
        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path("app/fonts/notosans/NotoSans-Regular.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path("app/fonts/notosans/NotoSans-Regular.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path("app/fonts/notosans/NotoSans-Bold.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 600;
            src: url('{{ storage_path("app/fonts/notosans/NotoSans-Bold.ttf") }}') format("truetype");
        }
    </style>
    @endif


    <!-- dompdf font - regular latin characters-->
    @if(config('system.settings2_dompdf_fonts') == 'dejavu')
    <style>
        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path("app/fonts/default/DejaVuSans.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path("app/fonts/default/DejaVuSans.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path("app/fonts/default/DejaVuSans-Bold.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 600;
            src: url('{{ storage_path("app/fonts/default/DejaVuSans-Bold.ttf") }}') format("truetype");
        }
    </style>
    @endif

    <!-- dompdf font - japanese characters-->
    @if(config('system.settings2_dompdf_fonts') == 'japanese')
    <style>
        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path("app/fonts/japanese/Meiryo.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path("app/fonts/japanese/Meiryo.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path("app/fonts/japanese/Meiryo-Bold.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 600;
            src: url('{{ storage_path("app/fonts/japanese/Meiryo-Bold.ttf") }}') format("truetype");
        }
    </style>
    @endif

    <!-- dompdf font - chinese characters-->
    @if(config('system.settings2_dompdf_fonts') == 'chinese-traditional')
    <style>
        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path("app/fonts/chinese/NotoSansTC-Regular.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path("app/fonts/chinese/NotoSansTC-SemiBold.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path("app/fonts/chinese/NotoSansTC-Bold.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 600;
            src: url('{{ storage_path("app/fonts/chinese/NotoSansTC-ExtraBold.ttf") }}') format("truetype");
        }
    </style>
    @endif

    <!-- dompdf font - chinese characters-->
    @if(config('system.settings2_dompdf_fonts') == 'chinese-simplified')
    <style>
        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path("app/fonts/chinese/NotoSansSC-Regular.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path("app/fonts/chinese/NotoSansSC-Regular.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path("app/fonts/chinese/NotoSansSC-Bold.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 600;
            src: url('{{ storage_path("app/fonts/chinese/NotoSansSC-ExtraBold.ttf") }}') format("truetype");
        }
    </style>
    @endif

    <!-- dompdf font - korean characters-->
    @if(config('system.settings2_dompdf_fonts') == 'korean')
    <style>
        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path("app/fonts/korean/NotoSansKR-Regular.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 400;
            src: url('{{ storage_path("app/fonts/korean/NotoSansKR-Regular.ttf") }}') format("truetype");
        }


        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path("app/fonts/korean/NotoSansKR-Bold.ttf") }}') format("truetype");
        }

        @font-face {
            font-family: 'DynamicFont';
            font-style: normal;
            font-weight: 600;
            src: url('{{ storage_path("app/fonts/korean/NotoSansKR-ExtraBold.ttf") }}') format("truetype");
        }
    </style>
    @endif


    @endif
    <!--[end] PDF FRONT-->


    @if(request('view') == 'preview')
    <link href="{{ config('theme.selected_theme_pdf_css') }}" rel="stylesheet">
    @else
    <link href="{{ BASE_DIR }}/{{ config('theme.selected_theme_pdf_css') }}" rel="stylesheet">
    @endif

    <!--[start] WEB FRONT-->
    @if(request('view') == 'preview')
    <style>
        body {
            font-family: "Montserrat" !important;
        }

        .pdf-page h1,
        .pdf-page h2,
        .pdf-page h3,
        .pdf-page h4,
        .pdf-page h5,
        .pdf-page h6 {
            font-family: "Montserrat" !important;
        }
    </style>
    @endif
    <!--[end] WEB FRONT-->

    <!--custom CSS file (DB) -->
    {!! customDPFCSS(config('system.settings2_bills_pdf_css')) !!}

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="public/images/favicon.png">

    <!-- ============================================ -->
    <!-- CIMS SMARTWEIGH CUSTOM PDF STYLING           -->
    <!-- Teal multi-tone with pink accents            -->
    <!-- ============================================ -->
    <style>
        /* --- CIMS Color Palette --- */
        /* Dark Teal:    #004D40 */
        /* Primary Teal: #009688 */
        /* Hover Teal:   #00796B */
        /* Light Teal:   #4DB6AC */
        /* Secondary:    #80CBC4 */
        /* Lightest:     #B2DFDB */
        /* Faint BG:     #E0F2F1 */
        /* Pink Accent:  #E91E63 */
        /* Pink Light:   #F48FB1 */

        .pdf-page .bill-pdf {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            color: #333333;
            font-size: 12px;
        }

        .pdf-page .bill-pdf .hidden {
            display: none !important;
        }

        /* ---- TOP ACCENT BAR ---- */
        .cims-top-bar {
            background-color: #004D40;
            height: 8px;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* ---- HEADER SECTION ---- */
        .pdf-page .bill-pdf .bill-header {
            margin-bottom: 0;
            padding: 20px 30px 15px 30px;
            background-color: #E0F2F1;
            border-bottom: 3px solid #009688;
        }
        .pdf-page .bill-pdf .bill-header table {
            width: 100%;
        }
        .pdf-page .bill-pdf .bill-header table td.x-left {
            width: 50%;
            text-align: left;
            vertical-align: middle;
        }
        .pdf-page .bill-pdf .bill-header table td.x-right {
            width: 50%;
            text-align: right;
            vertical-align: middle;
        }
        .pdf-page .bill-pdf .bill-header table td.x-left .x-logo img {
            max-width: 350px !important;
            height: auto !important;
        }
        .pdf-page .bill-pdf .bill-header table td.x-right .x-bill-type h2 {
            margin: 0 0 4px 0;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .pdf-page .bill-pdf .bill-header table td.x-right .x-bill-type h2.text-warning {
            color: #E91E63 !important;
        }
        .pdf-page .bill-pdf .bill-header table td.x-right .x-bill-type h2.text-danger {
            color: #E91E63 !important;
        }
        .pdf-page .bill-pdf .bill-header table td.x-right .x-bill-type h2.text-success {
            color: #009688 !important;
        }
        .pdf-page .bill-pdf .bill-header table td.x-right .x-bill-type h4 {
            margin: 0 0 2px 0;
            font-size: 22px;
            color: #004D40;
            letter-spacing: 2px;
            font-weight: bold;
        }
        .pdf-page .bill-pdf .bill-header table td.x-right .x-bill-type h5 {
            margin: 0;
            font-size: 13px;
            color: #00796B;
            font-weight: 600;
        }

        /* ---- HEADER ADDRESS (right side) ---- */
        .pdf-page .bill-pdf .bill-header .cims-header-address {
            font-size: 11px;
            color: #004D40;
            line-height: 15px;
            text-align: right;
        }
        .pdf-page .bill-pdf .bill-header .cims-header-address div {
            margin: 0;
            padding: 0;
        }

        /* ---- STATUS BAR (below header) ---- */
        .cims-status-bar {
            background-color: #FFFFFF;
            padding: 6px 30px;
            border-bottom: 1px solid #B2DFDB;
        }
        .cims-status-bar table {
            width: 100%;
        }
        .cims-status-bar table td.x-left {
            width: 50%;
            text-align: left;
            vertical-align: middle;
        }
        .cims-status-bar table td.x-right {
            width: 50%;
            text-align: right;
            vertical-align: middle;
        }
        .cims-status-bar .text-warning {
            color: #E91E63 !important;
        }
        .cims-status-bar .text-danger {
            color: #E91E63 !important;
        }
        .cims-status-bar .text-success {
            color: #009688 !important;
        }

        /* ---- ADDRESSES SECTION ---- */
        .pdf-page .bill-pdf .bill-addresses {
            padding: 10px 30px 0 30px;
        }
        .pdf-page .bill-pdf .bill-addresses table {
            width: 100%;
        }
        .pdf-page .bill-pdf .bill-addresses table td.x-left {
            width: 50%;
            text-align: left;
            vertical-align: top;
        }
        .pdf-page .bill-pdf .bill-addresses table td.x-right {
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        .pdf-page .bill-pdf .bill-addresses .bill-addresses-company,
        .pdf-page .bill-pdf .bill-addresses .bill-addresses-client {
            padding: 8px 12px;
            border-left: 4px solid #4DB6AC;
            background-color: #FAFAFA;
        }
        .pdf-page .bill-pdf .bill-addresses .bill-addresses-client {
            border-left: none;
            border-right: 4px solid #E91E63;
            text-align: right;
        }
        .pdf-page .bill-pdf .bill-addresses .x-company-name h5 {
            color: #004D40;
            font-size: 13px;
            margin: 0 0 3px 0;
            padding: 0;
        }
        .pdf-page .bill-pdf .bill-addresses .bill-addresses-client .x-company-name h5 {
            color: #E91E63;
        }
        .pdf-page .bill-pdf .bill-addresses .x-line {
            font-size: 11px;
            color: #555555;
            line-height: 15px;
            height: auto;
            padding: 0;
        }

        /* ---- DATES & PAYMENTS (inside right address cell) ---- */
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments {
            text-align: right;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments table td {
            padding: 2px 0 !important;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dates {
            margin: 0;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dates .x-date-lang,
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dates .x-date-due-lang {
            height: 14px !important;
            font-weight: bold;
            font-size: 11px;
            color: #004D40;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: right;
            padding-right: 10px;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dates .x-date span,
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dates .x-date-due span {
            font-size: 11px;
            color: #333;
            text-align: right;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dates .x-date,
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dates .x-date-due {
            text-align: right;
            width: 100px;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues {
            margin: 0;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues table {
            width: 100%;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-payments-lang,
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-balance-due-lang {
            text-align: right;
            height: 14px !important;
            padding-right: 10px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 11px;
            color: #004D40;
            letter-spacing: 0.5px;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-payments,
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-balance-due {
            text-align: right;
            width: 100px;
            padding-left: 0 !important;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-payments span,
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-payments label,
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-balance-due span,
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-balance-due label {
            font-size: 11px;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-balance-due .x-due-amount-plain {
            display: inline !important;
            color: #E91E63;
            font-weight: bold;
            font-size: 13px;
        }
        .pdf-page .bill-pdf .bill-addresses .cims-dates-payments .invoice-dues .x-balance-due .x-due-amount-label {
            display: none;
        }

        /* ---- LINE ITEMS TABLE ---- */
        .pdf-page .bill-pdf .bill-table-pdf {
            padding: 0 30px;
            margin-top: 20px;
        }
        .pdf-page .bill-pdf .bill-table-pdf .col-12 {
            padding: 0;
        }
        .pdf-page .bill-pdf .bill-table-pdf .table-responsive {
            margin-top: 0 !important;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table {
            width: 100%;
            border-collapse: collapse;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table th {
            background-color: #004D40 !important;
            color: #FFFFFF !important;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 12px !important;
            border: none !important;
            border-bottom: none !important;
            vertical-align: middle;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table th.x-description,
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table td.x-description {
            width: 55%;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table th.x-quantity,
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table td.x-quantity {
            width: 10%;
            text-align: center;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table th.x-unit,
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table td.x-unit {
            display: none !important;
            width: 0 !important;
            padding: 0 !important;
            border: none !important;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table th.x-rate,
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table td.x-rate {
            width: 17%;
            text-align: right !important;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table th.x-total,
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table td.x-total {
            width: 18%;
            text-align: right !important;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table th.text-right {
            text-align: right !important;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table th.text-left {
            text-align: left !important;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table th.hidden {
            display: none;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table td {
            font-size: 11px;
            color: #333333;
            padding: 9px 12px !important;
            border-bottom: 1px solid #B2DFDB !important;
            border-top: none !important;
            vertical-align: top;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table td.hidden {
            display: none;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table tbody tr:nth-child(even) td {
            background-color: #E0F2F1;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table tbody tr:nth-child(odd) td {
            background-color: #FFFFFF;
        }
        .pdf-page .bill-pdf .bill-table-pdf .invoice-table td.x-total {
            font-weight: bold;
            color: #004D40;
        }

        /* ---- TOTALS SECTION ---- */
        .pdf-page .bill-pdf .bill-totals-table-pdf {
            padding: 0 30px;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper {
            padding: 0 !important;
            margin-right: 0;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .pull-right {
            float: right;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table td {
            height: 28px;
            padding: 4px 8px;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #billing-table-section-subtotal tr {
            font-weight: bold;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #billing-table-section-subtotal td {
            border-top: 2px solid #009688;
            color: #004D40;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #billing-sums-before-tax-container {
            font-weight: bold;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #billing-subtotal-figure,
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #billing-sums-discount,
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #billing-sums-total,
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #billing-sums-before-tax {
            width: 160px;
            text-align: right;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #invoice-table-section-total {
            font-size: 16px;
            font-weight: bold;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #invoice-table-section-total td {
            border-top: 2px solid #004D40;
            padding-top: 8px;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #invoice-table-section-total .billing-sums-total {
            color: #004D40;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table #billing-sums-total {
            color: #E91E63 !important;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf #bill-totals-wrapper .invoice-total-table .billing-adjustment-text {
            color: #67757c !important;
            font-size: 12px !important;
            font-weight: normal !important;
            padding-top: 0px !important;
            border-top: 1px solid #B2DFDB !important;
        }

        /* ---- BANKING DETAILS IMAGE ---- */
        .pdf-page .bill-pdf .bill-totals-table-pdf .cims-banking-details {
            float: left;
            width: 45%;
            margin-top: 20px;
            padding: 0;
        }
        .pdf-page .bill-pdf .bill-totals-table-pdf .cims-banking-details img {
            width: 100%;
            height: auto;
        }

        /* ---- TERMS SECTION ---- */
        .pdf-page .bill-pdf .invoice-pdf-terms {
            clear: both;
            margin: 25px 30px 0 30px;
            padding: 12px 15px;
            background-color: #FAFAFA;
            border-left: 4px solid #E91E63;
            border-top: none;
        }
        .pdf-page .bill-pdf .invoice-pdf-terms h6 {
            color: #E91E63;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .pdf-page .bill-pdf .invoice-pdf-terms p,
        .pdf-page .bill-pdf .invoice-pdf-terms div {
            font-size: 10px;
            color: #555;
            line-height: 16px;
        }

        /* ---- BOTTOM ACCENT BAR ---- */
        .cims-bottom-bar {
            margin-top: 15px;
            padding: 10px 30px;
            background-color: #004D40;
            text-align: center;
            page-break-inside: avoid;
        }
        .cims-bottom-bar .cims-footer-disclaimer {
            padding: 0 20px 8px 20px;
            color: #B2DFDB;
            font-size: 12px;
            font-style: italic;
            letter-spacing: 0.3px;
        }
        .cims-bottom-bar .cims-footer-disclaimer span {
            display: block;
            line-height: 18px;
        }
        .cims-bottom-bar .cims-footer-company {
            padding: 6px 30px 0 30px;
            border-top: 1px solid #00796B;
            margin: 0 40px;
        }
        .cims-bottom-bar .cims-footer-company span {
            color: #80CBC4;
            font-size: 10px;
            letter-spacing: 1px;
        }

        /* ---- INLINE TAX MODE ADJUSTMENTS ---- */
        .pdf-page .bill-tax-inline .bill-addresses .x-line {
            line-height: 15px;
            height: 23px;
            padding: 0;
        }
        .pdf-page .bill-tax-inline .bill-table-pdf .invoice-table th.x-description {
            width: 300px;
        }
        .pdf-page .bill-tax-inline .bill-table-pdf .invoice-table td .tax-subtext {
            font-size: 9px;
            height: 15px;
            padding: 0;
            margin-top: -5px;
        }

        /* ---- UTILITY ---- */
        .text-wrap-new-lines {
            white-space: pre-line;
        }
        #invoice-table-section-total {
            text-align: right;
        }

        /* ---- BILL FILE ATTACHMENTS (hide in PDF) ---- */
        .bill-file-attachments {
            display: none;
        }
    </style>
</head>

<body class="pdf-page">

    <!-- TOP ACCENT BAR -->
    <div class="cims-top-bar"></div>

    <div
        class="bill-pdf {{ config('css.bill_mode') }} {{ $page['bill_mode'] ?? '' }} bill-tax-{{ $bill->bill_tax_type }}">

        <!--HEADER-->
        <div class="bill-header">
            <!--INVOICE HEADER-->
            @if($bill->bill_type =='invoice')
            <table>
                <tbody>
                    <tr>
                        <td class="x-left">
                            <div class="x-logo">
                                @if(request('view') == 'preview')
                                <img src="{{ url('/storage/logos/app/cims_inv_logo.png') }}" style="max-width: 350px; height: auto;">
                                @else
                                @php
                                $cimsLogoPath = BASE_DIR.'/storage/logos/app/cims_inv_logo.png';
                                $cimsLogoData = base64_encode(file_get_contents($cimsLogoPath));
                                $cimsLogoSrc = 'data:image/png;base64,' . $cimsLogoData;
                                @endphp
                                <img src="{{ $cimsLogoSrc }}" style="max-width: 350px; height: auto;">
                                @endif
                            </div>
                        </td>
                        <td class="x-right">
                            <div class="cims-header-address">
                                @if(config('system.settings_company_address_line_1'))
                                <div>{{ config('system.settings_company_address_line_1') }}</div>
                                @endif
                                @if(config('system.settings_company_city'))
                                <div>{{ config('system.settings_company_city') }}@if(config('system.settings_company_zipcode')), {{ config('system.settings_company_zipcode') }}@endif</div>
                                @endif
                                @if(config('system.settings_company_state'))
                                <div>{{ config('system.settings_company_state') }}</div>
                                @endif
                                @if(config('system.settings_company_country'))
                                <div>{{ config('system.settings_company_country') }}</div>
                                @endif
                                @if(config('system.settings_company_customfield_1') != '')
                                <div>{{ config('system.settings_company_customfield_1') }}</div>
                                @endif
                                @if(config('system.settings_company_customfield_2') != '')
                                <div>{{ config('system.settings_company_customfield_2') }}</div>
                                @endif
                                @if(config('system.settings_company_customfield_3') != '')
                                <div>{{ config('system.settings_company_customfield_3') }}</div>
                                @endif
                                @if(config('system.settings_company_customfield_4') != '')
                                <div>{{ config('system.settings_company_customfield_4') }}</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif
            <!--ESTIMATE HEADER-->
            @if($bill->bill_type =='estimate')
            <table>
                <tbody>
                    <tr>
                        <td class="x-left">
                            <div class="x-logo">
                                @if(request('view') == 'preview')
                                <img src="{{ url('/storage/logos/app/cims_inv_logo.png') }}" style="max-width: 350px; height: auto;">
                                @else
                                @php
                                $cimsLogoPath = BASE_DIR.'/storage/logos/app/cims_inv_logo.png';
                                $cimsLogoData = base64_encode(file_get_contents($cimsLogoPath));
                                $cimsLogoSrc = 'data:image/png;base64,' . $cimsLogoData;
                                @endphp
                                <img src="{{ $cimsLogoSrc }}" style="max-width: 350px; height: auto;">
                                @endif
                            </div>
                        </td>
                        <td class="x-right">
                            <div class="cims-header-address">
                                @if(config('system.settings_company_address_line_1'))
                                <div>{{ config('system.settings_company_address_line_1') }}</div>
                                @endif
                                @if(config('system.settings_company_city'))
                                <div>{{ config('system.settings_company_city') }}@if(config('system.settings_company_zipcode')), {{ config('system.settings_company_zipcode') }}@endif</div>
                                @endif
                                @if(config('system.settings_company_state'))
                                <div>{{ config('system.settings_company_state') }}</div>
                                @endif
                                @if(config('system.settings_company_country'))
                                <div>{{ config('system.settings_company_country') }}</div>
                                @endif
                                @if(config('system.settings_company_customfield_1') != '')
                                <div>{{ config('system.settings_company_customfield_1') }}</div>
                                @endif
                                @if(config('system.settings_company_customfield_2') != '')
                                <div>{{ config('system.settings_company_customfield_2') }}</div>
                                @endif
                                @if(config('system.settings_company_customfield_3') != '')
                                <div>{{ config('system.settings_company_customfield_3') }}</div>
                                @endif
                                @if(config('system.settings_company_customfield_4') != '')
                                <div>{{ config('system.settings_company_customfield_4') }}</div>
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif
        </div>

        <!--INVOICE/ESTIMATE STATUS BAR-->
        <div class="cims-status-bar">
            @if($bill->bill_type == 'invoice')
            <table>
                <tbody>
                    <tr>
                        <td class="x-left">
                            <span class="js-invoice-statuses">
                                <strong class="text-uppercase text-{{ runtimeInvoiceStatusColors($bill->bill_status, 'text') }}">
                                    {{ runtimeInvoiceStatusTitle($bill->bill_status) }}</strong>
                            </span>
                        </td>
                        <td class="x-right">
                            <strong style="color: #004D40; font-size: 16px; letter-spacing: 2px;">{{ cleanLang(__('lang.invoice')) }}</strong>
                            &nbsp;&nbsp;
                            <span style="color: #00796B; font-size: 13px;">{{ $bill->formatted_bill_invoiceid }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif
            @if($bill->bill_type == 'estimate')
            <table>
                <tbody>
                    <tr>
                        <td class="x-left">
                            <span class="js-estimate-statuses {{ runtimeEstimateStatus('draft', $bill->bill_status) }}"><strong class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">{{ cleanLang(__('lang.draft')) }}</strong></span>
                            <span class="js-estimate-statuses {{ runtimeEstimateStatus('new', $bill->bill_status) }}"><strong class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">{{ cleanLang(__('lang.new')) }}</strong></span>
                            <span class="js-estimate-statuses {{ runtimeEstimateStatus('accepted', $bill->bill_status) }}"><strong class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">{{ cleanLang(__('lang.accepted')) }}</strong></span>
                            <span class="js-estimate-statuses {{ runtimeEstimateStatus('declined', $bill->bill_status) }}"><strong class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">{{ cleanLang(__('lang.declined')) }}</strong></span>
                            <span class="js-estimate-statuses {{ runtimeEstimateStatus('revised', $bill->bill_status) }}"><strong class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">{{ cleanLang(__('lang.revised')) }}</strong></span>
                            <span class="js-estimate-statuses {{ runtimeEstimateStatus('expired', $bill->bill_status) }}"><strong class="text-uppercase {{ runtimeEstimateStatusColors($bill->bill_status, 'text') }}">{{ cleanLang(__('lang.expired')) }}</strong></span>
                        </td>
                        <td class="x-right">
                            <strong style="color: #004D40; font-size: 16px; letter-spacing: 2px;">{{ cleanLang(__('lang.estimate')) }}</strong>
                            &nbsp;&nbsp;
                            <span style="color: #00796B; font-size: 13px;">#{{ $bill->formatted_bill_estimateid }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            @endif
        </div>

        <!--ADDRESSES & DATES-->
        <div class="bill-addresses">
            <table>
                <tbody>
                    <tr>
                        <!--bill to (client) - left side-->
                        <td class="x-left bill-addresses-company">
                            <div class="x-company-name">
                                <h5 class="p-b-0 m-b-0"><strong>{{ $bill->client_company_name }}</strong></h5>
                            </div>
                            @if($bill->client_billing_street)
                            <div class="x-line x-street">
                                {{ $bill->client_billing_street }}
                            </div>
                            @endif
                            @if($bill->client_billing_city)
                            <div class="x-line x-city">
                                {{ $bill->client_billing_city }}@if($bill->client_billing_zip), {{ $bill->client_billing_zip }}@endif
                            </div>
                            @endif
                            @if($bill->client_billing_state)
                            <div class="x-line x-state">
                                {{ $bill->client_billing_state }}
                            </div>
                            @endif
                            @if($bill->client_billing_country)
                            <div class="x-line x-country">
                                {{ $bill->client_billing_country }}
                            </div>
                            @endif

                            <!--custom fields moved to right side-->

                            @if(config('system.settings_invoices_show_project_on_invoice') == 'yes' && $bill->project_title != '')
                            <div class="x-line" style="margin-top: 5px;">
                                <strong>@lang('lang.project'):</strong> {{ $bill->project_title }}
                            </div>
                            @endif
                        </td>
                        <td style="width: 20px;"></td>
                        <!--dates & payments - right side-->
                        <td class="x-right bill-addresses-client">
                            <div class="cims-dates-payments">
                                @if($bill->bill_type == 'invoice')
                                @include('pages.bill.components.elements.invoice.dates')
                                @include('pages.bill.components.elements.invoice.payments')
                                <!--custom fields (client code, vat etc)-->
                                <div class="invoice-dues" style="margin-top: 0;">
                                    <table style="border-collapse: collapse; border-spacing: 0;">
                                        @foreach($customfields as $field)
                                        @if($field->customfields_show_invoice == 'yes' && $field->customfields_status == 'enabled')
                                        @php $key = $field->customfields_name; @endphp
                                        @php $customfield = $bill[$key] ?? ''; @endphp
                                        @if($customfield != '')
                                        <tr>
                                            <td class="x-payments-lang" style="padding: 2px 10px 2px 0;">{{ strtoupper($field->customfields_title) }}</td>
                                            <td class="x-payments" style="padding: 2px 0;"><span>{{ runtimeCustomFieldsFormat($customfield, $field->customfields_datatype) }}</span></td>
                                        </tr>
                                        @endif
                                        @endif
                                        @endforeach
                                    </table>
                                </div>
                                @endif
                                @if($bill->bill_type == 'estimate')
                                @include('pages.bill.components.elements.estimate.dates')
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <!--DATES & AMOUNT DUE-->




        <!--INVOICE TABLE-->
        <div class="bill-table-pdf">
            @include('pages.bill.components.elements.main-table')
        </div>

        <!-- TOTAL & SUMMARY -->
        <div class="bill-totals-table-pdf">

            <!--BANKING DETAILS (left side)-->
            <div class="cims-banking-details">
                @if(request('view') == 'preview')
                <img src="{{ url('/storage/logos/app/banking_atp.png') }}">
                @else
                @php
                $bankImgPath = BASE_DIR.'/storage/logos/app/banking_atp.png';
                $bankImgData = base64_encode(file_get_contents($bankImgPath));
                $bankImgSrc = 'data:image/png;base64,' . $bankImgData;
                @endphp
                <img src="{{ $bankImgSrc }}">
                @endif
            </div>

            @if($bill->bill_tax_type == 'inline')
            @include('pages.bill.components.elements.totals-inline')
            @else
            @include('pages.bill.components.elements.totals-summary')
            @endif
        </div>

        <!--TERMS-->
        <div class="invoice-pdf-terms">
            <h6><strong>{{ cleanLang(__('lang.terms')) }}</strong></h6>
            {!! clean($bill->bill_terms) !!}
        </div>

        <!--module extension point-->
        @stack('bill_position_50')

    </div>

    <!-- BOTTOM ACCENT BAR -->
    <div class="cims-bottom-bar">
        <div class="cims-footer-disclaimer">
            <span>All services are provided subject to our standard Terms and Conditions of Business,</span>
            <span>a copy of which is available upon request.</span>
        </div>
        <div class="cims-footer-company">
            <span>{{ config('system.settings_company_name') }}</span>
        </div>
    </div>

</body>

</html>
