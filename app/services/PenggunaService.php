<?php
declare(strict_types=1);

/** Validasi akun pengguna dan aturan kata sandi. */
final class PenggunaService
{
    public const ROLES = ['admin' => 'Administrator', 'guru_bk' => 'Guru BK', 'siswa' => 'Siswa'];

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
            'kode_siswa' => trim(input('kode_siswa')),
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
        if ($form['role'] === 'siswa') {
            if ($form['kode_siswa'] === '' || !Siswa::findByKode($form['kode_siswa'])) {
                $errors['kode_siswa'] = 'Pilih kode siswa yang ada di data siswa.';
            } elseif (User::kodeSiswaTaken($form['kode_siswa'], (int) ($existing['id'] ?? 0))) {
                $errors['kode_siswa'] = 'Kode siswa sudah dipakai akun lain.';
            } elseif ($existing && $existing['kode_siswa'] !== $form['kode_siswa'] && Prediksi::hasCreatedBy((int) $existing['id'])) {
                $errors['kode_siswa'] = 'Akun dengan riwayat prediksi tidak dapat ditautkan ke siswa lain.';
            }
        } else {
            $form['kode_siswa'] = null;
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
            User::update((int) $existing['id'], $form['nama'], $form['username'], $form['role'], $form['password'], $form['kode_siswa']);
            return (int) $existing['id'];
        }
        return User::create($form['nama'], $form['username'], $form['role'], $form['password'], $form['kode_siswa']);
    }

    public static function createStudentAccounts(?int $schoolId, string $kelas): array
    {
        return Database::transaction(static function () use ($schoolId, $kelas): array {
            $cards = [];
            foreach (User::withoutSiswaAccount($schoolId, $kelas) as $student) {
                $username = strtolower($student['kode_siswa']);
                $password = bin2hex(random_bytes(6));
                User::create($student['nama'], $username, 'siswa', $password, $student['kode_siswa']);
                $cards[] = ['nama' => $student['nama'], 'kelas' => $student['kelas'], 'username' => $username, 'password' => $password];
            }
            return $cards;
        });
    }
}
