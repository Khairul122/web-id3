<?php
declare(strict_types=1);

final class AuthController
{
    public function login(): void
    {
        if (Auth::user()) {
            redirect('admin/');
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
                    if ($valid && $user && in_array($user['role'], ['admin', 'guru_bk'], true)) {
                        User::recordLogin((int) $user['id']);
                        LoginAttempt::clear($ip);
                        Auth::login($user);
                        redirect('admin/');
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
        View::auth('login', compact('errors', 'username', 'notice'));
    }

    public function workspace(): void
    {
        $user = Auth::user();
        if (!$user) {
            redirect('login.php');
        }
        $change = (bool) $user['must_change_password'] || ($_GET['page'] ?? '') === 'password';
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->verifyPost()) {
                return;
            }
            if (!$change) {
                $this->methodNotAllowed();
                return;
            }
            $password = input('password');
            if (!password_verify(input('current_password'), $user['password_hash'])) {
                $errors['current_password'] = 'Kata sandi saat ini belum sesuai.';
            }
            if (strlen($password) < 12 || strlen($password) > 72 || str_contains($password, "\0")) {
                $errors['password'] = 'Gunakan kata sandi sepanjang 12 hingga 72 byte.';
            } elseif (password_verify($password, $user['password_hash'])) {
                $errors['password'] = 'Gunakan kata sandi yang berbeda dari kata sandi saat ini.';
            }
            if ($password !== input('password_confirmation')) {
                $errors['password_confirmation'] = 'Konfirmasi kata sandi belum cocok.';
            }
            if (!$errors) {
                User::changePassword((int) $user['id'], $password);
                Auth::login(User::findById((int) $user['id']));
                redirect('admin/');
            }
            http_response_code(422);
        } elseif ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->methodNotAllowed();
            return;
        }
        View::auth($change ? 'password' : 'welcome', [
            'user' => $user,
            'errors' => $errors,
            'heading' => $change ? 'Perbarui kata sandi.' : 'Ruang kerja Anda.',
            'intro' => $change ? 'Buat kata sandi pribadi sebelum melanjutkan ke ruang kerja.' : 'Selamat datang, ' . $user['nama'] . '.',
        ]);
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
            http_response_code(419);
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
