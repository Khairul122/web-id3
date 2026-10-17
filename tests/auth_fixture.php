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
    $statement = Database::pdo()->prepare('INSERT INTO users (nama, username, password_hash, role, must_change_password) VALUES (?, ?, ?, ?, 1)');
    $statement->execute(['Fixture Pengujian Autentikasi', $username, password_hash(getenv('SIMINAT_TEST_PASSWORD'), PASSWORD_BCRYPT, ['cost' => 12]), 'admin']);
} elseif ($action === 'delete') {
    $statement = Database::pdo()->prepare('DELETE FROM users WHERE username = ? AND nama = ?');
    $statement->execute([$username, 'Fixture Pengujian Autentikasi']);
    LoginAttempt::clear('127.0.0.2');
} else {
    throw new InvalidArgumentException('Aksi fixture tidak valid.');
}
