<?php
declare(strict_types=1);

final class SekolahController extends Controller
{
    public function index(): void
    {
        $this->render('sekolah/index', [
            'schools' => $this->isAdmin ? Sekolah::all() : [Sekolah::find((int) $this->user['sekolah_id'])],
            'pageTitle' => 'Data Sekolah', 'active' => 'sekolah', 'crumbs' => ['Master Data' => null, 'Data Sekolah' => null],
        ]);
    }

    public function detail(): void
    {
        $school = Sekolah::find(query_int('id')) ?? $this->notFound('sekolah/', 'Sekolah yang dipilih tidak tersedia.');
        if (!$this->isAdmin && (int) $school['id'] !== (int) $this->user['sekolah_id']) {
            $this->notFound('sekolah/', 'Sekolah yang dipilih tidak tersedia.');
        }
        $classes = Sekolah::breakdown((int) $school['id']);
        $this->render('sekolah/detail', [
            'school' => $school,
            'classes' => $classes,
            'total' => array_sum(array_column($classes, 'total')),
            'pageTitle' => $school['nama'], 'active' => 'sekolah', 'crumbs' => ['Data Sekolah' => 'sekolah/', $school['kode'] => null],
        ]);
    }

    public function form(): void
    {
        $this->requireAdmin();
        $school = Sekolah::find(query_int('id')) ?? $this->notFound('sekolah/', 'Sekolah yang dipilih tidak tersedia.');
        $form = ['nama' => $school['nama'], 'alamat' => $school['alamat'] ?? ''];
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('sekolah/form?id=' . $school['id']);
            $form = ['nama' => $this->field('nama'), 'alamat' => $this->field('alamat')];
            if ($form['nama'] === '' || mb_strlen($form['nama']) > 150) {
                $errors['nama'] = 'Nama sekolah wajib diisi, maksimal 150 karakter.';
            }
            if (mb_strlen($form['alamat']) > 255) {
                $errors['alamat'] = 'Alamat maksimal 255 karakter.';
            }
            if (!$errors) {
                Sekolah::update((int) $school['id'], $form['nama'], $form['alamat']);
                flash('success', 'Berhasil disimpan', 'Data sekolah ' . $form['nama'] . ' telah diperbarui.');
                back_to('sekolah/detail?id=' . $school['id']);
            }
            $flash = $this->formError('Periksa kembali isian yang ditandai.');
        }
        $this->render('sekolah/form', compact('school', 'form', 'errors', 'flash') + [
            'pageTitle' => 'Ubah Sekolah', 'active' => 'sekolah', 'crumbs' => ['Data Sekolah' => 'sekolah/', 'Ubah' => null],
        ]);
    }
}
