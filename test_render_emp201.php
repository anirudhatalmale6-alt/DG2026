<?php
// Test that the EMP201 create page renders without PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a fake request to the EMP201 create page
$request = Illuminate\Http\Request::create('/cims/emp201/create', 'GET');
$request->setLaravelSession($app['session.store']);

// Login first
$user = \App\Models\User::first();
\Illuminate\Support\Facades\Auth::login($user);

// Handle the request through the kernel
try {
    $response = $kernel->handle($request);
    $html = $response->getContent();

    // Check for errors in response
    if ($response->getStatusCode() !== 200) {
        echo "Status: " . $response->getStatusCode() . "\n";
        echo substr($html, 0, 2000);
    } else {
        // Check for Year dropdown
        $hasYearSelect = strpos($html, 'id="tax_year"') !== false;
        $hasPeriodSelect = strpos($html, 'id="period_id"') !== false;
        $hasYearOptions = preg_match_all('/Select Year/', $html);

        echo "Page rendered OK (Status 200)\n";
        echo "Has Year dropdown: " . ($hasYearSelect ? 'YES' : 'NO') . "\n";
        echo "Has Period dropdown: " . ($hasPeriodSelect ? 'YES' : 'NO') . "\n";

        // Extract the Year/Period row HTML
        if (preg_match('/Row 2: Year.*?<\/div>\s*<\/div>\s*<\/div>\s*<\/div>/s', $html, $matches)) {
            echo "\nYear/Period Row HTML (trimmed):\n";
            echo substr($matches[0], 0, 1500);
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getFile() . ":" . $e->getLine();
}
