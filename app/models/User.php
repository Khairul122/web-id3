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
        return Database::run('SELECT id, nama, username, role, sekolah_id, kode_siswa, last_login, created_at FROM users ORDER BY role, nama')->fetchAll();
    }

    public static function create(string $nama, string $username, string $role, string $password, ?string $kodeSiswa = null, ?int $sekolahId = null): int
    {
        if ($role === 'siswa') {
            $sekolahId = self::studentSchool($kodeSiswa);
        }
        Database::run(
            'INSERT INTO users (nama, username, password_hash, role, kode_siswa, sekolah_id) VALUES (?, ?, ?, ?, ?, ?)',
            [$nama, $username, password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]), $role, $kodeSiswa, $sekolahId]
        );
        return (int) Database::pdo()->lastInsertId();
    }

    /** Ubah profil. Password baru (opsional) juga mengakhiri sesi akun tersebut di perangkat lain. */
    public static function update(int $id, string $nama, string $username, string $role, string $password = '', ?string $kodeSiswa = null, ?int $sekolahId = null): void
    {
        if ($role === 'siswa') {
            $sekolahId = self::studentSchool($kodeSiswa);
        }
        Database::run('UPDATE users SET session_version = session_version + IF(role <> ? OR NOT (kode_siswa <=> ?) OR NOT (sekolah_id <=> ?), 1, 0), nama = ?, username = ?, role = ?, kode_siswa = ?, sekolah_id = ? WHERE id = ?', [$role, $kodeSiswa, $sekolahId, $nama, $username, $role, $kodeSiswa, $sekolahId, $id]);
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

    public static function findByKodeSiswa(string $kode): ?array
    {
        return Database::run("SELECT * FROM users WHERE kode_siswa = ? AND role = 'siswa'", [$kode])->fetch() ?: null;
    }

    public static function kodeSiswaTaken(string $kode, int $exceptId = 0): bool
    {
        return (bool) Database::run('SELECT 1 FROM users WHERE kode_siswa = ? AND id <> ?', [$kode, $exceptId])->fetchColumn();
    }

    private static function studentSchool(?string $kode): ?int
    {
        $id = $kode === null ? false : Database::run('SELECT sekolah_id FROM siswa WHERE kode_siswa = ?', [$kode])->fetchColumn();
        return $id === false ? null : (int) $id;
    }

    public static function syncStudentSchools(?string $kode = null): void
    {
        Database::run("UPDATE users u LEFT JOIN siswa s ON s.kode_siswa = u.kode_siswa
            SET u.session_version = u.session_version + 1, u.sekolah_id = s.sekolah_id
            WHERE u.role = 'siswa' AND NOT (u.sekolah_id <=> s.sekolah_id)" . ($kode === null ? '' : ' AND u.kode_siswa = ?'), $kode === null ? [] : [$kode]);
    }

    public static function withoutSiswaAccount(?int $schoolId, string $kelas): array
    {
        $where = ['u.id IS NULL'];
        $params = [];
        if ($schoolId) {
            $where[] = 's.sekolah_id = ?';
            $params[] = $schoolId;
        }
        if ($kelas !== '') {
            $where[] = 's.kelas = ?';
            $params[] = $kelas;
        }
        return Database::run('SELECT s.kode_siswa, s.nama, s.kelas, k.nama AS sekolah_nama FROM siswa s JOIN sekolah k ON k.id = s.sekolah_id LEFT JOIN users u ON u.kode_siswa = s.kode_siswa WHERE ' . implode(' AND ', $where) . ' ORDER BY s.kode_siswa', $params)->fetchAll();
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
