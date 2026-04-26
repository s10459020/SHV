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
    if (is_file($base . '/' . $name)) tres('file already exist', 409);
} else {
    $i = 1;
    $name = 'new-file';
    while (is_file($base . '/' . $name)) { $i++; $name = 'new-file(' . $i . ')'; }
}
file_put_contents($base . '/' . $name, '');
tres('create [' . rel_from_root($base . '/' . $name) . ']');

