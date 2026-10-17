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
            'result' => Prediksi::paginate($filter, $page),
            'perPage' => Prediksi::PER_PAGE,
            'model' => ModelId3::active(),
            'pageTitle' => 'Prediksi', 'active' => 'prediksi', 'crumbs' => ['Proses ID3' => null, 'Prediksi' => null],
        ]);
    }

    /** Cek Rekomendasi langkah 1: isi sekolah, peminatan, dan nilai. */
    public function form(): void
    {
        $model = $this->activeModel();
        $draft = $_SESSION['prediksi_draft'] ?? null;
        $form = query('draft') === '1' && $draft ? $draft : PrediksiService::EMPTY;
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('prediksi/form');
            [$form, $errors] = PrediksiService::fromPost();
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
            'pageTitle' => 'Cek Rekomendasi', 'active' => 'prediksi', 'crumbs' => ['Prediksi' => 'prediksi/', 'Cek Rekomendasi' => null],
        ]);
    }

    /** Langkah 2: tinjau kategori hasil diskretisasi, lalu jalankan prediksi. */
    public function review(): void
    {
        $model = $this->activeModel();
        $draft = $_SESSION['prediksi_draft'] ?? null;
        if (!$draft) {
            flash('error', 'Tidak ada data untuk ditinjau', 'Isi form Cek Rekomendasi terlebih dahulu.');
            back_to('prediksi/form');
        }
        if (is_post()) {
            $this->verifyPost('prediksi/review');
            $id = PrediksiService::predictForm($model, $draft, (int) $this->user['id']);
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
            'pageTitle' => 'Tinjau Input', 'active' => 'prediksi', 'crumbs' => ['Prediksi' => 'prediksi/', 'Cek Rekomendasi' => 'prediksi/form?draft=1', 'Tinjau' => null],
        ]);
    }

    public function detail(): void
    {
        $prediction = Prediksi::find(query_int('id')) ?? $this->notFound('prediksi/', 'Hasil prediksi tidak tersedia.');
        $this->render('prediksi/detail', [
            'prediction' => $prediction,
            'attributeList' => Atribut::all(),
            'prodi' => PrediksiService::PRODI[$prediction['hasil']] ?? [],
            'pageTitle' => 'Hasil Rekomendasi', 'active' => 'prediksi', 'crumbs' => ['Prediksi' => 'prediksi/', 'Hasil #' . $prediction['id'] => null],
        ]);
    }

    /** Prediksi massal siswa tanpa label: tinjau jumlah lalu jalankan. */
    public function massal(): void
    {
        $this->requireAdmin();
        $model = $this->activeModel();
        $students = Siswa::unlabeled();
        if (is_post()) {
            $this->verifyPost('prediksi/massal');
            if (!$students) {
                flash('error', 'Prediksi massal gagal', 'Tidak ada siswa tanpa label.');
                back_to('prediksi/massal');
            }
            $summary = PrediksiService::runBatch($model, (int) $this->user['id']);
            flash('success', 'Prediksi massal selesai', count($students) . ' siswa diprediksi: Saintek ' . $summary['Saintek'] . ', Soshum ' . $summary['Soshum'] . ', Campuran ' . $summary['Campuran'] . '.');
            back_to('prediksi/?sumber=massal');
        }
        $this->render('prediksi/massal', [
            'model' => $model,
            'students' => $students,
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
}
