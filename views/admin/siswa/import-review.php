<div class="page-head"><div><h1>Tinjau Import</h1><p>Langkah 2 dari 2: periksa hasil pembacaan <?= e($parsed['file']) ?> sebelum menyimpan.</p></div></div>

<?php if ($parsed['errors']): ?>
  <div class="notice notice--error" role="alert"><?= icon('alert') ?><div><strong><?= count($parsed['errors']) ?> kesalahan ditemukan. Import tidak dapat disimpan.</strong>
    <ul><?php foreach (array_slice($parsed['errors'], 0, 30) as $message): ?><li><?= e($message) ?></li><?php endforeach; ?></ul>
    <?php if (count($parsed['errors']) > 30): ?><p>Dan <?= count($parsed['errors']) - 30 ?> kesalahan lain.</p><?php endif; ?>
  </div></div>
<?php else: ?>
  <div class="notice notice--success"><?= icon('check') ?><div>Validasi lulus. Jumlah data sesuai ketentuan penelitian.</div></div>
<?php endif; ?>

<?php if ($summary): ?>
<div class="stat-grid">
  <div class="stat"><span class="stat__label">Total siswa</span><span class="stat__value"><?= (int) $summary['total'] ?></span><span class="stat__note">Wajib <?= Importer::EXPECTED['total'] ?></span></div>
  <div class="stat"><span class="stat__label">Berlabel</span><span class="stat__value"><?= (int) $summary['berlabel'] ?></span><span class="stat__note">Wajib <?= Importer::EXPECTED['berlabel'] ?></span></div>
  <div class="stat"><span class="stat__label">Tanpa label</span><span class="stat__value"><?= (int) $summary['tanpa_label'] ?></span><span class="stat__note">Wajib <?= Importer::EXPECTED['tanpa_label'] ?></span></div>
  <div class="stat"><span class="stat__label">Kesalahan</span><span class="stat__value"><?= count($parsed['errors']) ?></span><span class="stat__note">Harus 0</span></div>
</div>

<div class="grid grid--sidebar">
  <section class="card" aria-labelledby="preview-title">
    <div class="card__head"><h2 id="preview-title">Pratinjau 10 baris pertama</h2></div>
    <div class="table-wrap"><table class="table table--cards">
      <thead><tr><th>Kode</th><th>Nama</th><th>Kelas</th><th>Peminatan</th><th>Label</th></tr></thead>
      <tbody>
      <?php foreach ($preview as $row): ?>
        <tr><td data-label="Kode"><?= e($row['kode_siswa']) ?></td><td class="cell-title"><?= e($row['nama']) ?></td><td data-label="Kelas"><?= e($row['kelas']) ?></td><td data-label="Peminatan"><?= e($row['peminatan']) ?></td><td data-label="Label"><?= badge($row['label'] ?: null) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
  <section class="card" aria-labelledby="dist-title">
    <div class="card__head"><h2 id="dist-title">Distribusi label</h2></div>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Sekolah</th><th>Label</th><th class="num">Jumlah</th></tr></thead>
      <tbody>
      <?php foreach ($summary['distribusi'] as $school => $labels): foreach ($labels as $label => $count): ?>
        <tr><td><?= e($school) ?></td><td><?= badge($label) ?></td><td class="num"><?= (int) $count ?></td></tr>
      <?php endforeach; endforeach; ?>
      </tbody>
    </table></div>
  </section>
</div>
<?php endif; ?>

<div class="card form-actions">
  <?php if (!$parsed['errors']): ?>
  <form method="post" data-busy data-confirm="Seluruh data siswa yang ada akan diganti dengan <?= (int) $summary['total'] ?> data dari file ini." data-confirm-title="Simpan hasil import?" data-confirm-ok="Ya, ganti data" data-confirm-danger>
    <?= Csrf::field() ?><input type="hidden" name="aksi" value="simpan">
    <button class="btn" type="submit"><?= icon('check') ?> Simpan Import</button>
  </form>
  <?php endif; ?>
  <form method="post">
    <?= Csrf::field() ?><input type="hidden" name="aksi" value="batal">
    <button class="btn btn--outline" type="submit">Batalkan</button>
  </form>
</div>
