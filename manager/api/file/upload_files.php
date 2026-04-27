<?php
declare(strict_types=1);
require_once __DIR__ . '/../../api_lib/_lib.php';
if (!isset($_FILES['files'])) tres('need FILE{files}!');
$dirs = $_POST['dirs'] ?? $_POST['dirs[]'] ?? [];
if (!is_array($dirs)) $dirs = [$dirs];
$mtimes = $_POST['mtimes'] ?? $_POST['mtimes[]'] ?? [];
if (!is_array($mtimes)) $mtimes = [$mtimes];
$names = $_FILES['files']['name'] ?? [];
$tmpNames = $_FILES['files']['tmp_name'] ?? [];
$errs = $_FILES['files']['error'] ?? [];
$count = is_array($names) ? count($names) : 0;
$savedFiles = [];
$errors = [];
for ($i = 0; $i < $count; $i++) {
    $errCode = (int)($errs[$i] ?? UPLOAD_ERR_OK);
    if ($errCode !== UPLOAD_ERR_OK) {
        $errors[] = [
            'index' => $i,
            'name' => (string)($names[$i] ?? ''),
            'error' => 'upload err code: ' . $errCode,
        ];
        continue;
    }
    $targetDirInput = (string)($dirs[$i] ?? '/');
    $targetDir = resolve_path($targetDirInput);
    if (!is_dir($targetDir) && !@mkdir($targetDir, 0777, true) && !is_dir($targetDir)) {
        $errors[] = [
            'index' => $i,
            'name' => (string)($names[$i] ?? ''),
            'error' => 'mkdir fail: ' . $targetDir,
        ];
        continue;
    }
    $baseName = basename((string)$names[$i]);
    $targetFile = path_join($targetDir, $baseName);
    $ok = @move_uploaded_file((string)$tmpNames[$i], $targetFile);
    if (!$ok) {
        $errors[] = [
            'index' => $i,
            'name' => $baseName,
            'error' => 'move fail: ' . $targetFile,
        ];
        continue;
    }
    // Optional mtime restore (unix timestamp seconds or milliseconds)
    $rawMtime = (string)($mtimes[$i] ?? '');
    if ($rawMtime !== '') {
        $n = (int)$rawMtime;
        if ($n > 0) {
            if ($n > 20000000000) $n = (int)floor($n / 1000); // ms -> sec
            @touch($targetFile, $n);
        }
    }
    $savedFiles[] = rel_from_root($targetFile);
}
jres([
    'count' => $count,
    'files' => $savedFiles,
    'saved' => count($savedFiles),
    'errors' => $errors,
]);

