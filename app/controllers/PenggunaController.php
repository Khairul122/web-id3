<?php
declare(strict_types=1);

final class PenggunaController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->requireAdmin();
    }

    public function index(): void
    {
        $this->render('pengguna/index', [
            'users' => User::all(),
            'pageTitle' => 'Pengguna', 'active' => 'pengguna', 'crumbs' => ['Pengguna' => null],
        ]);
    }

    public function detail(): void
    {
        $account = User::findById(query_int('id')) ?? $this->notFound('pengguna/', 'Pengguna tidak tersedia.');
        $this->render('pengguna/detail', [
            'account' => $account,
            'pageTitle' => $account['nama'], 'active' => 'pengguna', 'crumbs' => ['Pengguna' => 'pengguna/', $account['username'] => null],
        ]);
    }

    public function form(): void
    {
        $id = query_int('id');
        $existing = $id ? (User::findById($id) ?? $this->notFound('pengguna/', 'Pengguna tidak tersedia.')) : null;
        $form = ['nama' => $existing['nama'] ?? '', 'username' => $existing['username'] ?? '', 'role' => $existing['role'] ?? 'guru_bk'];
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('pengguna/form' . ($id ? '?id=' . $id : ''));
            [$form, $errors] = PenggunaService::fromPost($existing);
            if (!$errors) {
                $savedId = PenggunaService::save($form, $existing);
                if ($savedId === (int) $this->user['id']) {
                    // Akun sendiri: perbarui sesi agar session_version baru tetap berlaku.
                    Auth::login(User::findById($savedId));
                }
                flash('success', 'Berhasil disimpan', 'Akun ' . $form['username'] . ($existing ? ' telah diperbarui.' : ' telah dibuat.'));
                back_to('pengguna/detail?id=' . $savedId);
            }
            $flash = $this->formError('Periksa kembali isian yang ditandai.');
        }
        $pageTitle = $existing ? 'Ubah Pengguna' : 'Tambah Pengguna';
        $this->render('pengguna/form', compact('existing', 'form', 'errors', 'flash', 'pageTitle') + [
            'active' => 'pengguna', 'crumbs' => ['Pengguna' => 'pengguna/', $pageTitle => null],
        ]);
    }

    public function hapus(): void
    {
        $this->verifyPost('pengguna/');
        $account = User::findById((int) $this->field('id'));
        if (!$account) {
            flash('error', 'Gagal menghapus', 'Pengguna tidak ditemukan.');
        } elseif ((int) $account['id'] === (int) $this->user['id']) {
            flash('error', 'Gagal menghapus', 'Anda tidak dapat menghapus akun yang sedang dipakai.');
        } elseif ($account['role'] === 'admin' && User::countAdmins() <= 1) {
            flash('error', 'Gagal menghapus', 'Harus ada minimal satu administrator.');
        } else {
            User::delete((int) $account['id']);
            flash('success', 'Berhasil dihapus', 'Akun ' . $account['username'] . ' telah dihapus.');
        }
        back_to('pengguna/');
    }
}
