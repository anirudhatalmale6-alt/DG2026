<?php
header('Content-Type: text/plain');

require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$targetBase = public_path('storage');
$source = storage_path('app/public');

echo "=== Copying files to public_path('storage') ===\n";
echo "Source: {$source}\n";
echo "Target: {$targetBase}\n\n";

// Try symlink first
if (!file_exists($targetBase)) {
    $result = @symlink($source, $targetBase);
    if ($result) {
        echo "Symlink created successfully!\n";
        echo "Verifying: is_link=" . (is_link($targetBase) ? 'YES' : 'NO') . "\n";

        // Test file access
        $doc = \DB::table('cims_documents')->where('client_code', 'ATP100')->whereNotNull('file_path')->where('file_path', '!=', '')->first();
        if ($doc) {
            $testPath = public_path("storage/" . $doc->file_path);
            echo "Test: {$testPath}\n";
            echo "exists: " . (file_exists($testPath) ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "Symlink failed, falling back to directory copy...\n\n";

        // Copy recursively
        function copyDir($src, $dst) {
            $count = 0;
            if (!is_dir($dst)) mkdir($dst, 0755, true);
            $items = array_diff(scandir($src), ['.', '..']);
            foreach ($items as $item) {
                $srcPath = $src . '/' . $item;
                $dstPath = $dst . '/' . $item;
                if (is_dir($srcPath)) {
                    $count += copyDir($srcPath, $dstPath);
                } else {
                    if (!file_exists($dstPath) || filesize($srcPath) !== filesize($dstPath)) {
                        copy($srcPath, $dstPath);
                        $count++;
                    }
                }
            }
            return $count;
        }

        $copied = copyDir($source, $targetBase);
        echo "Copied {$copied} files\n\n";

        // Verify
        $doc = \DB::table('cims_documents')->where('client_code', 'ATP100')->whereNotNull('file_path')->where('file_path', '!=', '')->first();
        if ($doc) {
            $testPath = public_path("storage/" . $doc->file_path);
            echo "Test: {$testPath}\n";
            echo "exists: " . (file_exists($testPath) ? 'YES' : 'NO') . "\n";
        }
    }
} else {
    echo "Target already exists!\n";
    echo "is_dir: " . (is_dir($targetBase) ? 'YES' : 'NO') . "\n";
    echo "is_link: " . (is_link($targetBase) ? 'YES' : 'NO') . "\n";

    // Still need to check if files are there
    $doc = \DB::table('cims_documents')->where('client_code', 'ATP100')->whereNotNull('file_path')->where('file_path', '!=', '')->first();
    if ($doc) {
        $testPath = public_path("storage/" . $doc->file_path);
        echo "Test: {$testPath}\n";
        echo "exists: " . (file_exists($testPath) ? 'YES' : 'NO') . "\n";
    }
}
