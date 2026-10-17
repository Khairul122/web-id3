<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__) . '/app/core/bootstrap.php';
$local = APP_ROOT . '/config/config.php';
$config = require is_file($local) ? $local : APP_ROOT . '/config/config.example.php';
if (!preg_match('/^[a-zA-Z0-9_]+$/D', $config['db_name'])) {
    throw new RuntimeException('Nama database tidak valid.');
}
$server = new PDO('mysql:host=' . $config['db_host'] . ';port=' . $config['db_port'] . ';charset=utf8mb4', $config['db_user'], $config['db_password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$server->exec('CREATE DATABASE IF NOT EXISTS `' . $config['db_name'] . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
Database::pdo()->exec(file_get_contents(APP_ROOT . '/database/schema.sql'));
Database::pdo()->exec(file_get_contents(APP_ROOT . '/database/migrations/002_optional_password_change.sql'));
Database::pdo()->exec(file_get_contents(APP_ROOT . '/database/seed.sql'));
Database::pdo()->exec(file_get_contents(APP_ROOT . '/database/seed_master.sql'));
echo "Database siap. Akun dan data yang sudah ada tidak diubah.\n";
