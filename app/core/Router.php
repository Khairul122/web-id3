<?php
declare(strict_types=1);

/** Router panel: /panel/{modul}/{aksi} -> {Modul}Controller::{aksi}(). */
final class Router
{
    private const MODULES = [
        'dashboard' => DashboardController::class,
        'siswa' => SiswaController::class,
        'sekolah' => SekolahController::class,
        'atribut' => AtributController::class,
        'diskretisasi' => DiskretisasiController::class,
        'training' => TrainingController::class,
        'pengujian' => PengujianController::class,
        'prediksi' => PrediksiController::class,
        'laporan' => LaporanController::class,
        'pengguna' => PenggunaController::class,
        'akun' => AkunController::class,
    ];

    public static function dispatch(string $route): void
    {
        $parts = array_values(array_filter(explode('/', trim($route, '/'))));
        $module = $parts[0] ?? 'dashboard';
        $action = $parts[1] ?? 'index';
        $class = self::MODULES[$module] ?? null;
        $method = lcfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $action))));
        if (!$class || count($parts) > 2 || !preg_match('/^[a-z][a-z-]*$/D', $action) || !self::isAction($class, $method)) {
            Auth::requireUser();
            http_response_code(404);
            flash('error', 'Halaman tidak ditemukan', 'Alamat yang dibuka tidak tersedia di panel.');
            redirect('panel/dashboard/');
        }
        (new $class())->$method();
    }

    /** Hanya method publik yang dideklarasikan di controller itu sendiri yang boleh dipanggil lewat URL. */
    private static function isAction(string $class, string $method): bool
    {
        if (!method_exists($class, $method)) {
            return false;
        }
        $reflection = new ReflectionMethod($class, $method);
        return $reflection->isPublic() && !$reflection->isStatic() && $reflection->getDeclaringClass()->getName() === $class && $method !== '__construct';
    }
}
