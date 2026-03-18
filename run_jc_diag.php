<?php
/**
 * Diagnostic: Check user types/roles
 * DELETE after use.
 */
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>Users Diagnostic</h2><pre>\n";

// Roles table columns
echo "=== ROLES TABLE COLUMNS ===\n";
try {
    $cols = DB::select("SHOW COLUMNS FROM roles");
    foreach ($cols as $c) echo "  {$c->Field} ({$c->Type})\n";
} catch (\Exception $e) { echo "  " . $e->getMessage() . "\n"; }

// Roles data
echo "\n=== ALL ROLES ===\n";
try {
    $roles = DB::select("SELECT * FROM roles");
    foreach ($roles as $r) {
        $arr = (array)$r;
        echo "  " . json_encode($arr) . "\n";
    }
} catch (\Exception $e) { echo "  " . $e->getMessage() . "\n"; }

// Users by type
echo "\n=== USERS BY TYPE ===\n";
try {
    $rows = DB::select("SELECT type, COUNT(*) as cnt FROM users WHERE status = 'active' GROUP BY type");
    foreach ($rows as $r) echo "  type='{$r->type}': {$r->cnt}\n";
} catch (\Exception $e) { echo "  " . $e->getMessage() . "\n"; }

// Users by role_id
echo "\n=== USERS BY ROLE_ID ===\n";
try {
    $rows = DB::select("SELECT role_id, COUNT(*) as cnt FROM users WHERE status = 'active' GROUP BY role_id");
    foreach ($rows as $r) echo "  role_id={$r->role_id}: {$r->cnt}\n";
} catch (\Exception $e) { echo "  " . $e->getMessage() . "\n"; }

// Sample team members (non-client types)
echo "\n=== SAMPLE USERS (first 15) ===\n";
try {
    $rows = DB::select("SELECT id, first_name, last_name, email, role_id, type FROM users WHERE status = 'active' ORDER BY first_name LIMIT 15");
    foreach ($rows as $u) {
        echo "  ID={$u->id} | {$u->first_name} {$u->last_name} | {$u->email} | role={$u->role_id} | type={$u->type}\n";
    }
} catch (\Exception $e) { echo "  " . $e->getMessage() . "\n"; }

echo "\n</pre>";
