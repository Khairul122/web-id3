<div class="page-head"><div><h1><?= e($pageTitle) ?></h1><p>Langkah 1 dari 2: isi data. Berikutnya Anda meninjau kategori atribut sebelum menyimpan.</p></div></div>

<form class="card form-grid" method="post" novalidate data-tinggi="<?= e($rule->tinggi) ?>" data-sedang="<?= e($rule->sedang) ?>">
  <?= Csrf::field() ?>
  <section class="form-section form-grid form-grid--2" aria-labelledby="identitas-title">
    <div class="span-2"><h2 id="identitas-title">Identitas siswa</h2><p class="field__help">Nama, NIS, dan NISN hanya tampil di panel, tidak di halaman publik.</p></div>
    <?= text_field($form, $errors, 'kode_siswa', 'Kode siswa', 20, 'required placeholder="MAN-K1-01" autocapitalize="characters"') ?>
    <?= text_field($form, $errors, 'nama', 'Nama lengkap', 150, 'required') ?>
    <div class="field">
      <label for="sekolah_id">Sekolah</label>
      <select id="sekolah_id" name="sekolah_id" required <?= invalid_attr($errors, 'sekolah_id') ?>>
        <option value="">Pilih sekolah</option>
        <?php foreach ($schools as $school): ?><option value="<?= (int) $school['id'] ?>" <?= $form['sekolah_id'] === (string) $school['id'] ? 'selected' : '' ?>><?= e($school['kode'] . ' - ' . $school['nama']) ?></option><?php endforeach; ?>
      </select>
      <?= field_error($errors, 'sekolah_id') ?>
    </div>
    <?= text_field($form, $errors, 'kelas', 'Kelas', 10, 'required placeholder="XII.1"') ?>
    <?= text_field($form, $errors, 'nis', 'NIS (opsional)', 30) ?>
    <?= text_field($form, $errors, 'nisn', 'NISN (opsional)', 10, 'inputmode="numeric"') ?>
    <div class="field">
      <label for="jk">Jenis kelamin</label>
      <select id="jk" name="jk" <?= invalid_attr($errors, 'jk') ?>>
        <?php foreach (['' => 'Tidak diisi', 'L' => 'Laki-laki', 'P' => 'Perempuan'] as $value => $label): ?><option value="<?= e($value) ?>" <?= $form['jk'] === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
      </select>
      <?= field_error($errors, 'jk') ?>
    </div>
    <div class="field">
      <label for="peminatan">Peminatan</label>
      <select id="peminatan" name="peminatan" required <?= invalid_attr($errors, 'peminatan') ?>>
        <option value="">Pilih peminatan</option>
        <?php foreach ($domains['Peminatan'] as $value): ?><option <?= $form['peminatan'] === $value ? 'selected' : '' ?>><?= e($value) ?></option><?php endforeach; ?>
      </select>
      <p class="field__help">MAN memakai Umum. MAS mengikuti mapel pilihan di legger.</p>
      <?= field_error($errors, 'peminatan') ?>
    </div>
  </section>

  <section class="form-section form-grid" aria-labelledby="nilai-title">
    <div><h2 id="nilai-title">Nilai rapor</h2><p>Isi nilai agar kategori dihitung otomatis (Tinggi ≥ <?= e(num($rule->tinggi)) ?>, Sedang ≥ <?= e(num($rule->sedang)) ?>, selain itu Rendah). Bila nilai tidak tersedia, kosongkan dan pilih kategori manual.</p></div>
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
      <p class="field__help">Diabaikan bila rata-rata MIPA dan IPS keduanya diisi. Selisih 1 poin atau lebih menentukan arah.</p>
      <?= field_error($errors, 'Kecenderungan') ?>
    </div>
  </section>

  <section class="form-section form-grid form-grid--2" aria-labelledby="label-title">
    <div class="span-2"><h2 id="label-title">Label minat jurusan</h2><p>Kosongkan label untuk siswa yang akan diprediksi.</p></div>
    <div class="field">
      <label for="label">Label</label>
      <select id="label" name="label" <?= invalid_attr($errors, 'label') ?>>
        <option value="">Tanpa label</option>
        <?php foreach (Id3::CLASSES as $value): ?><option <?= $form['label'] === $value ? 'selected' : '' ?>><?= e($value) ?></option><?php endforeach; ?>
      </select>
      <?= field_error($errors, 'label') ?>
    </div>
    <?= text_field($form, $errors, 'sumber_label', 'Sumber label', 100, 'placeholder="Mapel pilihan TKA 2025"') ?>
    <?= text_field($form, $errors, 'detail_label', 'Detail label', 255, 'placeholder="Kimia + Biologi"') ?>
    <?= text_field($form, $errors, 'jurusan_diterima', 'Jurusan diterima', 255) ?>
  </section>

  <div class="form-actions">
    <button class="btn" type="submit">Lanjut ke Tinjauan <?= icon('arrow') ?></button>
    <a class="btn btn--outline" href="<?= e(admin_url($id ? 'siswa/detail?id=' . $id : 'siswa/')) ?>">Batal</a>
  </div>
</form>
