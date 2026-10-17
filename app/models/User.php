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

    public static function all(): array
    {
        return Database::run('SELECT id, nama, username, role, last_login, created_at FROM users ORDER BY role, nama')->fetchAll();
    }

    public static function create(string $nama, string $username, string $role, string $password): int
    {
        Database::run(
            'INSERT INTO users (nama, username, password_hash, role) VALUES (?, ?, ?, ?)',
            [$nama, $username, password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), $role]
        );
        return (int) Database::pdo()->lastInsertId();
    }

    /** Ubah profil. Password baru (opsional) juga mengakhiri sesi akun tersebut di perangkat lain. */
    public static function update(int $id, string $nama, string $username, string $role, string $password = ''): void
    {
        Database::run('UPDATE users SET nama = ?, username = ?, role = ? WHERE id = ?', [$nama, $username, $role, $id]);
        if ($password !== '') {
            self::changePassword($id, $password);
        }
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM users WHERE id = ?', [$id]);
    }

    public static function usernameTaken(string $username, int $exceptId = 0): bool
    {
        return (bool) Database::run('SELECT 1 FROM users WHERE username = ? AND id <> ?', [$username, $exceptId])->fetchColumn();
    }

    public static function countAdmins(): int
    {
        return (int) Database::run("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    }

    public static function changePassword(int $id, string $password): void
    {
        $statement = Database::pdo()->prepare('UPDATE users SET password_hash = ?, must_change_password = 0, session_version = session_version + 1 WHERE id = ?');
        $statement->execute([password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }
}
