<div class="page-head"><div><h1><?= $isSiswa ? 'Simulasi Rekomendasi' : 'Cek Rekomendasi' ?></h1><p>Langkah 1 dari 2: isi nilai siswa. Model aktif <strong><?= e($model['nama']) ?></strong> akan memberi rekomendasi rumpun beserta jalur keputusannya.</p></div></div>

<form class="card form-grid" method="post" novalidate data-tinggi="<?= e($rule->tinggi) ?>" data-sedang="<?= e($rule->sedang) ?>">
  <?= Csrf::field() ?>
  <section class="form-section form-grid form-grid--2" aria-labelledby="siswa-title">
    <div class="span-2"><h2 id="siswa-title">Sekolah dan peminatan</h2></div>
    <?php if ($isSiswa): ?><div class="field"><strong>Nama siswa</strong><p><?= e($form['nama']) ?></p></div><?php else: ?><?= text_field($form, $errors, 'nama', 'Nama siswa (opsional)', 150, 'autocomplete="off"') ?><?php endif; ?>
    <?php if ($isSiswa): ?>
    <div class="field"><strong>Sekolah</strong><p><?= e($ownSiswa['sekolah_nama'] ?? $form['sekolah']) ?></p></div>
    <div class="field"><strong>Peminatan</strong><p><?= e($form['peminatan']) ?></p></div>
    <?php else: ?>
    <div class="field">
      <label for="sekolah">Sekolah</label>
      <select id="sekolah" name="sekolah" required <?= invalid_attr($errors, 'sekolah') ?>>
        <option value="">Pilih sekolah</option>
        <?php foreach ($schools as $school): ?><option value="<?= e($school['kode']) ?>" <?= $form['sekolah'] === $school['kode'] ? 'selected' : '' ?>><?= e($school['nama']) ?></option><?php endforeach; ?>
      </select>
      <?= field_error($errors, 'sekolah') ?>
    </div>
    <div class="field">
      <label for="peminatan">Peminatan</label>
      <select id="peminatan" name="peminatan" required <?= invalid_attr($errors, 'peminatan') ?>>
        <option value="">Pilih peminatan</option>
        <?php foreach ($domains['Peminatan'] as $value): ?><option <?= $form['peminatan'] === $value ? 'selected' : '' ?>><?= e($value) ?></option><?php endforeach; ?>
      </select>
      <p class="field__help">Siswa MAN memakai Umum.</p>
      <?= field_error($errors, 'peminatan') ?>
    </div>
    <?php endif; ?>
  </section>

  <section class="form-section form-grid" aria-labelledby="nilai-title">
    <div><h2 id="nilai-title">Nilai rapor</h2><p>Isi rata-rata nilai. Kategori dihitung otomatis: Tinggi ≥ <?= e(num($rule->tinggi)) ?>, Sedang ≥ <?= e(num($rule->sedang)) ?>. Bila mapel tidak diambil, kosongkan nilai dan pilih kategori Tidak Ada.</p></div>
    <?php foreach (Discretizer::SOURCES as $attribute => $column): ?>
      <div class="score-row">
        <div class="field">
          <label for="<?= e($column) ?>"><?= e(Discretizer::SCORE_LABELS[$column]) ?></label>
          <input id="<?= e($column) ?>" name="<?= e($column) ?>" type="number" inputmode="decimal" min="0" max="100" step="0.01" value="<?= e($form['nilai'][$column] ?? '') ?>" data-score="<?= e($column) ?>-hint" <?= invalid_attr($errors, $column) ?>>
          <p class="score-hint" id="<?= e($column) ?>-hint" aria-live="polite"></p>
          <?= field_error($errors, $column) ?>
        </div>
        <div class="field">
          <label for="kat_<?= e($attribute) ?>">Kategori manual</label>
          <select id="kat_<?= e($attribute) ?>" name="kat_<?= e($attribute) ?>">
            <option value="">Pilih</option>
            <?php foreach ($domains[$attribute] as $value): ?><option <?= ($form['manual'][$attribute] ?? '') === $value ? 'selected' : '' ?>><?= e($value) ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>
    <?php endforeach; ?>
    <div class="field">
      <label for="kat_Kecenderungan">Kecenderungan</label>
      <select id="kat_Kecenderungan" name="kat_Kecenderungan" <?= invalid_attr($errors, 'Kecenderungan') ?>>
        <option value="">Hitung dari rata-rata MIPA dan IPS</option>
        <?php foreach ($domains['Kecenderungan'] as $value): ?><option <?= ($form['manual']['Kecenderungan'] ?? '') === $value ? 'selected' : '' ?>><?= e($value) ?></option><?php endforeach; ?>
      </select>
      <?= field_error($errors, 'Kecenderungan') ?>
    </div>
  </section>

  <div class="form-actions">
    <button class="btn" type="submit">Lanjut ke Tinjauan <?= icon('arrow') ?></button>
    <a class="btn btn--outline" href="<?= e(admin_url('prediksi/')) ?>">Batal</a>
  </div>
</form>
