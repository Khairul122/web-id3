<?php
declare(strict_types=1);

function e(string|int $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    if (str_ends_with($base, '/admin')) {
        $base = substr($base, 0, -6);
    }
    return rtrim($base, '/.') . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . url($path), true, 303);
    exit;
}

function input(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? $value : '';
}

function brand(): string
{
    return '<a class="brand" href="' . e(url()) . '" aria-label="SIMINAT, beranda">'
        . '<svg class="brand__mark" viewBox="0 0 64 72" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M11 30V19L32 6l21 13v11M6 34c10-2 18 1 26 7 8-6 16-9 26-7v29c-10-2-18 1-26 7-8-6-16-9-26-7V34Z"/><path d="M32 41v29M10 57c8-1 15 2 22 7 7-5 14-8 22-7"/><path d="m32 20 4 6-4 6-4-6 4-6Z" fill="currentColor" stroke="none"/></svg>'
        . '<span><span class="brand__name">SIMINAT</span><span class="brand__tagline">Kenali minat. Rencanakan masa depan.</span></span></a>';
}

function icon(string $name, string $class = ''): string
{
    $paths = [
        'book' => '<path d="M3 5c5-2 9-1 13 2v22c-4-3-8-4-13-2V5Zm26 0c-5-2-9-1-13 2v22c4-3 8-4 13-2V5Z"/><path d="M3 8H0v23c6-2 11-1 16 2 5-3 10-4 16-2V8h-3"/>',
        'branch' => '<circle cx="16" cy="5" r="4"/><circle cx="5" cy="27" r="4"/><circle cx="27" cy="27" r="4"/><path d="M16 9v7L5 22v1m11-7 11 6v1"/>',
        'cap' => '<path d="m1 11 15-8 15 8-15 8-15-8Zm6 4v10c6 5 12 5 18 0V15m6-4v15m0 0-2 5h4l-2-5Z"/>',
        'arrow' => '<path d="M5 16h22m-8-8 8 8-8 8"/>',
        'back' => '<path d="M27 16H5m8-8-8 8 8 8"/>',
        'menu' => '<path d="M5 9h22M5 16h22M5 23h22"/>',
        'close' => '<path d="m8 8 16 16M24 8 8 24"/>',
        'chevron' => '<path d="m11 6 10 10-10 10"/>',
        'eye' => '<path d="M2 16s5-8 14-8 14 8 14 8-5 8-14 8S2 16 2 16Z"/><circle cx="16" cy="16" r="4"/>',
    ];
    return '<svg class="icon ' . e($class) . '" viewBox="-2 -2 36 38" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}
