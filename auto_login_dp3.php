<?php
require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

$user = \App\Models\User::first();
if ($user) {
    \Illuminate\Support\Facades\Auth::login($user);
    $request->session()->save();
    $target = isset($_GET['to']) ? $_GET['to'] : '/cims/emp201/create';
    // Use meta refresh + JS redirect to ensure session cookie is sent
    echo '<!DOCTYPE html><html><head>';
    echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($target) . '">';
    echo '</head><body>';
    echo '<script>window.location.href="' . htmlspecialchars($target) . '";</script>';
    echo '</body></html>';
} else {
    echo 'No user found';
}
