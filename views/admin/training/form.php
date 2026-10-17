<div class="page-head"><div><h1>Latih Model Baru</h1><p>Langkah 1 dari 2: atur data dan atribut. Berikutnya Anda meninjau data latih sebelum ID3 dijalankan.</p></div></div>
<form class="card form-grid" method="post" novalidate>
  <?= Csrf::field() ?>
  <div class="field">
    <label for="nama">Nama model</label>
    <input id="nama" name="nama" maxlength="100" required value="<?= e($form['nama']) ?>" <?= invalid_attr($errors, 'nama') ?>>
    <?= field_error($errors, 'nama') ?>
  </div>
  <fieldset class="field">
    <legend>Skenario data</legend>
    <div class="check-grid">
      <?php foreach (ModelId3::SCENARIOS as $value => $label): ?>
        <label class="check"><input type="radio" name="skenario" value="<?= e($value) ?>" <?= $form['skenario'] === $value ? 'checked' : '' ?>> <span><?= e($label) ?></span></label>
      <?php endforeach; ?>
    </div>
    <p class="field__help">Skenario per sekolah dan tanpa Campuran membantu membaca pengaruh ketidakseimbangan kelas di data MAS.</p>
    <?= field_error($errors, 'skenario') ?>
  </fieldset>
  <fieldset class="field">
    <legend>Atribut yang dipakai</legend>
    <div class="check-grid">
      <?php foreach ($attributes as $attribute): ?>
        <label class="check"><input type="checkbox" name="atribut[]" value="<?= e($attribute['kode']) ?>" <?= in_array($attribute['kode'], $form['atribut'], true) ? 'checked' : '' ?>> <span><?= e($attribute['nama']) ?><small><?= e($attribute['kode']) ?><?= $attribute['aktif'] ? '' : ' · nonaktif' ?></small></span></label>
      <?php endforeach; ?>
    </div>
    <?= field_error($errors, 'atribut') ?>
  </fieldset>
  <div class="form-grid form-grid--2">
    <div class="field">
      <label for="min_sampel">Minimal sampel untuk dipecah</label>
      <input id="min_sampel" name="min_sampel" type="number" min="1" max="50" required value="<?= e((string) $form['min_sampel']) ?>" <?= invalid_attr($errors, 'min_sampel') ?>>
      <p class="field__help">Node dengan data lebih sedikit dijadikan daun (pre-pruning). Default 2.</p>
      <?= field_error($errors, 'min_sampel') ?>
    </div>
    <div class="field">
      <label for="max_depth">Kedalaman maksimal</label>
      <input id="max_depth" name="max_depth" type="number" min="1" max="20" value="<?= e((string) ($form['max_depth'] ?? '')) ?>" placeholder="Tanpa batas" <?= invalid_attr($errors, 'max_depth') ?>>
      <p class="field__help">Kosongkan untuk ID3 penuh.</p>
      <?= field_error($errors, 'max_depth') ?>
    </div>
  </div>
  <label class="check"><input type="checkbox" name="aktifkan" value="1" <?= $form['aktifkan'] ? 'checked' : '' ?>> <span>Jadikan model aktif setelah training<small>Model aktif dipakai di Cek Rekomendasi dan prediksi massal.</small></span></label>
  <div class="form-actions">
    <button class="btn" type="submit">Lanjut ke Tinjauan <?= icon('arrow') ?></button>
    <a class="btn btn--outline" href="<?= e(admin_url('training/')) ?>">Batal</a>
  </div>
</form>
