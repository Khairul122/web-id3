<?php
declare(strict_types=1);

/** Dasar controller panel admin: memastikan login, render view, dan validasi POST. */
abstract class Controller
{
    /** Modul yang boleh dibuka akun siswa. Modul lain tertutup bagi siswa tanpa perlu penjaga per aksi. */
    private const SISWA_MODULES = [DashboardController::class, PrediksiController::class, AkunController::class];

    protected array $user;
    protected bool $isAdmin;
    protected bool $isSiswa;
    /** Data siswa milik akun siswa yang login; null untuk peran lain atau bila kodenya belum ada di data siswa. */
    protected ?array $ownSiswa = null;

    public function __construct()
    {
        $this->user = Auth::requireUser();
        $this->isAdmin = $this->user['role'] === 'admin';
        $this->isSiswa = $this->user['role'] === 'siswa';
        if ($this->isSiswa) {
            if (!in_array(static::class, self::SISWA_MODULES, true)) {
                $this->denySiswa();
            }
            $this->ownSiswa = $this->user['kode_siswa'] ? Siswa::findByKode($this->user['kode_siswa']) : null;
        }
    }

    protected function render(string $view, array $data = []): void
    {
        View::admin($view, $data + ['user' => $this->user, 'isAdmin' => $this->isAdmin, 'isSiswa' => $this->isSiswa, 'ownSiswa' => $this->ownSiswa]);
    }

    protected function requireAdmin(): void
    {
        Auth::requireAdmin($this->user);
    }

    /** Aksi untuk admin dan guru BK saja. */
    protected function requireStaff(): void
    {
        if ($this->isSiswa) {
            $this->denySiswa();
        }
    }

    private function denySiswa(): never
    {
        flash('error', 'Akses ditolak', 'Halaman tersebut tidak tersedia untuk akun siswa.');
        back_to('dashboard/');
    }

    /** Hanya POST, ukuran body dibatasi, token CSRF wajib. Gagal = dialog error lalu kembali ke $backTo. */
    protected function verifyPost(string $backTo, int $maxBytes = 65536): void
    {
        if (!is_post()) {
            http_response_code(405);
            header('Allow: POST');
            exit;
        }
        if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $maxBytes) {
            flash('error', 'Gagal', 'Data yang dikirim terlalu besar.');
            back_to($backTo);
        }
        if (!Csrf::verify()) {
            flash('error', 'Sesi form berakhir', 'Muat ulang halaman lalu coba lagi.');
            back_to($backTo);
        }
    }

    protected function notFound(string $backTo, string $message): never
    {
        flash('error', 'Data tidak ditemukan', $message);
        back_to($backTo);
    }

    /** Dialog gagal untuk form yang ditampilkan ulang pada request yang sama. */
    protected function formError(string $message): array
    {
        http_response_code(422);
        return ['type' => 'error', 'title' => 'Gagal menyimpan', 'message' => $message];
    }

    protected function field(string $key): string
    {
        return trim(input($key));
    }
}
