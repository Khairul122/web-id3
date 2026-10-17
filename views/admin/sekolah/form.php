<div class="page-head"><div><h1>Ubah Sekolah</h1><p>Kode <?= e($school['kode']) ?> tetap karena menjadi nilai atribut ID3.</p></div></div>
<form class="card form-grid" method="post" novalidate data-busy>
  <?= Csrf::field() ?>
  <div class="field">
    <label for="kode">Kode</label>
    <input id="kode" value="<?= e($school['kode']) ?>" disabled>
  </div>
  <div class="field">
    <label for="nama">Nama sekolah</label>
    <input id="nama" name="nama" maxlength="150" required value="<?= e($form['nama']) ?>" <?= isset($errors['nama']) ? 'aria-invalid="true" aria-describedby="nama-error"' : '' ?>>
    <?php if (isset($errors['nama'])): ?><p class="field__error" id="nama-error"><?= e($errors['nama']) ?></p><?php endif; ?>
  </div>
  <div class="field">
    <label for="alamat">Alamat</label>
    <textarea id="alamat" name="alamat" maxlength="255" <?= isset($errors['alamat']) ? 'aria-invalid="true" aria-describedby="alamat-error"' : '' ?>><?= e($form['alamat']) ?></textarea>
    <?php if (isset($errors['alamat'])): ?><p class="field__error" id="alamat-error"><?= e($errors['alamat']) ?></p><?php endif; ?>
  </div>
  <div class="form-actions">
    <button class="btn" type="submit"><?= icon('check') ?> Simpan</button>
    <a class="btn btn--outline" href="<?= e(admin_url('sekolah/')) ?>">Batal</a>
  </div>
</form>
