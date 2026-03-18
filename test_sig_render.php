<?php
// Standalone preview of the signature with logo, dividers, clickable WhatsApp
$name = 'Darryl Smith';
$title = 'Managing Director';
$phone = '+27 11 123 4567';
$direct = '+27 11 987 6543';
$mobile = '+27 82 555 1234';
$whatsapp = '+27 82 555 1234';
$company = 'Accounting Taxation and Payroll (Pty) Ltd';
$website = 'www.smartweigh.co.za';
$slogan = 'Adding Value - Ensuring Compliance';
$logoUrl = 'https://smartweigh.co.za/assets/cims_core/atp_cims_logo.jpg';
$websiteUrl = 'https://' . $website;

echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Signature Preview</title></head><body style="background:#f5f5f5;padding:40px;">';
echo '<div style="background:#fff;max-width:650px;margin:0 auto;padding:30px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">';
echo '<h3 style="margin-bottom:20px;color:#555;">Email Signature Preview</h3>';

$html = '<table cellpadding="0" cellspacing="0" style="font-family:Arial,sans-serif;font-size:13px;color:#333;border-collapse:collapse;width:100%;max-width:550px;">';

// Name + Title with pink accent line
$html .= '<tr><td style="padding-bottom:10px;border-bottom:3px solid #E91E8C;">';
$html .= '<strong style="font-size:16px;color:#1a1a2e;letter-spacing:0.5px;">' . $name . '</strong>';
$html .= '<br><span style="font-size:12px;color:#777;margin-top:2px;display:inline-block;">' . $title . '</span>';
$html .= '</td></tr>';

// Contact numbers row
$contactParts = [];
$contactParts[] = '<span style="color:#2196F3;">Tel:</span> ' . $phone;
$contactParts[] = '<span style="color:#9C27B0;">Direct:</span> ' . $direct;
$contactParts[] = '<span style="color:#FF6B35;">Mobile:</span> ' . $mobile;
$waNum = preg_replace('/[^0-9]/', '', $whatsapp);
$contactParts[] = '<span style="color:#25D366;">WhatsApp:</span> <a href="https://wa.me/' . $waNum . '" style="color:#444;text-decoration:none;" target="_blank">' . $whatsapp . '</a>';
$html .= '<tr><td style="padding:12px 0 10px 0;">';
$html .= '<span style="font-size:12px;color:#444;">' . implode(' &nbsp;<span style="color:#ccc;">|</span>&nbsp; ', $contactParts) . '</span>';
$html .= '</td></tr>';

// Thin elegant divider
$html .= '<tr><td style="padding:0;"><div style="border-top:1px solid #eee;"></div></td></tr>';

// Company row
$html .= '<tr><td style="padding-top:10px;">';
$html .= '<strong style="font-size:14px;color:#1a1a2e;">' . $company . '</strong>';
$html .= ' <span style="color:#ccc;">|</span> <a href="' . $websiteUrl . '" style="font-size:13px;color:#0066CC;text-decoration:none;">' . $website . '</a>';
$html .= '</td></tr>';

// Slogan row
$html .= '<tr><td style="padding-top:4px;">';
$html .= '<em style="font-size:12px;color:#E91E8C;font-style:italic;letter-spacing:0.3px;">' . $slogan . '</em>';
$html .= '</td></tr>';

// Subtle separator before logo
$html .= '<tr><td style="padding-top:14px;"><div style="border-top:1px solid #e0e0e0;"></div></td></tr>';

// Company logo with protection
$html .= '<tr><td style="padding-top:10px;">';
$html .= '<a href="' . $websiteUrl . '" target="_blank" style="text-decoration:none;">';
$html .= '<img src="' . $logoUrl . '" alt="CIMS" style="max-width:160px;height:auto;pointer-events:none;-webkit-user-drag:none;user-select:none;-moz-user-select:none;-webkit-user-select:none;-ms-user-select:none;" draggable="false" oncontextmenu="return false;" ondragstart="return false;">';
$html .= '</a>';
$html .= '</td></tr>';

$html .= '</table>';

echo $html;
echo '</div></body></html>';
