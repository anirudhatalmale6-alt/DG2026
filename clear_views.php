<?php
$viewPath = __DIR__ . '/../storage/framework/views';
$count = 0;
if (is_dir($viewPath)) {
    $files = glob($viewPath . '/*.php');
    foreach ($files as $file) {
        if (is_file($file) && unlink($file)) $count++;
    }
}
echo "Cleared $count compiled views.";
