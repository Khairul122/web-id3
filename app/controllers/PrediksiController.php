<?php
declare(strict_types=1);

final class PrediksiController extends Controller
{
    public function index(): void
    {
        $filter = ['sumber' => query('sumber'), 'hasil' => query('hasil')];
        $page = max(1, query_int('page'));
        $this->render('prediksi/index', [
            'filter' => $filter,
            'page' => $page,
            'result' => Prediksi::paginate($filter, $page, $this->isSiswa ? [(int) $this->user['id'], $this->ownSiswa ? (int) $this->ownSiswa['id'] : null] : null),
            'perPage' => Prediksi::PER_PAGE,
            'model' => ModelId3::active(),
            'pageTitle' => $this->isSiswa ? 'Riwayat Saya' : 'Prediksi', 'active' => $this->isSiswa ? 'riwayat' : 'prediksi', 'crumbs' => [$this->isSiswa ? 'Riwayat Saya' : 'Prediksi' => null],
        ]);
    }

    /** Cek Rekomendasi langkah 1: isi sekolah, peminatan, dan nilai. */
    public function form(): void
    {
        $this->requireOwnSiswa();
        $model = $this->activeModel();
        $draft = $_SESSION['prediksi_draft'] ?? null;
        $fromDraft = query('draft') === '1' && $draft;
        $form = $fromDraft ? $draft : PrediksiService::EMPTY;
        if ($this->isSiswa) {
            $form = PrediksiService::withSiswaIdentity($form, $this->ownSiswa);
            if (!$fromDraft) {
                $form = PrediksiService::withSiswaScores($form, $this->ownSiswa);
            }
        }
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('prediksi/form');
            [$form, $errors] = PrediksiService::fromPost($this->isSiswa ? $this->ownSiswa : null);
            if (!$errors) {
                $_SESSION['prediksi_draft'] = $form;
                back_to('prediksi/review');
            }
            $flash = $this->formError('Ada ' . count($errors) . ' isian yang perlu diperbaiki.');
            $flash['title'] = 'Gagal memproses';
        }
        $this->render('prediksi/form', compact('model', 'form', 'errors', 'flash') + [
            'domains' => Atribut::domains(),
            'schools' => Sekolah::all(),
            'rule' => Discretizer::fromDb(),
            'pageTitle' => $this->isSiswa ? 'Simulasi Rekomendasi' : 'Cek Rekomendasi', 'active' => $this->isSiswa ? 'simulasi' : 'prediksi', 'crumbs' => ['Prediksi' => 'prediksi/', 'Cek Rekomendasi' => null],
        ]);
    }

    /** Langkah 2: tinjau kategori hasil diskretisasi, lalu jalankan prediksi. */
    public function review(): void
    {
        $this->requireOwnSiswa();
        $model = $this->activeModel();
        $draft = $_SESSION['prediksi_draft'] ?? null;
        if (!$draft) {
            flash('error', 'Tidak ada data untuk ditinjau', 'Isi form Cek Rekomendasi terlebih dahulu.');
            back_to('prediksi/form');
        }
        if ($this->isSiswa) {
            $draft = PrediksiService::withSiswaIdentity($draft, $this->ownSiswa);
        }
        if (is_post()) {
            $this->verifyPost('prediksi/review');
            $id = PrediksiService::predictForm($model, $draft, (int) $this->user['id'], $this->isSiswa ? (int) $this->ownSiswa['id'] : null);
            unset($_SESSION['prediksi_draft']);
            $saved = Prediksi::find($id);
            flash('success', 'Rekomendasi selesai', 'Hasil: ' . $saved['hasil'] . ' dengan keyakinan ' . pct($saved['keyakinan']) . '.');
            back_to('prediksi/detail?id=' . $id);
        }
        $this->render('prediksi/review', [
            'model' => $model,
            'form' => $draft,
            'attributes' => PrediksiService::toAttributes($draft, Discretizer::fromDb()),
            'attributeList' => Atribut::all(),
            'pageTitle' => 'Tinjau Input', 'active' => $this->isSiswa ? 'simulasi' : 'prediksi', 'crumbs' => ['Prediksi' => 'prediksi/', 'Cek Rekomendasi' => 'prediksi/form?draft=1', 'Tinjau' => null],
        ]);
    }

    public function detail(): void
    {
        $prediction = Prediksi::find(query_int('id')) ?? $this->notFound('prediksi/', 'Hasil prediksi tidak tersedia.');
        if ($this->isSiswa && !Prediksi::ownedBy($prediction, (int) $this->user['id'], $this->ownSiswa ? (int) $this->ownSiswa['id'] : null)) {
            $this->notFound('prediksi/', 'Hasil prediksi tidak tersedia.');
        }
        $this->render('prediksi/detail', [
            'prediction' => $prediction,
            'attributeList' => Atribut::all(),
            'prodi' => PrediksiService::PRODI[$prediction['hasil']] ?? [],
            'pageTitle' => 'Hasil Rekomendasi', 'active' => $this->isSiswa ? 'riwayat' : 'prediksi', 'crumbs' => ['Prediksi' => 'prediksi/', 'Hasil #' . $prediction['id'] => null],
        ]);
    }

    /** Prediksi massal: tinjau jumlah lalu jalankan. Cakupan bawaan siswa tanpa label; "semua" ikut memprediksi siswa berlabel. */
    public function massal(): void
    {
        $this->requireAdmin();
        $model = $this->activeModel();
        $all = (is_post() ? input('cakupan') : query('cakupan')) === 'semua';
        $students = Siswa::forBatch($all);
        if (is_post()) {
            $this->verifyPost('prediksi/massal');
            if (!$students) {
                flash('error', 'Prediksi massal gagal', 'Tidak ada siswa untuk diprediksi.');
                back_to('prediksi/massal');
            }
            $summary = PrediksiService::runBatch($model, (int) $this->user['id'], $all);
            flash('success', 'Prediksi massal selesai', count($students) . ' siswa diprediksi: Saintek ' . $summary['Saintek'] . ', Soshum ' . $summary['Soshum'] . ', Campuran ' . $summary['Campuran'] . '.'
                . ($summary['berlabel'] ? ' ' . $summary['sesuai'] . ' dari ' . $summary['berlabel'] . ' siswa berlabel sesuai label asli.' : ''));
            back_to('prediksi/?sumber=massal');
        }
        $this->render('prediksi/massal', [
            'model' => $model,
            'students' => $students,
            'all' => $all,
            'pageTitle' => 'Prediksi Massal', 'active' => 'prediksi', 'crumbs' => ['Prediksi' => 'prediksi/', 'Massal' => null],
        ]);
    }

    public function hapus(): void
    {
        $this->requireAdmin();
        $this->verifyPost('prediksi/');
        $prediction = Prediksi::find((int) $this->field('id'));
        if (!$prediction) {
            flash('error', 'Gagal menghapus', 'Hasil prediksi tidak ditemukan.');
            back_to('prediksi/');
        }
        Prediksi::delete((int) $prediction['id']);
        flash('success', 'Berhasil dihapus', 'Hasil prediksi telah dihapus dari riwayat.');
        back_to('prediksi/');
    }

    private function activeModel(): array
    {
        $model = ModelId3::active();
        if (!$model) {
            flash('error', 'Belum ada model aktif', 'Latih dan aktifkan model ID3 terlebih dahulu.');
            back_to($this->isAdmin ? 'training/form' : 'prediksi/');
        }
        return $model;
    }

    private function requireOwnSiswa(): void
    {
        if ($this->isSiswa && !$this->ownSiswa) {
            flash('error', 'Data belum tersedia', 'Data Anda belum tersedia, hubungi guru BK.');
            back_to('dashboard/');
        }
    }
}
