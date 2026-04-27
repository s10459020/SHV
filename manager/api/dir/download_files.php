<?php
declare(strict_types=1);
require_once __DIR__ . '/../../api_lib/_lib.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') tres('method not allowed', 405);
if (!class_exists('PharData')) tres('PharData not available', 500);

$baseInput = req('base');
// preferred: file_paths (json array). fallback: files
$filesJson = req('file_paths');
if ($filesJson === null || $filesJson === '') $filesJson = req('files');

if ($baseInput === null || $baseInput === '') tres('need POST{base}!', 400);
if ($filesJson === null || $filesJson === '') tres('need POST{file_paths}!', 400);

$base = resolve_path($baseInput);
if (!is_dir($base)) tres('base is not a directory: ' . $baseInput, 404);
$baseName = basename(rtrim(str_replace('\\', '/', $base), '/'));

$rows = json_decode($filesJson, true);
if (!is_array($rows)) tres('file_paths must be json array', 400);

$normRel = function (string $rel): string {
    $rel = str_replace('\\', '/', trim($rel));
    $rel = ltrim($rel, '/');
    $parts = [];
    foreach (explode('/', $rel) as $p) {
        if ($p === '' || $p === '.') continue;
        if ($p === '..') return '';
        if (str_contains($p, ':')) return '';
        $parts[] = $p;
    }
    return implode('/', $parts);
};

$selected = [];
$invalid = [];
foreach ($rows as $r) {
    if (!is_string($r)) continue;
    $rel = $normRel($r);
    if ($rel === '') { $invalid[] = (string)$r; continue; }

    // Accept both styles:
    // 1) relative to base: a/b.txt
    // 2) prefixed with base folder: data/a/b.txt
    $candidates = [$rel];
    if ($baseName !== '' && str_starts_with($rel, $baseName . '/')) {
        $candidates[] = substr($rel, strlen($baseName) + 1);
    } elseif ($baseName !== '') {
        $candidates[] = $baseName . '/' . $rel;
    }

    $found = false;
    foreach ($candidates as $cand) {
        if ($cand === '') continue;
        $abs = path_join($base, $cand);
        if (is_file($abs)) {
            $selected[$cand] = $abs;
            $found = true;
            break;
        }
    }
    if (!$found) $invalid[] = $rel;
}

if (count($selected) === 0) {
    $preview = implode(', ', array_slice($invalid, 0, 8));
    tres('no valid files selected (base=' . $baseInput . '), invalid: ' . $preview, 400);
}

$tmpBase = tempnam(sys_get_temp_dir(), 'mgrsel_');
if ($tmpBase === false) tres('temp create fail', 500);
@unlink($tmpBase);
$tarPath = $tmpBase . '.tar';
$tarGzPath = $tarPath . '.gz';

try {
    $tar = new PharData($tarPath);
    foreach ($selected as $rel => $abs) {
        $dir = dirname($rel);
        if ($dir !== '.' && $dir !== '') {
            $parts = explode('/', str_replace('\\', '/', $dir));
            $acc = '';
            foreach ($parts as $p) {
                $acc = $acc === '' ? $p : ($acc . '/' . $p);
                if (!$tar->offsetExists($acc)) $tar->addEmptyDir($acc);
            }
        }
        $tar->addFile($abs, $rel);
    }

    $tar->compress(Phar::GZ);
    unset($tar);

    if (!is_file($tarGzPath)) tres('tar.gz build fail', 500);
    $name = basename(rtrim(str_replace('\\', '/', $base), '/'));
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
