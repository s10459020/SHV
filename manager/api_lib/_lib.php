<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');

function root_dir(): string {
    static $root = null;
    if ($root !== null) return $root;
    // Workspace root: project root (SHV)
    $path = realpath(__DIR__ . '/..' . '/..');
    if ($path === false) {
        $path = __DIR__ . '/..' . '/..';
    }
    $root = rtrim(str_replace('\\', '/', (string)$path), '/');
    return $root;
}

function req(string $key, ?string $default = null): ?string {
    if (isset($_POST[$key])) return (string)$_POST[$key];
    if (isset($_GET[$key])) return (string)$_GET[$key];
    return $default;
}

function jres($data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function tres(string $text, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: text/plain; charset=utf-8');
    echo $text;
    exit;
}

function normalize_joined(string $input): string {
    $input = str_replace('\\', '/', trim($input));
    if ($input === '') return root_dir();
    if (preg_match('/^[A-Za-z]:\//', $input)) {
        $candidate = $input;
    } else {
        // Treat slash-leading paths as workspace-relative (not filesystem root).
        $candidate = root_dir() . '/' . ltrim($input, '/');
    }
    $parts = [];
    foreach (explode('/', $candidate) as $part) {
        if ($part === '' || $part === '.') continue;
        if ($part === '..') {
            if (count($parts) > 0) array_pop($parts);
            continue;
        }
        $parts[] = $part;
    }
    if (preg_match('/^[A-Za-z]:$/', $parts[0] ?? '')) {
        $drive = array_shift($parts);
        return $drive . '/' . implode('/', $parts);
    }
    return '/' . implode('/', $parts);
}

function ensure_in_root(string $path): string {
    $n = rtrim(str_replace('\\', '/', $path), '/');
    if (preg_match('/^[A-Za-z]:$/', $n)) $n .= '/';
    if ($n === '') $n = '/';
    return $n;
}

function resolve_path(string $input): string {
    return ensure_in_root(normalize_joined($input));
}

function rel_from_root(string $abs): string {
    $root = root_dir();
    $abs = str_replace('\\', '/', $abs);
    if (strtolower($abs) === strtolower($root)) return '/';
    if (!str_starts_with(strtolower($abs . '/'), strtolower($root . '/'))) {
        return $abs;
    }
    return '/' . ltrim(substr($abs, strlen($root)), '/');
}

function path_join(string $base, string $name): string {
    $base = str_replace('\\', '/', $base);
    if (preg_match('/^[A-Za-z]:\/$/', $base) || $base === '/') {
        return $base . ltrim($name, '/');
    }
    return rtrim($base, '/') . '/' . ltrim($name, '/');
}
