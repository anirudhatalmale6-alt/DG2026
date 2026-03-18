<?php
echo "<pre>\n=== FIX TMP DIRECTORY ===\n\n";

$tmpDir = '/usr/home/smartucbmh/.tmp';
echo "Target tmp dir: $tmpDir\n";

if (is_dir($tmpDir)) {
    echo "Already exists\n";
    echo "Writable: " . (is_writable($tmpDir) ? 'YES' : 'NO') . "\n";
} else {
    echo "Creating directory...\n";
    $result = @mkdir($tmpDir, 0755, true);
    if ($result) {
        echo "Created successfully!\n";
        echo "Writable: " . (is_writable($tmpDir) ? 'YES' : 'NO') . "\n";
    } else {
        echo "FAILED to create: " . error_get_last()['message'] . "\n";

        // Try alternative - use /tmp which is in allowed paths
        echo "\nTrying /tmp as alternative...\n";
        echo "/tmp exists: " . (is_dir('/tmp') ? 'YES' : 'NO') . "\n";
        echo "/tmp writable: " . (is_writable('/tmp') ? 'YES' : 'NO') . "\n";
    }
}

// Test write
echo "\nWrite test:\n";
$testFile = $tmpDir . '/test_write_' . time();
$result = @file_put_contents($testFile, 'test');
if ($result !== false) {
    echo "Write to $tmpDir: SUCCESS\n";
    @unlink($testFile);
} else {
    echo "Write to $tmpDir: FAILED\n";

    // Try /tmp
    $testFile2 = '/tmp/test_write_' . time();
    $result2 = @file_put_contents($testFile2, 'test');
    if ($result2 !== false) {
        echo "Write to /tmp: SUCCESS\n";
        @unlink($testFile2);
    } else {
        echo "Write to /tmp: FAILED\n";
    }
}

echo "\n=== DONE ===\n</pre>";
