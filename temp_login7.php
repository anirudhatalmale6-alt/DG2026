<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$_SERVER['REQUEST_URI'] = '/_autologin_temp';
$_SERVER['REQUEST_METHOD'] = 'GET';
$app->make('router')->group(['middleware' => ['web']], function($router) {
    $router->get('/_autologin_temp', function() {
        \Illuminate\Support\Facades\Auth::login(\App\Models\User::first());
        return redirect('/cims/emp201');
    });
});
$request = \Illuminate\Http\Request::capture();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
