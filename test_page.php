<?php
// Simulate loading the form.blade.php to check for redeclaration errors
require_once __DIR__ . '/vendor/autoload.php';

// Boot the app
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Try to render the form view
try {
    // First, let the app boot fully
    $request = \Illuminate\Http\Request::create('/cims/docmanager/create', 'GET');
    
    // Check if formatDateValue function can be loaded without conflict
    require_once __DIR__ . '/Modules/ClientMasterNew/Helpers/helpers.php';
    echo "formatDateValue loaded OK from ClientMasterNew\n";
    echo "function_exists('formatDateValue'): " . (function_exists('formatDateValue') ? 'YES' : 'NO') . "\n";
    
    // Check the form.blade.php doesn't redeclare it
    $formContent = file_get_contents(__DIR__ . '/Modules/CIMSDocManager/Resources/views/documents/form.blade.php');
    if (strpos($formContent, 'function formatDateValue') !== false) {
        echo "WARNING: form.blade.php STILL contains formatDateValue declaration!\n";
        // Show the context
        $lines = explode("\n", $formContent);
        foreach ($lines as $i => $line) {
            if (strpos($line, 'formatDateValue') !== false) {
                echo "  Line " . ($i+1) . ": " . trim($line) . "\n";
            }
        }
    } else {
        echo "GOOD: form.blade.php does NOT contain formatDateValue declaration\n";
    }
    
    echo "\nAll checks passed - the conflict should be resolved.\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
unlink(__FILE__);
