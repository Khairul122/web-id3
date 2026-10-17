<?php
declare(strict_types=1);

final class AkunController extends Controller
{
    public function index(): void
    {
        $this->render('akun/index', [
            'pageTitle' => 'Akun Saya', 'active' => 'akun', 'crumbs' => ['Akun Saya' => null],
        ]);
    }

    public function password(): void
    {
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('akun/password', 8192);
            $password = input('password');
            if (!password_verify(input('current_password'), $this->user['password_hash'])) {
                $errors['current_password'] = 'Kata sandi saat ini belum sesuai.';
            }
            $message = PenggunaService::passwordError($password, input('password_confirmation'));
            if ($message) {
                $errors[$message === 'Konfirmasi kata sandi belum cocok.' ? 'password_confirmation' : 'password'] = $message;
            } elseif (password_verify($password, $this->user['password_hash'])) {
                $errors['password'] = 'Gunakan kata sandi yang berbeda dari kata sandi saat ini.';
            }
            if (!$errors) {
                User::changePassword((int) $this->user['id'], $password);
                Auth::login(User::findById((int) $this->user['id']));
                flash('success', 'Kata sandi diperbarui', 'Sesi di perangkat lain telah diakhiri.');
                back_to('akun/');
            }
            $flash = $this->formError('Periksa kembali isian kata sandi.');
        }
        $this->render('akun/password', compact('errors', 'flash') + [
            'pageTitle' => 'Ganti Kata Sandi', 'active' => 'akun', 'crumbs' => ['Akun Saya' => 'akun/', 'Ganti Kata Sandi' => null],
        ]);
    }
}
