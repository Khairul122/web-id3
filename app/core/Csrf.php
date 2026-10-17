<?php
declare(strict_types=1);

final class Csrf
{
    public static function field(): string
    {
        $_SESSION['_csrf'] ??= bin2hex(random_bytes(32));
        return '<input type="hidden" name="_csrf" value="' . e($_SESSION['_csrf']) . '">';
    }

    public static function verify(): bool
    {
        $token = input('_csrf');
        return isset($_SESSION['_csrf']) && $token !== '' && hash_equals($_SESSION['_csrf'], $token);
    }
}
