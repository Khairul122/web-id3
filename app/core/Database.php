<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $connection = null;

    public static function pdo(): PDO
    {
        if (self::$connection === null) {
            $local = APP_ROOT . '/config/config.php';
            $config = require is_file($local) ? $local : APP_ROOT . '/config/config.example.php';
            self::$connection = new PDO(
                'mysql:host=' . $config['db_host'] . ';port=' . $config['db_port'] . ';dbname=' . $config['db_name'] . ';charset=utf8mb4',
                $config['db_user'],
                $config['db_password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
            );
            self::$connection->exec("SET time_zone = '+00:00'");
        }
        return self::$connection;
    }
}
