<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__) . '/app/core/bootstrap.php';
$action = $argv[1] ?? '';
$username = $argv[2] ?? '';
if (!preg_match('/^auth_test_[a-f0-9]{12}$/D', $username)) {
    throw new InvalidArgumentException('Nama fixture tidak valid.');
}
if ($action === 'create') {
    $role = getenv('SIMINAT_TEST_ROLE') ?: 'admin';
    if (!in_array($role, ['admin', 'guru_bk', 'siswa'], true)) {
        throw new InvalidArgumentException('Peran fixture tidak valid.');
    }
    $statement = Database::pdo()->prepare('INSERT INTO users (nama, username, password_hash, role, kode_siswa, sekolah_id, must_change_password) VALUES (?, ?, ?, ?, ?, ?, 1)');
    $kode = getenv('SIMINAT_TEST_KODE') ?: null;
    $studentSchool = $role === 'siswa' && $kode ? Database::run('SELECT sekolah_id FROM siswa WHERE kode_siswa = ?', [$kode])->fetchColumn() : false;
    $statement->execute(['Fixture Pengujian Autentikasi', $username, password_hash(getenv('SIMINAT_TEST_PASSWORD'), PASSWORD_BCRYPT, ['cost' => 12]), $role, $kode, $role === 'guru_bk' ? (int) (getenv('SIMINAT_TEST_SEKOLAH_ID') ?: 1) : ($studentSchool === false ? null : (int) $studentSchool)]);
} elseif ($action === 'delete') {
    $statement = Database::pdo()->prepare('DELETE FROM users WHERE username = ? AND nama = ?');
    $statement->execute([$username, 'Fixture Pengujian Autentikasi']);
    if (getenv('SIMINAT_TEST_RATE_LIMIT_IP') === '127.0.0.2') {
        LoginAttempt::clear('127.0.0.2');
    }
} else {
    throw new InvalidArgumentException('Aksi fixture tidak valid.');
}
