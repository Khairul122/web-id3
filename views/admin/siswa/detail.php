<div class="page-head">
  <div><h1><?= e($siswa['nama']) ?></h1><p><?= e($siswa['kode_siswa'] . ' · ' . $siswa['sekolah_nama'] . ' · Kelas ' . $siswa['kelas']) ?></p></div>
  <?php if ($isAdmin): ?>
  <div class="page-actions">
    <a class="btn" href="<?= e(admin_url('siswa/form?id=' . $siswa['id'])) ?>"><?= icon('edit') ?> Ubah</a>
    <form method="post" action="<?= e(admin_url('siswa/hapus')) ?>" data-confirm="Data <?= e($siswa['nama']) ?> beserta nilai dan atributnya akan dihapus permanen." data-confirm-title="Hapus siswa?" data-confirm-ok="Ya, hapus" data-confirm-danger>
      <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $siswa['id'] ?>">
      <button class="btn btn--danger" type="submit"><?= icon('trash') ?> Hapus</button>
    </form>
  </div>
  <?php endif; ?>
</div>

<div class="grid grid--2">
  <section class="card" aria-labelledby="identitas-title">
    <div class="card__head"><h2 id="identitas-title">Identitas dan label</h2></div>
    <dl class="dl">
      <dt>NIS / NISN</dt><dd><?= e(($siswa['nis'] ?? '-') . ' / ' . ($siswa['nisn'] ?? '-')) ?></dd>
      <dt>Jenis kelamin</dt><dd><?= e(['L' => 'Laki-laki', 'P' => 'Perempuan'][$siswa['jk'] ?? ''] ?? '-') ?></dd>
      <dt>Peminatan</dt><dd><?= e($siswa['peminatan']) ?></dd>
      <dt>Label</dt><dd><?= badge($siswa['label']) ?></dd>
      <dt>Sumber label</dt><dd><?= e($siswa['sumber_label'] ?? '-') ?></dd>
      <dt>Detail label</dt><dd><?= e($siswa['detail_label'] ?? '-') ?></dd>
      <dt>Jurusan diterima</dt><dd><?= e($siswa['jurusan_diterima'] ?? '-') ?></dd>
      <dt>Diperbarui</dt><dd><?= e(wib($siswa['updated_at'])) ?></dd>
    </dl>
  </section>
  <section class="card" aria-labelledby="atribut-title">
    <div class="card__head"><h2 id="atribut-title">Atribut ID3</h2></div>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Atribut</th><th class="num">Nilai</th><th>Kategori</th></tr></thead>
      <tbody>
      <?php foreach ($attributes as $attribute): $column = Discretizer::SOURCES[$attribute['kode']] ?? null; ?>
        <tr>
          <td><?= e($attribute['nama']) ?></td>
          <td class="num"><?= $column ? e(num($siswa['nilai'][$column] ?? null)) : '-' ?></td>
          <td><?= badge($siswa['atribut'][$attribute['kode']] ?? '-') ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php if (!$siswa['nilai']): ?><p class="table-caption">Data ini hanya memiliki kategori, tanpa nilai angka.</p><?php endif; ?>
  </section>
</div>

<?php if ($live): ?>
<section class="card" aria-labelledby="live-title">
  <div class="card__head"><h2 id="live-title">Prediksi model aktif</h2><p>Dihitung langsung dengan model <?= e($model['nama']) ?>. Tidak disimpan ke riwayat.</p></div>
  <p>Hasil: <?= badge($live['kelas']) ?> dengan keyakinan <?= e(pct($live['keyakinan'])) ?><?php if ($siswa['label']): ?>. Label asli <?= badge($siswa['label']) ?>, prediksi <?= $live['kelas'] === $siswa['label'] ? '<strong>sesuai</strong>' : '<strong>tidak sesuai</strong>' ?><?php endif; ?>.</p>
  <ol class="path" aria-label="Jalur keputusan">
    <?php foreach ($live['jalur'] as $step): ?><li><span class="path__step"><?= e($step['atribut'] . ' = ' . $step['nilai']) ?></span></li><?php endforeach; ?>
    <li><?= badge($live['kelas']) ?></li>
  </ol>
</section>
<?php endif; ?>

<?php if ($siswa['mapel']): ?>
<section class="card" aria-labelledby="mapel-title">
  <div class="card__head"><h2 id="mapel-title">Nilai mapel legger</h2><p>Nilai asli dari legger sekolah, hasil import.</p></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><?php foreach ($siswa['mapel'] as $code => $value): ?><th class="num"><?= e($code) ?></th><?php endforeach; ?></tr></thead>
    <tbody><tr><?php foreach ($siswa['mapel'] as $value): ?><td class="num"><?= (int) $value ?></td><?php endforeach; ?></tr></tbody>
  </table></div>
</section>
<?php endif; ?>

<section class="card" aria-labelledby="riwayat-title">
  <div class="card__head"><h2 id="riwayat-title">Riwayat prediksi tersimpan</h2></div>
  <?php if (!$history): ?>
    <p class="table-caption">Belum ada prediksi tersimpan untuk siswa ini.</p>
  <?php else: ?>
  <div class="table-wrap"><table class="table table--cards">
    <thead><tr><th>Waktu</th><th>Model</th><th>Hasil</th><th class="num">Keyakinan</th><th class="actions">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($history as $row): ?>
      <tr>
        <td data-label="Waktu"><?= e(wib($row['created_at'])) ?></td>
        <td data-label="Model"><?= e($row['model_nama']) ?></td>
        <td data-label="Hasil"><?= badge($row['hasil']) ?></td>
        <td data-label="Keyakinan" class="num"><?= e(pct($row['keyakinan'])) ?></td>
        <td class="actions"><a class="link-btn" href="<?= e(admin_url('prediksi/detail?id=' . $row['id'])) ?>">Detail</a></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
  <?php endif; ?>
</section>
