<?php
declare(strict_types=1);

final class User
{
    public static function findByUsername(string $username): ?array
    {
        $statement = Database::pdo()->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $statement->execute([$username]);
        return $statement->fetch() ?: null;
    }

    public static function findById(int $id): ?array
    {
        $statement = Database::pdo()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public static function recordLogin(int $id): void
    {
        $statement = Database::pdo()->prepare('UPDATE users SET last_login = UTC_TIMESTAMP() WHERE id = ?');
        $statement->execute([$id]);
    }

    public static function changePassword(int $id, string $password): void
    {
        $statement = Database::pdo()->prepare('UPDATE users SET password_hash = ?, must_change_password = 0, session_version = session_version + 1 WHERE id = ?');
        $statement->execute([password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }
}
