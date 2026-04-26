<?php
declare(strict_types=1);
require_once __DIR__ . '/../_lib.php';
$dir = req('dir');
if ($dir === null) tres('need POST{dir}!');
$base = resolve_path($dir);
if (!is_dir($base)) tres('dir not exists: ' . $base, 404);
$i = 1;
$name = 'new-folder';
while (is_dir($base . '/' . $name)) { $i++; $name = 'new-folder(' . $i . ')'; }
mkdir($base . '/' . $name, 0777, true);
tres('create [' . rel_from_root($base . '/' . $name) . '/]');
