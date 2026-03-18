<?php
require_once '/usr/www/users/smartucbmh/application/vendor/autoload.php';
$app = require_once '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Check route exists
$routes = app('router')->getRoutes();
$found = false;
foreach ($routes as $route) {
    if (strpos($route->getName() ?? '', 'statement.send-email') !== false) {
        echo "Route found: " . $route->getName() . " => " . $route->uri() . " [" . implode(',', $route->methods()) . "]\n";
        $found = true;
    }
}
if (!$found) echo "send-email route NOT found\n";

// Test API returns email field
$req = Illuminate\Http\Request::create('/cims/emp201/api/statement-data', 'GET', ['client_id' => 16, 'tax_year' => 2026]);
$controller = new Modules\CIMS_EMP201\Http\Controllers\Emp201Controller();
$resp = $controller->apiStatementData($req);
$data = json_decode($resp->getContent(), true);
echo "Client email in API: " . ($data['client']['email'] ?? 'NOT FOUND') . "\n";
echo "Client name: " . ($data['client']['company_name'] ?? '') . "\n";
