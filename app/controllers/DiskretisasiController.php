<?php
declare(strict_types=1);

final class DiskretisasiController extends Controller
{
    public function index(): void
    {
        $rule = Diskretisasi::current();
        $this->render('diskretisasi/index', [
            'rule' => $rule,
            'tinggi' => (float) $rule['batas_tinggi'],
            'sedang' => (float) $rule['batas_sedang'],
            'numericAttributes' => array_filter(Atribut::all(), static fn (array $a): bool => isset(Discretizer::SOURCES[$a['kode']])),
            'pageTitle' => 'Diskretisasi Nilai', 'active' => 'diskretisasi', 'crumbs' => ['Master Data' => null, 'Diskretisasi' => null],
        ]);
    }

    public function form(): void
    {
        $this->requireAdmin();
        $rule = Diskretisasi::current();
        $form = ['batas_tinggi' => num($rule['batas_tinggi']), 'batas_sedang' => num($rule['batas_sedang'])];
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('diskretisasi/form');
            [$form, $errors, $values] = DiskretisasiService::fromPost();
            if (!$errors) {
                $affected = DiskretisasiService::apply($values['batas_tinggi'], $values['batas_sedang']);
                flash('success', 'Berhasil disimpan', 'Batas baru diterapkan. Kategori ' . $affected . ' siswa bernilai angka telah dihitung ulang. Latih ulang model agar memakai kategori baru.');
                back_to('diskretisasi/');
            }
            $flash = $this->formError('Periksa kembali batas nilai.');
        }
        $this->render('diskretisasi/form', compact('form', 'errors', 'flash') + [
            'pageTitle' => 'Ubah Batas Diskretisasi', 'active' => 'diskretisasi', 'crumbs' => ['Diskretisasi' => 'diskretisasi/', 'Ubah' => null],
        ]);
    }
}
