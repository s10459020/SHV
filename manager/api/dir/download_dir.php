<?php
declare(strict_types=1);
require_once __DIR__ . '/../../api_lib/_lib.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') tres('method not allowed', 405);
$pathInput = req('path');
if ($pathInput === null || $pathInput === '') tres('need POST{path}!', 400);

$dir = resolve_path($pathInput);
if (!is_dir($dir)) tres('base is not a directory: ' . $pathInput, 404);
if (!class_exists('PharData')) tres('PharData not available', 500);

$tmpBase = tempnam(sys_get_temp_dir(), 'mgrtar_');
if ($tmpBase === false) tres('temp create fail', 500);
@unlink($tmpBase);
$tarPath = $tmpBase . '.tar';
$tarGzPath = $tarPath . '.gz';

try {
    $tar = new PharData($tarPath);
    $base = rtrim(str_replace('\\', '/', $dir), '/');
    $baseLen = strlen($base) + 1;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $node) {
        $full = str_replace('\\', '/', $node->getPathname());
        $rel = substr($full, $baseLen);
        if ($rel === false || $rel === '') continue;
        if ($node->isDir()) {
            $tar->addEmptyDir($rel);
        } else {
            $tar->addFile($node->getPathname(), $rel);
        }
    }
    $tar->compress(Phar::GZ);
    unset($tar);

    if (!is_file($tarGzPath)) tres('tar.gz build fail', 500);
    $name = basename(rtrim(str_replace('\\', '/', $dir), '/'));
    if ($name === '') $name = 'selected';
    header('Content-Type: application/gzip');
    header('Content-Disposition: attachment; filename="' . $name . '.tar.gz"');
    header('Content-Length: ' . filesize($tarGzPath));
    readfile($tarGzPath);
    @unlink($tarPath);
    @unlink($tarGzPath);
    exit;
} catch (Throwable $e) {
    @unlink($tarPath);
    @unlink($tarGzPath);
    tres('tar.gz error: ' . $e->getMessage(), 500);
}

