<?php
declare(strict_types=1);

function download_normalize_rel_path(string $rel): string {
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
}

function download_collect_selected_files(string $base, array $rows): array {
    $selected = [];
    $invalid = [];
    foreach ($rows as $r) {
        if (!is_string($r)) continue;
        $rel = download_normalize_rel_path($r);
        if ($rel === '') { $invalid[] = (string)$r; continue; }
        $abs = path_join($base, $rel);
        if (is_file($abs)) {
            $selected[$rel] = $abs;
        } else {
            $invalid[] = $rel;
        }
    }
    return [$selected, $invalid];
}

function download_list_dir_file_rows(string $dir): array {
    $rows = [];
    $base = rtrim(str_replace('\\', '/', $dir), '/');
    $baseLen = strlen($base) + 1;
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $node) {
        if (!$node->isFile()) continue;
        $full = str_replace('\\', '/', $node->getPathname());
        $rel = substr($full, $baseLen);
        if ($rel === false || $rel === '') continue;
        $rows[] = $rel;
    }
    return $rows;
}

function download_stream_selected_zip(string $base, array $selected): void {
    $zipPath = tempnam(sys_get_temp_dir(), 'mgrzip_');
    if ($zipPath === false) tres('temp create fail', 500);

    try {
        $fp = fopen($zipPath, 'wb');
        if ($fp === false) tres('zip open fail', 500);

        $central = '';
        $offset = 0;
        $count = 0;

        $toDos = function (int $ts): array {
            $d = getdate($ts);
            $year = max(1980, (int)$d['year']);
            $dosTime = (($d['hours'] & 0x1F) << 11) | (($d['minutes'] & 0x3F) << 5) | ((int)floor($d['seconds'] / 2) & 0x1F);
            $dosDate = ((($year - 1980) & 0x7F) << 9) | (($d['mon'] & 0x0F) << 5) | ($d['mday'] & 0x1F);
            return [$dosTime, $dosDate];
        };

        foreach ($selected as $rel => $abs) {
            $name = str_replace('\\', '/', $rel);
            $size = filesize($abs);
            if ($size === false) continue;
            $crcHex = hash_file('crc32b', $abs);
            if ($crcHex === false) continue;
            $crc = (int)hexdec($crcHex);
            [$dosTime, $dosDate] = $toDos(filemtime($abs) ?: time());

            $local = pack(
                'VvvvvvVVVvv',
                0x04034b50,
                20,
                0,
                0,
                $dosTime,
                $dosDate,
                $crc,
                $size,
                $size,
                strlen($name),
                0
            ) . $name;
            fwrite($fp, $local);

            $in = fopen($abs, 'rb');
            if ($in !== false) {
                stream_copy_to_stream($in, $fp);
                fclose($in);
            }

            $central .= pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                0x0314,
                20,
                0,
                0,
                $dosTime,
                $dosDate,
                $crc,
                $size,
                $size,
                strlen($name),
                0,
                0,
                0,
                0,
                0,
                $offset
            ) . $name;

            $offset += strlen($local) + $size;
            $count++;
        }

        $centralOffset = $offset;
        fwrite($fp, $central);
        $centralSize = strlen($central);
        $end = pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            $count,
            $count,
            $centralSize,
            $centralOffset,
            0
        );
        fwrite($fp, $end);
        fclose($fp);

        if (!is_file($zipPath)) tres('zip build fail', 500);
        $name = basename(rtrim(str_replace('\\', '/', $base), '/'));
        if ($name === '') $name = 'selected';
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $name . '.zip"');
        header('Content-Length: ' . filesize($zipPath));
        readfile($zipPath);
        @unlink($zipPath);
        exit;
    } catch (Throwable $e) {
        @unlink($zipPath);
        tres('zip error: ' . $e->getMessage(), 500);
    }
}

function download_files_from_rows(string $base, array $rows): void {
    [$selected, $invalid] = download_collect_selected_files($base, $rows);
    if (count($selected) === 0) {
        $preview = implode(', ', array_slice($invalid, 0, 8));
        tres('no valid files selected (base=' . rel_from_root($base) . '), invalid: ' . $preview, 400);
    }
    download_stream_selected_zip($base, $selected);
}
