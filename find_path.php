<?php
echo "Home dir: " . getenv('HOME') . "\n";
echo "Current user: " . get_current_user() . "\n";
$target = 'bill-pdf.blade.php';
function findFile($dir, $name) {
    try {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->getFilename() === $name) {
                echo "Found: " . $file->getPathname() . "\n";
            }
        }
    } catch (Exception $e) {}
}
findFile(__DIR__ . '/application/resources/views/pages/bill/', $target);
