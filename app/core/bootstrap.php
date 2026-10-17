<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__, 2));
date_default_timezone_set('UTC');
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', APP_ROOT . '/storage/logs/app.log');

spl_autoload_register(static function (string $class): void {
    if (!preg_match('/^[A-Za-z][A-Za-z0-9]*$/D', $class)) {
        return;
    }
    foreach (['core', 'models', 'controllers'] as $directory) {
        $file = APP_ROOT . '/app/' . $directory . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

require_once __DIR__ . '/helpers.php';

set_exception_handler(static function (Throwable $exception): void {
    error_log(get_class($exception) . ': ' . $exception->getMessage());
    if (PHP_SAPI === 'cli') {
        fwrite(STDERR, "Proses gagal. Periksa storage/logs/app.log.\n");
        exit(1);
    }
    http_response_code(503);
    View::auth('error', [
        'heading' => 'Layanan belum dapat diakses.',
        'intro' => 'Silakan coba kembali beberapa saat lagi. Jika kendala berlanjut, hubungi administrator sekolah.',
    ]);
});

if (PHP_SAPI !== 'cli') {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; font-src 'self'; connect-src 'self'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'; object-src 'none'");
    header('Cache-Control: no-store');
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    if ($secure) {
        header('Strict-Transport-Security: max-age=31536000');
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.gc_maxlifetime', '28800');
    session_name('siminat_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => url(''),
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
