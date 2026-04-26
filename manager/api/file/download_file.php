<?php
declare(strict_types=1);
require_once __DIR__ . '/../../api_lib/_lib.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') tres('method not allowed', 405);
$path = isset($_GET['path']) ? (string)$_GET['path'] : null;
if ($path === null || $path === '') tres('need GET{path}!');
$file = resolve_path($path);
if (!is_file($file)) tres($path . ' is not a file', 404);
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file) . '"');
header('Content-Length: ' . filesize($file));
readfile($file);
exit;

