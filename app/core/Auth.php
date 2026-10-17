<?php
declare(strict_types=1);

final class Auth
{
    public static function isSignedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::isSignedIn()) {
            return null;
        }
        $now = time();
        if ($now - ($_SESSION['last_activity'] ?? 0) > 1800 || $now - ($_SESSION['auth_started'] ?? 0) > 28800) {
            self::logout();
            return null;
        }
        $user = User::findById((int) $_SESSION['user_id']);
        if (!$user || (int) $user['session_version'] !== ($_SESSION['auth_version'] ?? 0) || !in_array($user['role'], ['admin', 'guru_bk'], true)) {
            self::logout();
            return null;
        }
        $_SESSION['last_activity'] = $now;
        return $user;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION = [
            'user_id' => (int) $user['id'],
            'auth_version' => (int) $user['session_version'],
            'auth_started' => time(),
            'last_activity' => time(),
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }
}
