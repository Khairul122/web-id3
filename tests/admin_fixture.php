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
    Database::run("DELETE FROM users WHERE kode_siswa LIKE 'UJI-%'");
    Database::run("DELETE FROM siswa WHERE kode_siswa LIKE 'UJI-%'");
    Database::run("DELETE FROM users WHERE username LIKE 'uji\\_otomatis\\_%'");
    $previous = (int) ($argv[2] ?? 0);
    if ($previous > 0) {
        ModelId3::activate($previous);
    }
    echo "bersih\n";
} elseif ($action === 'user-hash' && preg_match('/^UJI-[A-Z0-9-]{3,16}$/D', $argv[2] ?? '')) {
    echo User::findByKodeSiswa($argv[2])['password_hash'] ?? '';
} elseif ($action === 'prediction-student' && ctype_digit($argv[2] ?? '')) {
    echo Database::run('SELECT siswa_id FROM prediksi WHERE id = ?', [(int) $argv[2]])->fetchColumn();
} elseif ($action === 'user-id' && preg_match('/^auth_test_[a-f0-9]{12}$/D', $argv[2] ?? '')) {
    echo User::findByUsername($argv[2])['id'] ?? '';
} else {
    throw new InvalidArgumentException('Aksi fixture tidak valid.');
}
