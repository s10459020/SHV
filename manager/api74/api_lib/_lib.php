<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');

function root_dir(): string {
    static $root = null;
    if ($root !== null) return $root;
    // Workspace root: project root (SHV)
    $path = realpath(__DIR__ . '/..' . '/..' . '/..');
    if ($path === false) {
        $path = __DIR__ . '/..' . '/..' . '/..';
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

function normalize_physical_path(string $input): string {
    $raw = str_replace('\\', '/', trim($input));
    if ($raw === '') tres('path must be physical absolute path', 400);
    if (preg_match('/^[A-Za-z]:\//', $raw) === 1) {
        $raw = '/' . $raw;
    }
    if (!str_starts_with($raw, '/')) {
        tres('path must be physical absolute path: ' . $input, 400);
    }

    $kind = 'unix';
    $prefix = '/';
    $rest = substr($raw, 1);
    if (preg_match('/^([A-Za-z]:)(?:\/(.*)|$)/', $rest, $m) === 1) {
        $kind = 'drive';
        $prefix = strtoupper($m[1]) . '/';
        $rest = isset($m[2]) ? (string)$m[2] : '';
    }

    $parts = [];
    foreach (explode('/', $rest) as $part) {
        if ($part === '' || $part === '.') continue;
        if ($part === '..') {
            if (count($parts) > 0) array_pop($parts);
            continue;
        }
        $parts[] = $part;
    }

    if ($kind === 'drive') {
        return count($parts) > 0 ? $prefix . implode('/', $parts) : $prefix;
    }
    return count($parts) > 0 ? '/' . implode('/', $parts) : '/';
}

function resolve_path(string $input): string {
    return normalize_physical_path($input);
}

function to_api_path(string $physical): string {
    $raw = str_replace('\\', '/', trim($physical));
    if ($raw === '') return '/';
    if (preg_match('/^[A-Za-z]:\//', $raw) === 1) return '/' . $raw;
    return str_starts_with($raw, '/') ? $raw : '/' . $raw;
}

function rel_from_root(string $abs): string {
    return to_api_path($abs);
}

function path_join(string $base, string $name): string {
    $base = str_replace('\\', '/', $base);
    if (preg_match('/^[A-Za-z]:\/$/', $base) || $base === '/') {
        return $base . ltrim($name, '/');
    }
    return rtrim($base, '/') . '/' . ltrim($name, '/');
}

