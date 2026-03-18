<?php
$file = __DIR__ . '/Modules/CIMSDocManager/Resources/views/documents/form.blade.php';
$content = file_get_contents($file);
if (strpos($content, "if (!function_exists('formatDateValue'))") !== false) {
    echo "FIX APPLIED: form.blade.php has function_exists guard";
} else {
    echo "FIX NOT APPLIED: form.blade.php still missing guard";
}
unlink(__FILE__);
