<div class="page-head"><div><h1>Ubah Atribut</h1><p>Kode <code><?= e($attribute['kode']) ?></code> dan domain nilainya tetap agar data siswa konsisten.</p></div></div>
<form class="card form-grid form-grid--2" method="post" novalidate data-busy>
  <?= Csrf::field() ?>
  <div class="field">
    <label for="nama">Nama</label>
    <input id="nama" name="nama" maxlength="100" required value="<?= e($form['nama']) ?>" <?= invalid_attr($errors, 'nama') ?>>
    <?= field_error($errors, 'nama') ?>
  </div>
  <div class="field">
    <label for="urutan">Urutan</label>
    <input id="urutan" name="urutan" type="number" min="1" max="99" required value="<?= e($form['urutan']) ?>" <?= invalid_attr($errors, 'urutan') ?>>
    <p class="field__help">Dipakai sebagai pemecah seri bila gain dua atribut sama.</p>
    <?= field_error($errors, 'urutan') ?>
  </div>
  <div class="field span-2">
    <label for="deskripsi">Deskripsi</label>
    <textarea id="deskripsi" name="deskripsi" maxlength="255" <?= invalid_attr($errors, 'deskripsi') ?>><?= e($form['deskripsi']) ?></textarea>
    <?= field_error($errors, 'deskripsi') ?>
  </div>
  <div class="field span-2">
    <label class="check"><input type="checkbox" name="aktif" value="1" <?= $form['aktif'] ? 'checked' : '' ?> <?= invalid_attr($errors, 'aktif') ?>> <span>Aktif<small>Atribut aktif terpilih otomatis di form training.</small></span></label>
    <?= field_error($errors, 'aktif') ?>
  </div>
  <div class="form-actions span-2">
    <button class="btn" type="submit"><?= icon('check') ?> Simpan</button>
    <a class="btn btn--outline" href="<?= e(admin_url('atribut/detail?id=' . $attribute['id'])) ?>">Batal</a>
  </div>
</form>
