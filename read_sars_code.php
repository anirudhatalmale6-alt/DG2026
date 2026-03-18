<?php
/**
 * Deep-dive reader for SARS Statement of Account code
 * Reads key controller methods, blade templates, and services
 * Self-deletes after execution
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/plain; charset=utf-8');

$baseDir = '/usr/www/users/smartucbmh/application/Modules';

echo "=== SARS STATEMENT CODE DEEP-DIVE ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Files to read in full
$files = [
    // CIMS_EMP201 - Statement of Account (EMPSA)
    'CIMS_EMP201/Http/Controllers/Emp201Controller.php',
    'CIMS_EMP201/Resources/views/emp201/statement.blade.php',
    'CIMS_EMP201/Resources/views/emp201/statement_pdf.blade.php',
    'CIMS_EMP201/Routes/web.php',

    // CustomerStatements module
    'CustomerStatements/Http/Controllers/StatementController.php',
    'CustomerStatements/Resources/views/pdf.blade.php',
    'CustomerStatements/Resources/views/statement.blade.php',
    'CustomerStatements/Services/StatementService.php',
    'CustomerStatements/Http/routes.php',

    // SARSRepresentative module
    'SARSRepresentative/Http/Controllers/SarsRepController.php',
    'SARSRepresentative/Routes/web.php',
];

foreach ($files as $relPath) {
    $fullPath = $baseDir . '/' . $relPath;
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "FILE: $relPath\n";
    echo str_repeat('=', 80) . "\n";

    if (!file_exists($fullPath)) {
        echo "[FILE NOT FOUND]\n";
        continue;
    }

    $content = @file_get_contents($fullPath);
    if ($content === false) {
        echo "[COULD NOT READ]\n";
        continue;
    }

    $size = strlen($content);
    echo "Size: $size bytes\n";

    // For large controller files, only show statement/pdf-related methods
    if ($size > 20000 && strpos($relPath, 'Controller') !== false) {
        echo "[LARGE FILE - showing statement/pdf related sections only]\n\n";

        // Extract methods related to statement/pdf
        $lines = explode("\n", $content);
        $inRelevantMethod = false;
        $braceCount = 0;
        $methodStartLine = 0;
        $methodBuffer = '';

        // Also show use statements and class declaration
        $headerShown = false;
        foreach ($lines as $i => $line) {
            if (!$headerShown && (preg_match('/^(namespace|use |class )/', trim($line)) || $i < 20)) {
                echo sprintf("L%d: %s\n", $i + 1, $line);
                if (preg_match('/^class /', trim($line))) {
                    $headerShown = true;
                    echo "...\n\n";
                }
                continue;
            }

            $lower = strtolower($line);

            // Detect start of a relevant method
            if (!$inRelevantMethod && preg_match('/(?:public|protected|private)\s+function\s+(\w+)/i', $line, $m)) {
                $methodName = strtolower($m[1]);
                if (strpos($methodName, 'statement') !== false ||
                    strpos($methodName, 'pdf') !== false ||
                    strpos($methodName, 'sars') !== false ||
                    strpos($methodName, 'email') !== false ||
                    strpos($methodName, 'empsa') !== false) {
                    $inRelevantMethod = true;
                    $braceCount = 0;
                    $methodStartLine = $i + 1;
                    $methodBuffer = '';
                }
            }

            if ($inRelevantMethod) {
                $methodBuffer .= sprintf("L%d: %s\n", $i + 1, $line);
                $braceCount += substr_count($line, '{') - substr_count($line, '}');
                if ($braceCount <= 0 && $methodStartLine != ($i + 1)) {
                    echo $methodBuffer . "\n";
                    $inRelevantMethod = false;
                    $methodBuffer = '';
                }
            }
        }

        // If still in a method at end of file
        if ($inRelevantMethod && $methodBuffer) {
            echo $methodBuffer . "\n";
        }
    } else {
        // Show full file
        $lines = explode("\n", $content);
        foreach ($lines as $i => $line) {
            echo sprintf("L%d: %s\n", $i + 1, $line);
        }
    }
}

echo "\n\n=== DEEP-DIVE COMPLETE ===\n";

// Self-delete
@unlink(__FILE__);
echo "Script self-deleted.\n";
