<?php
declare(strict_types=1);
require_once __DIR__ . '/../_lib.php';
$path = req('path');
if ($path === null) tres('need POST{path}!');
$file = resolve_path($path);
if (!is_file($file)) tres($path . ' is not a file', 404);
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;
