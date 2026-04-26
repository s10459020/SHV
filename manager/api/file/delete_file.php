<?php
declare(strict_types=1);
require_once __DIR__ . '/../_lib.php';
$path = req('path');
if ($path === null) tres('need POST{path}!');
$file = resolve_path($path);
if (!is_file($file)) tres($path . ' is not a file', 404);
unlink($file);
tres('delete [' . rel_from_root($file) . ']');
