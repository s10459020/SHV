<?php
declare(strict_types=1);
require_once __DIR__ . '/../_lib.php';
$path = req('path');
$name = req('name');
if ($path === null) tres('need POST{path}!');
if ($name === null || trim($name) === '') tres('need POST{name}!');
$file = resolve_path($path);
if (!is_file($file)) tres($file . ' is not a file', 404);
$new = dirname($file) . '/' . trim($name);
$new = ensure_in_root(normalize_joined($new));
if (is_file($new)) tres('file already exist', 409);
rename($file, $new);
tres('rename [' . rel_from_root($file) . '] to [' . rel_from_root($new) . ']');
