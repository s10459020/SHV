<?php
declare(strict_types=1);
require_once __DIR__ . '/../_lib.php';
$path = req('path');
if ($path === null) tres('need POST{path}!');
$dir = resolve_path($path);
if (!is_dir($dir)) tres($path . ' is not a directory', 404);

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::CHILD_FIRST
);
foreach ($it as $node) {
    if ($node->isDir()) {
        rmdir($node->getPathname());
    } else {
        unlink($node->getPathname());
    }
}
rmdir($dir);
tres('delete [' . rel_from_root($dir) . '/]');
