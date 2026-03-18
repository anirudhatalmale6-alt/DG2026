<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once __DIR__ . '/application/vendor/autoload.php';
    $app = require_once __DIR__ . '/application/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle($request = Illuminate\Http\Request::capture());

    // Find first user
    $user = \App\Models\User::first();
    if (!$user) {
        echo 'No user found';
        exit;
    }

    echo "User found: " . $user->id . " - " . $user->email . "<br>";

    \Illuminate\Support\Facades\Auth::login($user);
    $request->session()->save();

    echo "Logged in. Session ID: " . session()->getId() . "<br>";

    $target = $_GET['to'] ?? '/cims/emp201/create';
    echo "Redirecting to: $target<br>";
    echo "<a href='$target'>Click here</a>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "<br>";
}
