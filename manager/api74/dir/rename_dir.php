<?php
declare(strict_types=1);
require_once __DIR__ . '/../api_lib/_lib.php';
$path = req('path');
if ($path === null) tres('need POST{path}!');
$old = resolve_path($path);
if (!is_dir($old)) tres($old . ' is not a dir', 404);
$name = req('name');
if ($name === null || trim($name) === '') tres('need POST{name}!');
$new = dirname($old) . '/' . trim($name);
$new = ensure_in_root(normalize_joined($new));
if (is_dir($new)) tres('dir already exist', 409);
rename($old, $new);
tres('rename [' . rel_from_root($old) . '/] to [' . rel_from_root($new) . '/]');


