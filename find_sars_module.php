<?php
/**
 * SARS Statement of Account Module Discovery Script
 * Scans nwidart/laravel-modules for SARS/statement/PDF related files
 * Self-deletes after execution
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

$baseDir = __DIR__ . '/Modules';
$output = [];

echo "=== SARS MODULE DISCOVERY SCRIPT ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "Base Dir: " . realpath($baseDir) . "\n\n";

// 1. List all module directories
if (!is_dir($baseDir)) {
    echo "ERROR: Modules directory not found at: $baseDir\n";
    echo "Trying alternative paths...\n";
    $altPaths = [
        dirname(__DIR__) . '/Modules',
        __DIR__ . '/../Modules',
        realpath(__DIR__ . '/..') . '/Modules',
    ];
    foreach ($altPaths as $alt) {
        echo "  Checking: $alt => " . (is_dir($alt) ? "EXISTS" : "NOT FOUND") . "\n";
        if (is_dir($alt)) {
            $baseDir = $alt;
            break;
        }
    }
}

if (!is_dir($baseDir)) {
    echo "\nFATAL: Cannot find Modules directory. Listing __DIR__ contents:\n";
    foreach (scandir(__DIR__) as $item) {
        if ($item === '.' || $item === '..') continue;
        $type = is_dir(__DIR__ . '/' . $item) ? '[DIR]' : '[FILE]';
        echo "  $type $item\n";
    }
    // Self-delete
    @unlink(__FILE__);
    exit(1);
}

// Get all module directories
$modules = [];
foreach (scandir($baseDir) as $item) {
    if ($item === '.' || $item === '..') continue;
    if (is_dir($baseDir . '/' . $item)) {
        $modules[] = $item;
    }
}

echo "=== ALL MODULES (" . count($modules) . ") ===\n";
foreach ($modules as $mod) {
    echo "  - $mod\n";
}
echo "\n";

// 2. Search keywords
$keywords = ['sars', 'statement', 'pdf', 'emp201', 'empsa', 'tax', 'declaration'];

// Recursive file scanner
function scanDirRecursive($dir, $relativeTo = '') {
    $results = [];
    if (!is_dir($dir)) return $results;
    $items = @scandir($dir);
    if (!$items) return $results;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        $relPath = $relativeTo ? $relativeTo . '/' . $item : $item;
        if (is_dir($path)) {
            $results = array_merge($results, scanDirRecursive($path, $relPath));
        } else {
            $results[] = $relPath;
        }
    }
    return $results;
}

// 3. For each module, check for keyword matches in filenames
echo "=== KEYWORD MATCHES IN MODULE FILES ===\n";
$matchedModules = [];

foreach ($modules as $mod) {
    $modDir = $baseDir . '/' . $mod;
    $files = scanDirRecursive($modDir);
    $matches = [];

    foreach ($files as $file) {
        $lower = strtolower($file);
        foreach ($keywords as $kw) {
            if (strpos($lower, $kw) !== false) {
                $matches[$file][] = $kw;
            }
        }
    }

    if (!empty($matches)) {
        $matchedModules[$mod] = $matches;
        echo "\n--- Module: $mod ---\n";
        foreach ($matches as $file => $kws) {
            echo "  [" . implode(', ', array_unique($kws)) . "] $file\n";
        }
    }
}

if (empty($matchedModules)) {
    echo "  No keyword matches found in any module filenames.\n";
}

// 4. Search route files for sars/statement references
echo "\n\n=== ROUTE FILE CONTENTS (sars/statement references) ===\n";
foreach ($modules as $mod) {
    $routesDir = $baseDir . '/' . $mod . '/Routes';
    if (!is_dir($routesDir)) continue;

    $routeFiles = @scandir($routesDir);
    if (!$routeFiles) continue;

    foreach ($routeFiles as $rf) {
        if ($rf === '.' || $rf === '..') continue;
        $routePath = $routesDir . '/' . $rf;
        if (!is_file($routePath)) continue;

        $content = @file_get_contents($routePath);
        if (!$content) continue;

        $lines = explode("\n", $content);
        $relevantLines = [];
        foreach ($lines as $lineNo => $line) {
            $lower = strtolower($line);
            if (strpos($lower, 'sars') !== false ||
                strpos($lower, 'statement') !== false ||
                strpos($lower, 'pdf') !== false ||
                strpos($lower, 'emp201') !== false ||
                strpos($lower, 'empsa') !== false ||
                strpos($lower, 'declaration') !== false) {
                $relevantLines[] = sprintf("  L%d: %s", $lineNo + 1, trim($line));
            }
        }

        if (!empty($relevantLines)) {
            echo "\n--- $mod/Routes/$rf ---\n";
            foreach ($relevantLines as $rl) {
                echo "$rl\n";
            }
        }
    }
}

// 5. Check for Controllers related to SARS/statement
echo "\n\n=== CONTROLLERS WITH SARS/STATEMENT REFERENCES ===\n";
foreach ($modules as $mod) {
    $ctrlDir = $baseDir . '/' . $mod . '/Http/Controllers';
    if (!is_dir($ctrlDir)) continue;

    $files = scanDirRecursive($ctrlDir);
    foreach ($files as $file) {
        $lower = strtolower($file);
        if (strpos($lower, 'sars') !== false ||
            strpos($lower, 'statement') !== false ||
            strpos($lower, 'pdf') !== false ||
            strpos($lower, 'emp201') !== false ||
            strpos($lower, 'empsa') !== false ||
            strpos($lower, 'declaration') !== false) {
            $fullPath = $ctrlDir . '/' . $file;
            echo "\n--- $mod/Http/Controllers/$file ---\n";

            // Show methods in the controller
            $content = @file_get_contents($fullPath);
            if ($content) {
                // Extract class and method signatures
                preg_match_all('/(?:public|protected|private)\s+function\s+(\w+)\s*\([^)]*\)/', $content, $methodMatches);
                if (!empty($methodMatches[1])) {
                    echo "  Methods:\n";
                    foreach ($methodMatches[1] as $method) {
                        echo "    - $method()\n";
                    }
                }

                // Find PDF-related lines
                $lines = explode("\n", $content);
                echo "  PDF-related lines:\n";
                foreach ($lines as $lineNo => $line) {
                    $lower = strtolower($line);
                    if (strpos($lower, 'pdf') !== false ||
                        strpos($lower, 'dompdf') !== false ||
                        strpos($lower, 'mpdf') !== false ||
                        strpos($lower, '->stream') !== false ||
                        strpos($lower, '->download') !== false ||
                        strpos($lower, '->loadview') !== false ||
                        strpos($lower, 'loadhtml') !== false) {
                        echo "    L" . ($lineNo + 1) . ": " . trim($line) . "\n";
                    }
                }
            }
        }
    }
}

// 6. Check for blade templates related to SARS/statement/PDF
echo "\n\n=== BLADE TEMPLATES (SARS/STATEMENT/PDF) ===\n";
foreach ($modules as $mod) {
    $viewsDir = $baseDir . '/' . $mod . '/Resources/views';
    if (!is_dir($viewsDir)) {
        // Try alternate views path
        $viewsDir = $baseDir . '/' . $mod . '/views';
        if (!is_dir($viewsDir)) continue;
    }

    $files = scanDirRecursive($viewsDir);
    foreach ($files as $file) {
        $lower = strtolower($file);
        if (strpos($lower, 'sars') !== false ||
            strpos($lower, 'statement') !== false ||
            strpos($lower, 'pdf') !== false ||
            strpos($lower, 'emp201') !== false ||
            strpos($lower, 'empsa') !== false ||
            strpos($lower, 'declaration') !== false) {
            echo "  $mod/views/$file\n";
        }
    }
}

// 7. Look at module.json files for SARS-related modules
echo "\n\n=== MODULE.JSON DETAILS (matched modules) ===\n";
foreach (array_keys($matchedModules) as $mod) {
    $jsonPath = $baseDir . '/' . $mod . '/module.json';
    if (file_exists($jsonPath)) {
        $json = @file_get_contents($jsonPath);
        echo "\n--- $mod/module.json ---\n";
        echo $json . "\n";
    }
}

// 8. Full directory tree for matched modules
echo "\n\n=== FULL FILE TREE (matched modules) ===\n";
foreach (array_keys($matchedModules) as $mod) {
    $modDir = $baseDir . '/' . $mod;
    $files = scanDirRecursive($modDir);
    echo "\n--- $mod (" . count($files) . " files) ---\n";
    foreach ($files as $file) {
        echo "  $file\n";
    }
}

// 9. Check modules_statuses.json
echo "\n\n=== MODULES STATUS ===\n";
$statusFiles = [
    __DIR__ . '/modules_statuses.json',
    dirname(__DIR__) . '/modules_statuses.json',
    __DIR__ . '/../modules_statuses.json',
];
foreach ($statusFiles as $sf) {
    if (file_exists($sf)) {
        echo "Found: $sf\n";
        echo file_get_contents($sf) . "\n";
        break;
    }
}

echo "\n\n=== DISCOVERY COMPLETE ===\n";

// Self-delete
@unlink(__FILE__);
echo "Script self-deleted.\n";
