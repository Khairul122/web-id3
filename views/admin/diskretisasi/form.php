<div class="page-head"><div><h1>Ubah Batas Diskretisasi</h1><p>Perubahan langsung menghitung ulang kategori siswa yang memiliki nilai angka. Siswa yang hanya memiliki kategori tidak berubah.</p></div></div>
<form class="card form-grid form-grid--2" method="post" novalidate data-busy data-confirm="Kategori nilai seluruh siswa bernilai angka akan dihitung ulang. Model yang sudah dilatih tidak berubah." data-confirm-title="Terapkan batas baru?" data-confirm-ok="Ya, terapkan">
  <?= Csrf::field() ?>
  <div class="field">
    <label for="batas_tinggi">Batas Tinggi (nilai ≥)</label>
    <input id="batas_tinggi" name="batas_tinggi" inputmode="decimal" required value="<?= e($form['batas_tinggi']) ?>" <?= invalid_attr($errors, 'batas_tinggi') ?>>
    <?= field_error($errors, 'batas_tinggi') ?>
  </div>
  <div class="field">
    <label for="batas_sedang">Batas Sedang (nilai ≥)</label>
    <input id="batas_sedang" name="batas_sedang" inputmode="decimal" required value="<?= e($form['batas_sedang']) ?>" <?= invalid_attr($errors, 'batas_sedang') ?>>
    <p class="field__help">Nilai di bawah batas ini menjadi Rendah.</p>
    <?= field_error($errors, 'batas_sedang') ?>
  </div>
  <div class="form-actions span-2">
    <button class="btn" type="submit"><?= icon('check') ?> Simpan dan Terapkan</button>
    <a class="btn btn--outline" href="<?= e(admin_url('diskretisasi/')) ?>">Batal</a>
  </div>
</form>
