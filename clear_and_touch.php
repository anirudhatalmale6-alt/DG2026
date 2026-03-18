<?php
$viewPath = '/usr/www/users/smartucbmh/application/storage/framework/views/';
$count = 0;
foreach (glob($viewPath . '*.php') as $file) { unlink($file); $count++; }
$sources = [
    '/usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/tax-tables/index.blade.php',
    '/usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/deduction-types/index.blade.php',
    '/usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/contribution-types/index.blade.php',
    '/usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Resources/views/payroll/employees/index.blade.php',
    '/usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Providers/CIMSPAYROLLServiceProvider.php',
    '/usr/www/users/smartucbmh/application/Modules/CIMS_PAYROLL/Helpers/helpers.php',
];
foreach ($sources as $src) { if (file_exists($src)) touch($src); }
$bootstrap = '/usr/www/users/smartucbmh/application/bootstrap/cache/';
foreach (['services.php', 'packages.php'] as $f) { $fp = $bootstrap . $f; if (file_exists($fp)) unlink($fp); }
echo "Cleared $count views, touched sources, cache bumped";
