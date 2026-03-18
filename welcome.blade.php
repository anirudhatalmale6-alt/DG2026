<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to CIMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cims-blue-primary: #004b87;
            --cims-blue-secondary: #006aa7;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 25px;
        }

        .welcome-wrapper {
            width: 100%;
            max-width: 1200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* TOP SECTION - Logo & Title (Full Width) */
        .top-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .welcome-logo {
            max-width: 340px;
            width: 100%;
            height: auto;
            margin-bottom: 15px;
        }

        .welcome-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .welcome-subtitle {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--cims-blue-primary);
        }

        /* MIDDLE SECTION - Two Columns */
        .middle-section {
            display: flex;
            gap: 30px;
            margin-bottom: 25px;
        }

        .info-column {
            flex: 1;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--cims-blue-secondary);
        }

        .info-column h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--cims-blue-primary);
            margin-bottom: 12px;
        }

        .info-column p {
            font-size: 0.95rem;
            line-height: 1.7;
            color: #444;
            margin-bottom: 10px;
        }

        .info-column p:last-child {
            margin-bottom: 0;
        }

        /* BOTTOM SECTION - Checkbox & Button (Full Width) */
        .bottom-section {
            text-align: center;
            padding-top: 20px;
            border-top: 3px solid var(--cims-blue-secondary);
        }

        .consent-row {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
        }

        .consent-checkbox {
            width: 24px;
            height: 24px;
            border: 2px solid var(--cims-blue-secondary);
            cursor: pointer;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .consent-checkbox:checked {
            background-color: var(--cims-blue-secondary);
            border-color: var(--cims-blue-secondary);
        }

        .consent-label {
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }

        .begin-btn {
            display: inline-block;
            min-width: 300px;
            font-size: 1.15rem;
            font-weight: 600;
            padding: 14px 50px;
            background: var(--cims-blue-primary);
            border-color: var(--cims-blue-primary);
            border-radius: 6px;
        }

        .begin-btn:hover:not(:disabled) {
            background: var(--cims-blue-secondary);
            border-color: var(--cims-blue-secondary);
        }

        .begin-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 768px) {
            html, body {
                overflow: auto;
            }
            .middle-section {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>
<body>

<div class="welcome-wrapper">

    <!-- TOP: Logo & Company Name (Full Width) -->
    <div class="top-section">
        <img src="/public/modules/cimscore/images/atp_cims_logo.jpg"
             alt="Accounting Taxation and Payroll"
             class="welcome-logo">
        <h1 class="welcome-title">Welcome to the</h1>
        <h2 class="welcome-subtitle"><strong>Client Information Management System (CIMS)</strong></h2>
    </div>

    <!-- MIDDLE: Two Columns of Info -->
    <div class="middle-section">

        <!-- Left Column - About CIMS -->
        <div class="info-column">
            <h3>About This System</h3>
            <p>
                This system has been designed to guide you through the <strong>secure capture</strong>
                of company and statutory information required for <strong>regulatory compliance</strong>
                in South Africa.
            </p>
            <p>
                CIMS is used by <strong>Accounting, Taxation &amp; Payroll (Pty) Ltd</strong>
                to ensure that submissions to <strong>SARS, CIPC, COIDA, UIF</strong>, and
                related statutory bodies are <strong>accurate, complete, and compliant</strong>.
            </p>
        </div>

        <!-- Right Column - POPIA -->
        <div class="info-column">
            <h3>POPIA Declaration &amp; Consent</h3>
            <p>
                I confirm that the information I provide is <strong>true, accurate, and complete</strong>
                to the best of my knowledge.
            </p>
            <p>
                I understand that this information will be used for <strong>statutory, regulatory,
                and professional compliance purposes</strong>, including submission to relevant
                authorities where applicable.
            </p>
            <p>
                I consent to the collection, processing, and lawful sharing of this information
                in accordance with the <strong>Protection of Personal Information Act (POPIA)</strong>
                and related legislation.
            </p>
        </div>

    </div>

    <!-- BOTTOM: Checkbox & Button (Full Width) -->
    <div class="bottom-section">
        <div class="consent-row">
            <input class="form-check-input consent-checkbox"
                   type="checkbox"
                   id="popiaConsent">
            <label class="form-check-label consent-label" for="popiaConsent">
                I agree to the above declaration and consent
            </label>
        </div>

        <button class="btn btn-primary begin-btn" id="beginBtn" disabled onclick="window.location.href='{{ route('cimscore.landing') }}'">
            Begin Guided Process
        </button>
    </div>

</div>

<script>
    document.getElementById('popiaConsent').addEventListener('change', function() {
        document.getElementById('beginBtn').disabled = !this.checked;
    });
</script>

</body>
</html>
