<?php
declare(strict_types=1);

final class PengujianController extends Controller
{
    public function index(): void
    {
        $this->render('pengujian/index', [
            'evaluations' => Evaluasi::all(),
            'pageTitle' => 'Pengujian Model', 'active' => 'pengujian', 'crumbs' => ['Proses ID3' => null, 'Pengujian Model' => null],
        ]);
    }

    public function detail(): void
    {
        $evaluation = Evaluasi::find(query_int('id')) ?? $this->notFound('pengujian/', 'Hasil pengujian tidak tersedia.');
        [$classes, $max] = EvaluasiService::matrixView($evaluation['confusion_json']);
        $this->render('pengujian/detail', [
            'evaluation' => $evaluation,
            'matrix' => $evaluation['confusion_json'],
            'classes' => $classes,
            'max' => $max,
            'pageTitle' => 'Hasil Pengujian', 'active' => 'pengujian', 'crumbs' => ['Pengujian Model' => 'pengujian/', 'Hasil #' . $evaluation['id'] => null],
        ]);
    }

    public function form(): void
    {
        $this->requireAdmin();
        $models = ModelId3::all();
        $preferred = query_int('model_id') ?: (int) (array_values(array_filter($models, static fn (array $m): bool => (bool) $m['aktif']))[0]['id'] ?? $models[0]['id'] ?? 0);
        $form = ['model_id' => (string) $preferred, 'k' => '5'];
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('pengujian/form');
            $form = ['model_id' => $this->field('model_id'), 'k' => $this->field('k')];
            $model = ctype_digit($form['model_id']) ? ModelId3::find((int) $form['model_id']) : null;
            if (!$model) {
                $errors['model_id'] = 'Pilih model yang akan diuji.';
            }
            if (!ctype_digit($form['k']) || (int) $form['k'] < 3 || (int) $form['k'] > 10) {
                $errors['k'] = 'Nilai k antara 3 sampai 10.';
            }
            if (!$errors) {
                try {
                    $run = EvaluasiService::run($model, (int) $form['k'], (int) $this->user['id']);
                    flash('success', 'Pengujian selesai', trim('Akurasi ' . pct($run['result']['akurasi']) . ', F1 macro ' . pct($run['result']['f1']) . ' dari ' . $run['result']['jumlah'] . ' prediksi. ' . $run['catatan']));
                    back_to('pengujian/detail?id=' . $run['id']);
                } catch (DomainException $exception) {
                    $errors['k'] = $exception->getMessage();
                }
            }
            $flash = $this->formError((string) reset($errors));
            $flash['title'] = 'Gagal menguji';
        }
        $this->render('pengujian/form', compact('models', 'form', 'errors', 'flash') + [
            'pageTitle' => 'Uji Model', 'active' => 'pengujian', 'crumbs' => ['Pengujian Model' => 'pengujian/', 'Uji Model' => null],
        ]);
    }

    public function hapus(): void
    {
        $this->requireAdmin();
        $this->verifyPost('pengujian/');
        $evaluation = Evaluasi::find((int) $this->field('id'));
        if (!$evaluation) {
            flash('error', 'Gagal menghapus', 'Hasil pengujian tidak ditemukan.');
            back_to('pengujian/');
        }
        Evaluasi::delete((int) $evaluation['id']);
        flash('success', 'Berhasil dihapus', 'Hasil pengujian model ' . $evaluation['model_nama'] . ' telah dihapus.');
        back_to('pengujian/');
    }
}
