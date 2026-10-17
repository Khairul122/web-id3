<?php
declare(strict_types=1);

final class View
{
    public static function auth(string $view, array $data = []): void
    {
        if (!in_array($view, ['login', 'password', 'welcome', 'error'], true)) {
            throw new InvalidArgumentException('View tidak tersedia.');
        }
        $heading = 'Selamat datang kembali.';
        $intro = 'Masuk untuk melanjutkan ke ruang kerja SIMINAT.';
        $errors = [];
        $notice = '';
        $username = '';
        $user = null;
        extract($data, EXTR_OVERWRITE);
        $contentView = APP_ROOT . '/views/auth/' . $view . '.php';
        require APP_ROOT . '/views/layouts/auth.php';
    }
}
