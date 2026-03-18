<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/application/vendor/autoload.php';
$app = require_once __DIR__ . '/application/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// 1. Add period_id column if not exists
if (!Schema::hasColumn('cims_emp201_declarations', 'period_id')) {
    DB::statement("ALTER TABLE cims_emp201_declarations ADD COLUMN period_id INT UNSIGNED NULL AFTER pay_period");
    echo "Added period_id column.\n";
} else {
    echo "period_id column already exists.\n";
}

// 2. Backfill period_id for existing records based on period_combo + financial_year
$records = DB::table('cims_emp201_declarations')->whereNull('period_id')->get();
foreach ($records as $r) {
    if ($r->period_combo && $r->financial_year) {
        $period = DB::table('cims_document_periods')
            ->where('period_combo', $r->period_combo)
            ->where('tax_year', $r->financial_year)
            ->first();
        if ($period) {
            DB::table('cims_emp201_declarations')
                ->where('id', $r->id)
                ->update(['period_id' => $period->id]);
            echo "Updated record {$r->id}: period_id = {$period->id}\n";
        } else {
            echo "No matching period for record {$r->id} (combo={$r->period_combo}, year={$r->financial_year})\n";
        }
    }
}

echo "\nDone!\n";
