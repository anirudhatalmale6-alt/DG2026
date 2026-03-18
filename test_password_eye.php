<?php
// Quick visual test to verify password eye toggle styling matches SmartDash theme
?>
<!DOCTYPE html>
<html>
<head>
<title>Password Eye Test</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
:root {
    --sd-primary: #17A2B8;
    --sd-primary-dark: #0d3d56;
    --sd-primary-light: #1496bb;
    --sd-border-radius: 8px;
    --sd-font-size-md: 20px;
    --sd-spacing-sm: 8px;
    --sd-spacing-md: 12px;
    --sd-input-height: 48px;
    --sd-font-family: 'Poppins', sans-serif;
    --sd-text-dark: #333333;
}
body { padding: 40px; background: #f0f2f5; }
.test-card { background: #fff; padding: 30px; border-radius: 12px; max-width: 700px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

/* Replicate SmartDash form-control styling */
.test-card .form-control {
    font-family: var(--sd-font-family) !important;
    font-size: var(--sd-font-size-md) !important;
    color: var(--sd-text-dark) !important;
    border: 2px solid var(--sd-primary) !important;
    border-radius: var(--sd-border-radius) !important;
    padding: var(--sd-spacing-sm) var(--sd-spacing-md);
    min-height: var(--sd-input-height);
    transition: all 0.2s ease;
}
.test-card .form-control:focus {
    border-color: var(--sd-primary-dark) !important;
    box-shadow: 0 0 0 3px rgba(23, 162, 184, 0.15) !important;
    outline: none !important;
}
.test-card .form-label {
    font-family: var(--sd-font-family);
    font-weight: 600;
    color: var(--sd-primary-dark);
    font-size: 15px;
}
</style>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="test-card">
<h5 style="color:#0d3d56; margin-bottom:25px; font-family:Poppins;">SARS E-Filing Login Details</h5>

<!-- Password field with eye toggle (new design) -->
<div class="mb-3">
    <label class="form-label">SARS Password</label>
    <div style="position:relative; display:flex; align-items:stretch;">
        <input type="text" class="form-control" value="********"
               style="border-top-right-radius:0 !important; border-bottom-right-radius:0 !important; border-right:none !important;">
        <button type="button" title="Show/Hide password" tabindex="-1"
                style="display:flex; align-items:center; justify-content:center; min-width:48px; padding:0 14px; border:2px solid #17A2B8; border-left:none; border-radius:0 8px 8px 0; background:linear-gradient(135deg, #0d3d56 0%, #1496bb 100%); color:#fff; font-size:18px; cursor:pointer; outline:none; transition:all 0.2s ease;">
            <i class="fa fa-eye"></i>
        </button>
    </div>
</div>

<!-- Normal field for comparison -->
<div class="mb-3">
    <label class="form-label">Email For SARS OTP</label>
    <input type="text" class="form-control" value="krish@example.com">
</div>

<!-- Another normal field for comparison -->
<div class="mb-3">
    <label class="form-label">SARS Username</label>
    <input type="text" class="form-control" value="KrishMoodley">
</div>

</div>
</body>
</html>
