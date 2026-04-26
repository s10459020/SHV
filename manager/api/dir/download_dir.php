<?php
declare(strict_types=1);
require_once __DIR__ . '/../_lib.php';
$path = req('path');
if ($path === null) tres('need GET{path}!');
$dir = resolve_path($path);
if (!is_dir($dir)) tres($path . ' is not a dir', 404);

$tmpZip = tempnam(sys_get_temp_dir(), 'mgrzip_');
if ($tmpZip === false) tres('zip create fail', 500);
$zipFile = $tmpZip . '.zip';
rename($tmpZip, $zipFile);

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE) !== true) tres('zip open fail', 500);
$baseLen = strlen(rtrim($dir, '/\\')) + 1;
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
foreach ($it as $f) {
    if (!$f->isFile()) continue;
    $full = $f->getPathname();
    $rel = str_replace('\\', '/', substr($full, $baseLen));
    $zip->addFile($full, $rel);
}
$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($dir) . '.zip"');
header('Content-Length: ' . filesize($zipFile));
readfile($zipFile);
@unlink($zipFile);
exit;
