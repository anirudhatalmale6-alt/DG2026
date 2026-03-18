<?php
require __DIR__.'/application/vendor/autoload.php';
$app = require_once __DIR__.'/application/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$periods = DB::table('cims_document_periods')->where('is_active',1)->orderByDesc('display_order')->limit(5)->get(['id','period_name','tax_year','period_combo']);
foreach($periods as $p) { echo $p->id.' | '.$p->period_name.' | '.$p->tax_year.' | '.$p->period_combo."\n"; }
