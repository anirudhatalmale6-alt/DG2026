<?php
/**
 * CIMSDocumentGenerator Setup Script
 * Extracts vendor libraries and cleans up cache
 * DELETE THIS FILE AFTER RUNNING
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$basePath = dirname(__FILE__);
$vendorPath = $basePath . '/vendor';
$zipPath = $basePath . '/vendor_libs.zip';

echo "<pre>\n";
echo "=== CIMSDocumentGenerator Setup ===\n\n";

// Step 1: Extract vendor_libs.zip
if (file_exists($zipPath)) {
    echo "1. Extracting vendor libraries...\n";

    $zip = new ZipArchive();
    if ($zip->open($zipPath) === TRUE) {
        // Extract to a temp location first
        $tempDir = $basePath . '/vendor_libs_temp';
        $zip->extractTo($tempDir);
        $zip->close();

        // Move setasign and tecnickcom to vendor
        $dirs = ['setasign', 'tecnickcom'];
        foreach ($dirs as $dir) {
            $src = $tempDir . '/vendor_files/' . $dir;
            $dst = $vendorPath . '/' . $dir;

            if (is_dir($src)) {
                if (!is_dir($dst)) {
                    rename($src, $dst);
                    echo "   - Installed: vendor/{$dir}\n";
                } else {
                    echo "   - Already exists: vendor/{$dir} (skipped)\n";
                }
            }
        }

        // Clean up temp
        deleteDir($tempDir);
        echo "   - Cleaned up temp files\n";
    } else {
        echo "   ERROR: Could not open zip file\n";
    }
} else {
    echo "1. vendor_libs.zip not found (skipped)\n";

    // Check if already installed
    if (is_dir($vendorPath . '/setasign') && is_dir($vendorPath . '/tecnickcom')) {
        echo "   - Libraries already installed\n";
    } else {
        echo "   WARNING: Vendor libraries not found!\n";
    }
}

// Step 2: Clear Laravel cache
echo "\n2. Clearing Laravel cache...\n";

$cacheDirs = [
    $basePath . '/bootstrap/cache',
    $basePath . '/storage/framework/cache',
    $basePath . '/storage/framework/views',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            if (basename($file) !== '.gitignore') {
                @unlink($file);
            }
        }
        echo "   - Cleared: " . basename(dirname($dir)) . '/' . basename($dir) . "\n";
    }
}

// Step 3: Verify module exists
echo "\n3. Verifying module...\n";
$modulePath = $basePath . '/Modules/CIMSDocumentGenerator';
if (is_dir($modulePath)) {
    echo "   - Module folder: EXISTS\n";

    $moduleJson = $modulePath . '/module.json';
    if (file_exists($moduleJson)) {
        $json = json_decode(file_get_contents($moduleJson), true);
        if ($json) {
            echo "   - module.json: VALID (name: {$json['name']})\n";
        } else {
            echo "   - module.json: INVALID JSON!\n";
        }
    } else {
        echo "   - module.json: MISSING!\n";
    }

    $sp = $modulePath . '/Providers/CIMSDocumentGeneratorServiceProvider.php';
    echo "   - ServiceProvider: " . (file_exists($sp) ? "EXISTS" : "MISSING!") . "\n";
    echo "   - Routes: " . (file_exists($modulePath . '/Routes/web.php') ? "EXISTS" : "MISSING!") . "\n";
    echo "   - Controller: " . (file_exists($modulePath . '/Http/Controllers/DocgenController.php') ? "EXISTS" : "MISSING!") . "\n";
} else {
    echo "   - Module folder: MISSING!\n";
}

// Step 4: Check modules_statuses.json
echo "\n4. Checking modules_statuses.json...\n";
$statusFile = $basePath . '/modules_statuses.json';
if (file_exists($statusFile)) {
    $statuses = json_decode(file_get_contents($statusFile), true);
    if ($statuses) {
        echo "   - File: VALID JSON\n";
        if (isset($statuses['CIMSDocumentGenerator'])) {
            echo "   - CIMSDocumentGenerator: " . ($statuses['CIMSDocumentGenerator'] ? 'ENABLED' : 'DISABLED') . "\n";
        } else {
            echo "   - CIMSDocumentGenerator: NOT FOUND - Adding...\n";
            $statuses['CIMSDocumentGenerator'] = true;
            file_put_contents($statusFile, json_encode($statuses, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            echo "   - CIMSDocumentGenerator: ADDED & ENABLED\n";
        }
    } else {
        echo "   - File: INVALID JSON!\n";
    }
} else {
    echo "   - File: NOT FOUND!\n";
}

// Step 5: Check vendor libraries
echo "\n5. Checking vendor libraries...\n";
echo "   - setasign/fpdi: " . (file_exists($vendorPath . '/setasign/fpdi/src/Fpdi.php') ? "OK" : "MISSING!") . "\n";
echo "   - setasign/fpdi-tcpdf: " . (is_dir($vendorPath . '/setasign/fpdi-tcpdf') ? "OK" : "MISSING!") . "\n";
echo "   - tecnickcom/tcpdf: " . (file_exists($vendorPath . '/tecnickcom/tcpdf/tcpdf.php') ? "OK" : "MISSING!") . "\n";

echo "\n=== Setup Complete ===\n";
echo "\nURL to test: /cims/document-generator/\n";
echo "\n*** IMPORTANT: DELETE THIS FILE (setup_docgen.php) FROM YOUR SERVER ***\n";
echo "</pre>\n";

function deleteDir($dir) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            deleteDir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}
