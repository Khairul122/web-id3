<?php
declare(strict_types=1);

final class LoginAttempt
{
    public static function reserve(string $ip): int
    {
        $pdo = Database::pdo();
        $key = hash('sha256', $ip);
        $now = time();
        $pdo->beginTransaction();
        try {
            $statement = $pdo->prepare('INSERT IGNORE INTO login_attempts (ip_hash, attempts, window_start) VALUES (?, 0, ?)');
            $statement->execute([$key, $now]);
            $statement = $pdo->prepare('SELECT attempts, window_start FROM login_attempts WHERE ip_hash = ? FOR UPDATE');
            $statement->execute([$key]);
            $row = $statement->fetch();
            $elapsed = $now - (int) $row['window_start'];
            if ($elapsed < 900 && (int) $row['attempts'] >= 5) {
                $pdo->commit();
                return 900 - $elapsed;
            }
            $statement = $pdo->prepare('UPDATE login_attempts SET attempts = ?, window_start = ? WHERE ip_hash = ?');
            $statement->execute([$elapsed >= 900 ? 1 : (int) $row['attempts'] + 1, $elapsed >= 900 ? $now : $row['window_start'], $key]);
            $pdo->commit();
            return 0;
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $exception;
        }
    }

    public static function clear(string $ip): void
    {
        $statement = Database::pdo()->prepare('DELETE FROM login_attempts WHERE ip_hash = ?');
        $statement->execute([hash('sha256', $ip)]);
        $statement = Database::pdo()->prepare('DELETE FROM login_attempts WHERE window_start < ?');
        $statement->execute([time() - 900]);
    }
}
