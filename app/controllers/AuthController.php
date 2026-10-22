<?php
declare(strict_types=1);

final class AuthController
{
    public function login(): void
    {
        if (Auth::user()) {
            redirect('admin/dashboard/');
        }
        $errors = [];
        $username = '';
        $notice = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->verifyPost()) {
                return;
            }
            $username = trim(input('username'));
            $password = input('password');
            if ($username === '') {
                $errors['username'] = 'Username wajib diisi.';
            } elseif (!preg_match('/^[a-zA-Z0-9_.-]{1,100}$/D', $username)) {
                $errors['username'] = 'Gunakan huruf, angka, titik, tanda hubung, atau garis bawah (maksimal 100 karakter).';
            }
            if ($password === '') {
                $errors['password'] = 'Kata sandi wajib diisi.';
            } elseif (strlen($password) > 72 || str_contains($password, "\0")) {
                $errors['password'] = 'Kata sandi tidak dapat digunakan. Maksimal 72 byte.';
            }
            if (!$errors) {
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                $retry = LoginAttempt::reserve($ip);
                if ($retry > 0) {
                    http_response_code(429);
                    header('Retry-After: ' . $retry);
                    $notice = 'Terlalu banyak percobaan masuk. Coba lagi dalam ' . (int) ceil($retry / 60) . ' menit.';
                } else {
                    $user = User::findByUsername($username);
                    $dummy = '$2y$12$jp51.VxPCE78fy1uK9CU7OKKTywqx0uGstjSyR.qPWOPg.7ms9JxW';
                    $valid = password_verify($password, $user['password_hash'] ?? $dummy);
                    if ($valid && $user && isset(PenggunaService::ROLES[$user['role']])) {
                        User::recordLogin((int) $user['id']);
                        LoginAttempt::clear($ip);
                        Auth::login($user);
                        flash('success', 'Berhasil masuk', 'Selamat datang, ' . $user['nama'] . '.');
                        redirect('admin/dashboard/');
                    }
                    http_response_code(422);
                    $notice = 'Username atau kata sandi tidak sesuai.';
                }
            } else {
                http_response_code(422);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->methodNotAllowed();
            return;
        }
        $flash = $notice !== '' || $errors
            ? ['type' => 'error', 'title' => 'Gagal masuk', 'message' => $notice !== '' ? $notice : 'Periksa kembali username dan kata sandi Anda.']
            : null;
        View::auth('login', compact('errors', 'username', 'notice', 'flash'));
    }

    public function logout(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->methodNotAllowed();
            return;
        }
        if (!$this->verifyPost()) {
            return;
        }
        Auth::logout();
        flash('success', 'Berhasil keluar', 'Anda telah keluar dari SIMINAT. Sampai jumpa kembali.');
        redirect('login.php');
    }

    private function verifyPost(): bool
    {
        if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 8192) {
            http_response_code(413);
            View::auth('error', ['heading' => 'Form terlalu besar.', 'intro' => 'Kembali ke halaman sebelumnya dan periksa isian Anda.']);
            return false;
        }
        if (!Csrf::verify()) {
            http_response_code(403);
            View::auth('error', ['heading' => 'Sesi form telah berakhir.', 'intro' => 'Buka kembali halaman masuk untuk melanjutkan.']);
            return false;
        }
        return true;
    }

    private function methodNotAllowed(): void
    {
        http_response_code(405);
        header('Allow: ' . (str_ends_with($_SERVER['SCRIPT_NAME'], 'logout.php') ? 'POST' : 'GET, POST'));
        View::auth('error', ['heading' => 'Permintaan belum sesuai.', 'intro' => 'Gunakan tombol pada halaman untuk melanjutkan.']);
    }
}
