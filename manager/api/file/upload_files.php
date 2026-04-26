<?php
declare(strict_types=1);
require_once __DIR__ . '/../../api_lib/_lib.php';
if (!isset($_FILES['files'])) tres('need FILE{files}!');
$dirs = $_POST['dirs'] ?? $_POST['dirs[]'] ?? [];
if (!is_array($dirs)) $dirs = [$dirs];
$names = $_FILES['files']['name'] ?? [];
$tmpNames = $_FILES['files']['tmp_name'] ?? [];
$errs = $_FILES['files']['error'] ?? [];
$count = is_array($names) ? count($names) : 0;
for ($i = 0; $i < $count; $i++) {
    if (($errs[$i] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) continue;
    $targetDirInput = (string)($dirs[$i] ?? '/');
    $targetDir = resolve_path($targetDirInput);
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $baseName = basename((string)$names[$i]);
    move_uploaded_file((string)$tmpNames[$i], $targetDir . '/' . $baseName);
}
tres('ok');

