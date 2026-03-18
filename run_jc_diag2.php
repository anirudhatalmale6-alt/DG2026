<?php
/**
 * Diagnostic: Explore directors, persons, and files table structure
 * DELETE after use.
 */
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>Directors / Persons / Files Diagnostic</h2><pre>\n";

// 1. Find director-related tables
echo "=== TABLES WITH 'director' ===\n";
try {
    $tables = DB::select("SHOW TABLES");
    $dbKey = array_keys((array)$tables[0])[0];
    foreach ($tables as $t) {
        $name = $t->$dbKey;
        if (stripos($name, 'director') !== false) echo "  {$name}\n";
    }
} catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }

// 2. Find person-related tables
echo "\n=== TABLES WITH 'person' ===\n";
try {
    foreach ($tables as $t) {
        $name = $t->$dbKey;
        if (stripos($name, 'person') !== false) echo "  {$name}\n";
    }
} catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }

// 3. Find file/document-related tables
echo "\n=== TABLES WITH 'file' OR 'document' OR 'attachment' ===\n";
try {
    foreach ($tables as $t) {
        $name = $t->$dbKey;
        if (stripos($name, 'file') !== false || stripos($name, 'document') !== false || stripos($name, 'attachment') !== false) echo "  {$name}\n";
    }
} catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }

// 4. Find share-related tables
echo "\n=== TABLES WITH 'share' ===\n";
try {
    foreach ($tables as $t) {
        $name = $t->$dbKey;
        if (stripos($name, 'share') !== false) echo "  {$name}\n";
    }
} catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }

// 5. Directors table columns
echo "\n=== DIRECTOR TABLE COLUMNS ===\n";
$dirTables = [];
foreach ($tables as $t) {
    $name = $t->$dbKey;
    if (stripos($name, 'director') !== false) $dirTables[] = $name;
}
foreach ($dirTables as $dt) {
    echo "\nTable: {$dt}\n";
    try {
        $cols = DB::select("SHOW COLUMNS FROM `{$dt}`");
        foreach ($cols as $c) echo "  {$c->Field} ({$c->Type})\n";
    } catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }
}

// 6. Person table columns (if exists)
echo "\n=== PERSON TABLE COLUMNS ===\n";
$personTables = [];
foreach ($tables as $t) {
    $name = $t->$dbKey;
    if (stripos($name, 'person') !== false) $personTables[] = $name;
}
foreach ($personTables as $pt) {
    echo "\nTable: {$pt}\n";
    try {
        $cols = DB::select("SHOW COLUMNS FROM `{$pt}`");
        foreach ($cols as $c) echo "  {$c->Field} ({$c->Type})\n";
    } catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }
}

// 7. Files table columns
echo "\n=== FILE/DOCUMENT TABLES COLUMNS ===\n";
$fileTables = [];
foreach ($tables as $t) {
    $name = $t->$dbKey;
    if (stripos($name, 'file') !== false) $fileTables[] = $name;
}
foreach ($fileTables as $ft) {
    echo "\nTable: {$ft}\n";
    try {
        $cols = DB::select("SHOW COLUMNS FROM `{$ft}`");
        foreach ($cols as $c) echo "  {$c->Field} ({$c->Type})\n";
    } catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }
}

// 8. Sample director data (first 5 rows)
echo "\n=== SAMPLE DIRECTOR DATA ===\n";
foreach ($dirTables as $dt) {
    echo "\nTable: {$dt} (first 5 rows)\n";
    try {
        $rows = DB::select("SELECT * FROM `{$dt}` LIMIT 5");
        foreach ($rows as $r) {
            echo "  " . json_encode((array)$r) . "\n";
        }
        $cnt = DB::select("SELECT COUNT(*) as c FROM `{$dt}`");
        echo "  Total rows: {$cnt[0]->c}\n";
    } catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }
}

// 9. Sample file data for a person/director (if we can find it)
echo "\n=== SAMPLE FILE DATA ===\n";
foreach ($fileTables as $ft) {
    echo "\nTable: {$ft} (first 3 rows)\n";
    try {
        $rows = DB::select("SELECT * FROM `{$ft}` LIMIT 3");
        foreach ($rows as $r) {
            echo "  " . json_encode((array)$r) . "\n";
        }
        $cnt = DB::select("SELECT COUNT(*) as c FROM `{$ft}`");
        echo "  Total rows: {$cnt[0]->c}\n";
    } catch (\Exception $e) { echo "  ERROR: " . $e->getMessage() . "\n"; }
}

echo "\n</pre>";
