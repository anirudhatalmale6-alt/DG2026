<?php
$f = glob(__DIR__ . '/application/storage/framework/views/*.php');
foreach ($f as $x) unlink($x);
echo json_encode(['cleared' => count($f)]);
