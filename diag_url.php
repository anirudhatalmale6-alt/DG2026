<?php
header('Content-Type: text/plain');

// Bootstrap Laravel to check what the helpers actually return
$app = require '/usr/www/users/smartucbmh/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

echo "asset('storage/logos/app/logo.png') = " . asset('storage/logos/app/logo.png') . "\n";
echo "asset('storage/logos/app/logo-small.png') = " . asset('storage/logos/app/logo-small.png') . "\n";
echo "asset('storage/avatars/system/avatar.jpg') = " . asset('storage/avatars/system/avatar.jpg') . "\n";
echo "url('/') = " . url('/') . "\n";
echo "config('app.url') = " . config('app.url') . "\n";

// Check what runtimeLogoSmall returns
if (function_exists('runtimeLogoSmall')) {
    echo "runtimeLogoSmall() = " . runtimeLogoSmall() . "\n";
}
if (function_exists('runtimeLogoLarge')) {
    echo "runtimeLogoLarge() = " . runtimeLogoLarge() . "\n";
}

// Check system settings for logo names
echo "settings_system_logo_small_name = " . config('system.settings_system_logo_small_name') . "\n";
echo "settings_system_logo_large_name = " . config('system.settings_system_logo_large_name') . "\n";
