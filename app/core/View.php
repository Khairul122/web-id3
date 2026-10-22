<?php
declare(strict_types=1);

final class View
{
    public static function auth(string $view, array $data = []): void
    {
        if (!in_array($view, ['login', 'error'], true)) {
            throw new InvalidArgumentException('View tidak tersedia.');
        }
        $heading = 'Selamat datang kembali.';
        $intro = 'Masuk untuk melihat rekomendasi dan melanjutkan kegiatan sesuai peran Anda.';
        $errors = [];
        $notice = '';
        $username = '';
        $user = null;
        $flash = null;
        extract($data, EXTR_OVERWRITE);
        $contentView = APP_ROOT . '/views/auth/' . $view . '.php';
        require APP_ROOT . '/views/layouts/auth.php';
    }

    /** Render view panel admin di dalam layout. $view berbentuk "modul/halaman". */
    public static function admin(string $view, array $data = []): void
    {
        if (!preg_match('#^[a-z]+/[a-z-]+$#D', $view) || !is_file(APP_ROOT . '/views/admin/' . $view . '.php')) {
            throw new InvalidArgumentException('View tidak tersedia: ' . $view);
        }
        $flash = null;
        $crumbs = [];
        $errors = [];
        extract($data, EXTR_OVERWRITE);
        require APP_ROOT . '/views/layouts/admin_top.php';
        require APP_ROOT . '/views/admin/' . $view . '.php';
        require APP_ROOT . '/views/layouts/admin_bottom.php';
    }
}
