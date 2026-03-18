<?php
/**
 * Diagnostic: Check users table structure and data
 * DELETE after use.
 */
require __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>Users Table Diagnostic</h2><pre>\n";

// 1. Show table columns
echo "=== COLUMNS ===\n";
$cols = DB::select("SHOW COLUMNS FROM users");
foreach ($cols as $c) {
    echo "  {$c->Field} ({$c->Type}) " . ($c->Null === 'YES' ? 'NULL' : 'NOT NULL') . "\n";
}

// 2. Count all users
$total = DB::table('users')->count();
echo "\nTotal users: {$total}\n";

// 3. Count by status
echo "\n=== BY STATUS ===\n";
$byStatus = DB::select("SELECT status, COUNT(*) as cnt FROM users GROUP BY status");
foreach ($byStatus as $row) {
    echo "  status={$row->status}: {$row->cnt} users\n";
}

// 4. Show first 10 users (key fields only)
echo "\n=== FIRST 10 USERS ===\n";
$users = DB::table('users')->limit(10)->get();
foreach ($users as $u) {
    $id = $u->id ?? '?';
    $fn = $u->first_name ?? ($u->firstname ?? ($u->name ?? '?'));
    $ln = $u->last_name ?? ($u->lastname ?? ($u->surname ?? ''));
    $email = $u->email ?? '?';
    $status = $u->status ?? '?';
    echo "  ID={$id} | Name={$fn} {$ln} | Email={$email} | Status={$status}\n";
}

echo "\n=== getUsers() RESULT ===\n";
try {
    $result = DB::table('users')
        ->where('status', 1)
        ->select(['id', 'first_name', 'last_name', 'email'])
        ->orderBy('first_name')
        ->get();
    echo "Count: " . $result->count() . "\n";
    foreach ($result as $u) {
        echo "  ID={$u->id} | {$u->first_name} {$u->last_name} | {$u->email}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
