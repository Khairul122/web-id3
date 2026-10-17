<?php
declare(strict_types=1);

final class AtributController extends Controller
{
    public function index(): void
    {
        $this->render('atribut/index', [
            'attributes' => Atribut::all(),
            'pageTitle' => 'Atribut ID3', 'active' => 'atribut', 'crumbs' => ['Master Data' => null, 'Atribut ID3' => null],
        ]);
    }

    public function detail(): void
    {
        $attribute = Atribut::find(query_int('id')) ?? $this->notFound('atribut/', 'Atribut yang dipilih tidak tersedia.');
        $rootGain = null;
        $model = ModelId3::active();
        if ($model) {
            foreach (ModelId3::steps((int) $model['id'])[0]['detail_gain'] ?? [] as $gain) {
                if ($gain['atribut'] === $attribute['kode']) {
                    $rootGain = $gain['gain'];
                }
            }
        }
        $this->render('atribut/detail', [
            'attribute' => $attribute,
            'distribution' => Atribut::distribution((int) $attribute['id']),
            'rootGain' => $rootGain,
            'pageTitle' => $attribute['nama'], 'active' => 'atribut', 'crumbs' => ['Atribut ID3' => 'atribut/', $attribute['kode'] => null],
        ]);
    }

    public function form(): void
    {
        $this->requireAdmin();
        $attribute = Atribut::find(query_int('id')) ?? $this->notFound('atribut/', 'Atribut yang dipilih tidak tersedia.');
        $form = ['nama' => $attribute['nama'], 'deskripsi' => $attribute['deskripsi'] ?? '', 'urutan' => (string) $attribute['urutan'], 'aktif' => (bool) $attribute['aktif']];
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('atribut/form?id=' . $attribute['id']);
            $form = ['nama' => $this->field('nama'), 'deskripsi' => $this->field('deskripsi'), 'urutan' => $this->field('urutan'), 'aktif' => $this->field('aktif') === '1'];
            if ($form['nama'] === '' || mb_strlen($form['nama']) > 100) {
                $errors['nama'] = 'Nama wajib diisi, maksimal 100 karakter.';
            }
            if (mb_strlen($form['deskripsi']) > 255) {
                $errors['deskripsi'] = 'Deskripsi maksimal 255 karakter.';
            }
            if (!ctype_digit($form['urutan']) || (int) $form['urutan'] < 1 || (int) $form['urutan'] > 99) {
                $errors['urutan'] = 'Urutan berupa angka 1 sampai 99.';
            }
            if (!$form['aktif'] && $attribute['aktif'] && count(Atribut::activeCodes()) <= 1) {
                $errors['aktif'] = 'Minimal satu atribut harus aktif.';
            }
            if (!$errors) {
                Atribut::update((int) $attribute['id'], $form['nama'], $form['deskripsi'], $form['aktif'], (int) $form['urutan']);
                flash('success', 'Berhasil disimpan', 'Atribut ' . $attribute['kode'] . ' telah diperbarui. Perubahan berlaku pada training berikutnya.');
                back_to('atribut/detail?id=' . $attribute['id']);
            }
            $flash = $this->formError('Periksa kembali isian yang ditandai.');
        }
        $this->render('atribut/form', compact('attribute', 'form', 'errors', 'flash') + [
            'pageTitle' => 'Ubah Atribut', 'active' => 'atribut', 'crumbs' => ['Atribut ID3' => 'atribut/', $attribute['kode'] => null],
        ]);
    }
}
