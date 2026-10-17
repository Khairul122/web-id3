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

    public static function run(string $sql, array $params = []): PDOStatement
    {
        $statement = self::pdo()->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    /** Jalankan $work dalam transaksi; rollback bila gagal. */
    public static function transaction(callable $work): mixed
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();
        try {
            $result = $work($pdo);
            $pdo->commit();
            return $result;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }
}
