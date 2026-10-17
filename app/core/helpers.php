<?php
declare(strict_types=1);

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $base = preg_replace('#/admin(?:/.*)?$#', '', $base);
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
        'grid' => '<rect x="4" y="4" width="10" height="10" rx="1"/><rect x="18" y="4" width="10" height="10" rx="1"/><rect x="4" y="18" width="10" height="10" rx="1"/><rect x="18" y="18" width="10" height="10" rx="1"/>',
        'users' => '<circle cx="12" cy="10" r="5"/><path d="M3 28c0-5 4-8 9-8s9 3 9 8m2-22a5 5 0 0 1 0 9m3 5c2 1 3 4 3 8"/>',
        'school' => '<path d="m2 12 14-8 14 8-14 8-14-8Zm5 3v8c5 4 13 4 18 0v-8"/>',
        'list' => '<path d="M11 8h17M11 16h17M11 24h17M4 8h1m-1 8h1m-1 8h1"/>',
        'sliders' => '<path d="M4 9h14m6 0h4M4 23h4m6 0h14"/><circle cx="21" cy="9" r="3"/><circle cx="11" cy="23" r="3"/>',
        'chart' => '<path d="M4 4v24h24M10 22v-6m6 6V10m6 12v-9"/>',
        'target' => '<circle cx="16" cy="16" r="12"/><circle cx="16" cy="16" r="6"/><circle cx="16" cy="16" r="1"/>',
        'file' => '<path d="M8 3h11l7 7v19H8V3Zm11 0v7h7M12 17h10m-10 5h10"/>',
        'user' => '<circle cx="16" cy="10" r="6"/><path d="M5 29c0-6 5-10 11-10s11 4 11 10"/>',
        'logout' => '<path d="M13 5H5v22h8m7-17 6 6-6 6m6-6H12"/>',
        'plus' => '<path d="M16 5v22M5 16h22"/>',
        'edit' => '<path d="m20 6 6 6L12 26H6v-6L20 6Z"/>',
        'trash' => '<path d="M5 8h22M12 8V4h8v4m-11 0 1 20h12l1-20"/>',
        'upload' => '<path d="M16 21V5m-7 7 7-7 7 7M5 21v6h22v-6"/>',
        'download' => '<path d="M16 5v16m-7-7 7 7 7-7M5 21v6h22v-6"/>',
        'printer' => '<path d="M9 12V4h14v8M9 23H4V12h24v11h-5M9 18h14v10H9V18Z"/>',
        'search' => '<circle cx="14" cy="14" r="9"/><path d="m21 21 7 7"/>',
        'check' => '<path d="m6 16 7 7 13-14"/>',
        'alert' => '<path d="M16 3 2 28h28L16 3Zm0 9v8m0 4v1"/>',
        'question' => '<circle cx="16" cy="16" r="13"/><path d="M12 12a4 4 0 1 1 6 3c-2 1-2 2-2 4m0 4v1"/>',
        'play' => '<path d="M9 5v22l18-11L9 5Z"/>',
        'star' => '<path d="m16 3 4 9 9 1-7 6 2 9-8-5-8 5 2-9-7-6 9-1 4-9Z"/>',
    ];
    return '<svg class="icon ' . e($class) . '" viewBox="-2 -2 36 38" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . ($paths[$name] ?? '') . '</svg>';
}

function flash(string $type, string $title, string $message = ''): void
{
    $_SESSION['_flash'] = ['type' => $type, 'title' => $title, 'message' => $message];
}

function take_flash(): ?array
{
    $flash = $_SESSION['_flash'] ?? null;
    unset($_SESSION['_flash']);
    return $flash;
}

function query(string $key, string $default = ''): string
{
    $value = $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function query_int(string $key): int
{
    $value = query($key);
    return ctype_digit($value) ? (int) $value : 0;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function pct(float|string|null $ratio, int $decimals = 1): string
{
    return $ratio === null ? '-' : number_format((float) $ratio * 100, $decimals, ',', '.') . '%';
}

function num(float|string|null $value, int $decimals = 2): string
{
    return $value === null || $value === '' ? '-' : number_format((float) $value, $decimals, ',', '.');
}

function wib(?string $utc): string
{
    if (!$utc) {
        return '-';
    }
    $date = new DateTimeImmutable($utc, new DateTimeZone('UTC'));
    return $date->setTimezone(new DateTimeZone('Asia/Jakarta'))->format('d/m/Y H:i') . ' WIB';
}

function badge(?string $value): string
{
    if ($value === null || $value === '') {
        return '<span class="badge badge--none">Tanpa label</span>';
    }
    $class = strtolower(str_replace(' ', '-', $value));
    return '<span class="badge badge--' . e($class) . '">' . e($value) . '</span>';
}

function json_out(mixed $value): string
{
    return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}

function admin_url(string $path = ''): string
{
    return url('admin/' . ltrim($path, '/'));
}

function back_to(string $path): never
{
    redirect('admin/' . ltrim($path, '/'));
}
