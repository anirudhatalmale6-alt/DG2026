<?php
$cacheDir = __DIR__ . '/../storage/framework/views';
$count = 0;
if (is_dir($cacheDir)) {
    foreach (glob("$cacheDir/*.php") as $f) {
        unlink($f);
        $count++;
    }
}
echo json_encode(['status' => 'ok', 'cleared' => $count]);
