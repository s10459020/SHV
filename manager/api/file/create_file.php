<?php
declare(strict_types=1);
require_once __DIR__ . '/../_lib.php';
$dir = req('dir');
if ($dir === null) tres('need POST{dir}!');
$base = resolve_path($dir);
if (!is_dir($base)) tres('dir not exists: ' . $base, 404);
$i = 1;
$name = 'new-file';
while (is_file($base . '/' . $name)) { $i++; $name = 'new-file(' . $i . ')'; }
file_put_contents($base . '/' . $name, '');
tres('create [' . rel_from_root($base . '/' . $name) . ']');
