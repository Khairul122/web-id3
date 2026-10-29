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
        if (!$user || (int) $user['session_version'] !== ($_SESSION['auth_version'] ?? 0) || !isset(PenggunaService::ROLES[$user['role']]) || ($user['role'] === 'guru_bk' && !$user['sekolah_id'])) {
            self::logout();
            return null;
        }
        $_SESSION['last_activity'] = $now;
        return $user;
    }

    public static function requireUser(): array
    {
        $wasSignedIn = self::isSignedIn();
        $user = self::user();
        if (!$user) {
            if ($wasSignedIn) {
                flash('error', 'Sesi berakhir', 'Sesi Anda telah berakhir. Silakan masuk kembali.');
            }
            redirect('login.php');
        }
        return $user;
    }

    public static function requireAdmin(array $user): void
    {
        if ($user['role'] !== 'admin') {
            flash('error', 'Akses ditolak', 'Halaman tersebut hanya dapat dibuka oleh administrator.');
            redirect('panel/dashboard/');
        }
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
