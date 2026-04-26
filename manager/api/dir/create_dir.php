<?php
declare(strict_types=1);
require_once __DIR__ . '/../../api_lib/_lib.php';
$dir = req('dir');
if ($dir === null) tres('need POST{dir}!');
$base = resolve_path($dir);
if (!is_dir($base)) tres('dir not exists: ' . $base, 404);
$name = req('name');
if ($name !== null) {
    $name = trim($name);
    if ($name === '') tres('name cannot be empty', 400);
    if (str_contains($name, '/') || str_contains($name, '\\')) {
        tres('name cannot contain path separator', 400);
    }
    if (is_dir($base . '/' . $name)) tres('dir already exist', 409);
} else {
    $i = 1;
    $name = 'new-folder';
    while (is_dir($base . '/' . $name)) { $i++; $name = 'new-folder(' . $i . ')'; }
}
mkdir($base . '/' . $name, 0777, true);
tres('create [' . rel_from_root($base . '/' . $name) . '/]');

