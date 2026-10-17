<div class="page-head">
  <div>
    <h1>Diskretisasi Nilai</h1>
    <p>ID3 hanya menerima nilai kategori. Nilai rapor diubah menjadi Tinggi, Sedang, atau Rendah dengan batas berikut. Batas default berasal dari persentil 33 dan 67 nilai mapel MAN 4.</p>
  </div>
  <?php if ($isAdmin): ?><div class="page-actions"><a class="btn" href="<?= e(admin_url('diskretisasi/form')) ?>"><?= icon('edit') ?> Ubah Batas</a></div><?php endif; ?>
</div>
<div class="grid grid--2">
  <section class="card" aria-labelledby="aturan-title">
    <div class="card__head"><h2 id="aturan-title">Aturan aktif</h2></div>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Kategori</th><th>Ketentuan</th></tr></thead>
      <tbody>
        <tr><td><?= badge('Tinggi') ?></td><td>Nilai ≥ <?= e(num($tinggi)) ?></td></tr>
        <tr><td><?= badge('Sedang') ?></td><td><?= e(num($sedang)) ?> ≤ nilai &lt; <?= e(num($tinggi)) ?></td></tr>
        <tr><td><?= badge('Rendah') ?></td><td>Nilai &lt; <?= e(num($sedang)) ?></td></tr>
        <tr><td><?= badge('Tidak Ada') ?></td><td>Mapel tidak diambil siswa</td></tr>
      </tbody>
    </table></div>
    <p class="table-caption">Terakhir diubah: <?= e(wib($rule['updated_at'])) ?></p>
  </section>
  <section class="card" aria-labelledby="atribut-title">
    <div class="card__head"><h2 id="atribut-title">Atribut yang memakai aturan ini</h2></div>
    <dl class="dl">
      <?php foreach ($numericAttributes as $attribute): ?>
        <dt><code><?= e($attribute['kode']) ?></code></dt><dd><?= e(Discretizer::SCORE_LABELS[Discretizer::SOURCES[$attribute['kode']]]) ?></dd>
      <?php endforeach; ?>
      <dt><code>Kecenderungan</code></dt><dd>Selisih rata-rata MIPA dan IPS. Selisih 1 poin atau lebih: MIPA atau IPS; kurang dari itu: Seimbang; salah satu kosong: Tidak Terukur.</dd>
    </dl>
  </section>
</div>
