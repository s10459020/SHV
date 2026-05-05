<?php
declare(strict_types=1);
require_once __DIR__ . '/../api_lib/_lib.php';
$path = req('path');
if ($path === null) tres('?閬OST{path = (...)}');
$dir = resolve_path($path);
if (!is_dir($dir)) tres('閰脰??冗銝??? ' . $dir, 404);
$out = [];
foreach (scandir($dir) ?: [] as $n) {
    if ($n === '.' || $n === '..') continue;
    if (is_dir($dir . '/' . $n)) $out[] = $n;
}
jres($out);


