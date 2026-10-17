<?php
declare(strict_types=1);

// Fixture tes panel admin. Pakai: php tests/admin_fixture.php state | cleanup <id model aktif awal atau 0>
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__) . '/app/core/bootstrap.php';

$action = $argv[1] ?? '';
if ($action === 'state') {
    echo (int) Database::run('SELECT id FROM model_id3 WHERE aktif = 1 LIMIT 1')->fetchColumn();
} elseif ($action === 'cleanup') {
    Database::run("DELETE FROM model_id3 WHERE nama LIKE 'UJI-OTOMATIS%'");
    Database::run("DELETE FROM siswa WHERE kode_siswa LIKE 'UJI-%'");
    Database::run("DELETE FROM users WHERE username LIKE 'uji\\_otomatis\\_%'");
    $previous = (int) ($argv[2] ?? 0);
    if ($previous > 0) {
        ModelId3::activate($previous);
    }
    echo "bersih\n";
} else {
    throw new InvalidArgumentException('Aksi fixture tidak valid.');
}
