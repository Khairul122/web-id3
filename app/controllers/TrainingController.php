<?php
declare(strict_types=1);

final class TrainingController extends Controller
{
    public function index(): void
    {
        $this->render('training/index', [
            'models' => ModelId3::all(),
            'pageTitle' => 'Training Model', 'active' => 'training', 'crumbs' => ['Proses ID3' => null, 'Training Model' => null],
        ]);
    }

    public function detail(): void
    {
        $model = ModelId3::find(query_int('id')) ?? $this->notFound('training/', 'Model yang dipilih tidak tersedia.');
        $this->render('training/detail', [
            'model' => $model,
            'steps' => ModelId3::steps((int) $model['id']),
            'rules' => ModelId3::rules((int) $model['id']),
            'evaluation' => Evaluasi::latestForModel((int) $model['id']),
            'pageTitle' => $model['nama'], 'active' => 'training', 'crumbs' => ['Training Model' => 'training/', $model['nama'] => null],
        ]);
    }

    /** Gambar pohon keputusan (SVG). ?id=…&unduh=1 untuk mengunduh. */
    public function pohon(): void
    {
        $model = ModelId3::find(query_int('id')) ?? $this->notFound('training/', 'Model yang dipilih tidak tersedia.');
        $evaluation = Evaluasi::latestForModel((int) $model['id']);
        $subtitle = (ModelId3::SCENARIOS[$model['skenario']] ?? $model['skenario']) . ' · ' . $model['jumlah_data'] . ' data latih · '
            . count(ModelId3::rules((int) $model['id'])) . ' aturan · akurasi latih ' . pct($model['akurasi_latih'])
            . ($evaluation ? ' · akurasi ' . $evaluation['k'] . '-fold ' . pct($evaluation['akurasi']) : '');
        header('Content-Type: image/svg+xml; charset=utf-8');
        if (query('unduh') === '1') {
            header('Content-Disposition: attachment; filename="pohon-id3-model-' . (int) $model['id'] . '.svg"');
        }
        echo TreeImage::svg($model['tree'], 'Pohon Keputusan ID3 · ' . $model['nama'], $subtitle);
    }

    /** Langkah 1: konfigurasi skenario, atribut, dan pruning. */
    public function form(): void
    {
        $this->requireAdmin();
        $draft = $_SESSION['training_draft'] ?? null;
        $form = query('draft') === '1' && $draft ? $draft : TrainingService::defaults();
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('training/form');
            [$form, $errors] = TrainingService::fromPost();
            if (!$errors) {
                $_SESSION['training_draft'] = $form;
                back_to('training/review');
            }
            $flash = $this->formError('Periksa kembali konfigurasi training.');
        }
        $this->render('training/form', compact('form', 'errors', 'flash') + [
            'attributes' => Atribut::all(),
            'pageTitle' => 'Latih Model Baru', 'active' => 'training', 'crumbs' => ['Training Model' => 'training/', 'Latih Model' => null],
        ]);
    }

    /** Langkah 2: tinjau data latih dan gain akar, lalu jalankan ID3. */
    public function review(): void
    {
        $this->requireAdmin();
        $config = $_SESSION['training_draft'] ?? null;
        if (!$config) {
            flash('error', 'Tidak ada konfigurasi', 'Isi form training terlebih dahulu.');
            back_to('training/form');
        }
        if (is_post()) {
            $this->verifyPost('training/review');
            try {
                [$id, $result] = TrainingService::run($config, (int) $this->user['id']);
            } catch (DomainException $exception) {
                flash('error', 'Training gagal', $exception->getMessage());
                back_to('training/review');
            }
            unset($_SESSION['training_draft']);
            flash('success', 'Training berhasil', 'Model ' . $config['nama'] . ' terbentuk dengan ' . $result['stats']['node'] . ' node dan ' . count($result['rules']) . ' aturan. Akurasi data latih ' . pct($result['akurasi_latih']) . '.');
            back_to('training/detail?id=' . $id);
        }
        $this->render('training/review', ['config' => $config] + TrainingService::preview($config) + [
            'pageTitle' => 'Tinjau Training', 'active' => 'training',
            'crumbs' => ['Training Model' => 'training/', 'Latih Model' => 'training/form?draft=1', 'Tinjau' => null],
        ]);
    }

    public function aktifkan(): void
    {
        $this->requireAdmin();
        $this->verifyPost('training/');
        $model = ModelId3::find((int) $this->field('id'));
        if (!$model) {
            flash('error', 'Gagal mengaktifkan', 'Model tidak ditemukan.');
            back_to('training/');
        }
        ModelId3::activate((int) $model['id']);
        flash('success', 'Model diaktifkan', $model['nama'] . ' sekarang dipakai untuk prediksi.');
        back_to('training/detail?id=' . $model['id']);
    }

    public function hapus(): void
    {
        $this->requireAdmin();
        $this->verifyPost('training/');
        $model = ModelId3::find((int) $this->field('id'));
        if (!$model) {
            flash('error', 'Gagal menghapus', 'Model tidak ditemukan.');
            back_to('training/');
        }
        if ($model['aktif']) {
            flash('error', 'Gagal menghapus', 'Model aktif tidak dapat dihapus. Aktifkan model lain terlebih dahulu.');
            back_to('training/detail?id=' . $model['id']);
        }
        ModelId3::delete((int) $model['id']);
        flash('success', 'Berhasil dihapus', 'Model ' . $model['nama'] . ' telah dihapus.');
        back_to('training/');
    }
}
