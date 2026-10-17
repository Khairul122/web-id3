<?php
declare(strict_types=1);

/** Dasar controller panel admin: memastikan login, render view, dan validasi POST. */
abstract class Controller
{
    protected array $user;
    protected bool $isAdmin;

    public function __construct()
    {
        $this->user = Auth::requireUser();
        $this->isAdmin = $this->user['role'] === 'admin';
    }

    protected function render(string $view, array $data = []): void
    {
        View::admin($view, $data + ['user' => $this->user, 'isAdmin' => $this->isAdmin]);
    }

    protected function requireAdmin(): void
    {
        Auth::requireAdmin($this->user);
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
