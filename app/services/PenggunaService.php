<?php
declare(strict_types=1);

/** Validasi akun pengguna dan aturan kata sandi. */
final class PenggunaService
{
    public const ROLES = ['admin' => 'Administrator', 'guru_bk' => 'Guru BK'];

    /** Aturan kata sandi baru: 12 sampai 72 byte (batas bcrypt). Mengembalikan pesan error atau null. */
    public static function passwordError(string $password, string $confirmation): ?string
    {
        if (strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0")) {
            return 'Gunakan kata sandi sepanjang 12 hingga 72 byte.';
        }
        return $password === $confirmation ? null : 'Konfirmasi kata sandi belum cocok.';
    }

    /** @return array{0: array, 1: array<string, string>} */
    public static function fromPost(?array $existing): array
    {
        $form = [
            'nama' => trim(input('nama')),
            'username' => trim(input('username')),
            'role' => input('role'),
            'password' => input('password'),
        ];
        $errors = [];
        if ($form['nama'] === '' || mb_strlen($form['nama']) > 100) {
            $errors['nama'] = 'Nama wajib diisi, maksimal 100 karakter.';
        }
        if (!preg_match('/^[a-zA-Z0-9_.-]{3,100}$/D', $form['username'])) {
            $errors['username'] = 'Gunakan 3 sampai 100 huruf, angka, titik, tanda hubung, atau garis bawah.';
        } elseif (User::usernameTaken($form['username'], (int) ($existing['id'] ?? 0))) {
            $errors['username'] = 'Username sudah dipakai.';
        }
        if (!isset(self::ROLES[$form['role']])) {
            $errors['role'] = 'Pilih peran.';
        } elseif ($existing && $existing['role'] === 'admin' && $form['role'] !== 'admin' && User::countAdmins() <= 1) {
            $errors['role'] = 'Harus ada minimal satu administrator.';
        }
        if (!$existing || $form['password'] !== '') {
            $message = self::passwordError($form['password'], input('password_confirmation'));
            if ($message) {
                $errors['password'] = $message;
            }
        }
        return [$form, $errors];
    }

    public static function save(array $form, ?array $existing): int
    {
        if ($existing) {
            User::update((int) $existing['id'], $form['nama'], $form['username'], $form['role'], $form['password']);
            return (int) $existing['id'];
        }
        return User::create($form['nama'], $form['username'], $form['role'], $form['password']);
    }
}
