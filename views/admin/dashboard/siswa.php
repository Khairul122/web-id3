<div class="page-head"><div><h1>Dashboard</h1><p>Profil dan rekomendasi minat jurusan Anda.</p></div></div>
<?php if (!$siswa): ?>
<div class="card empty"><strong>Data Anda belum tersedia</strong>Hubungi guru BK untuk menghubungkan akun dengan data siswa.</div>
<?php else: ?>
<div class="grid grid--2">
  <section class="card" aria-labelledby="profil-title">
    <div class="card__head"><h2 id="profil-title">Profil Saya</h2></div>
    <dl class="dl">
      <dt>Nama</dt><dd><?= e($siswa['nama']) ?></dd>
      <dt>Sekolah</dt><dd><?= e($siswa['sekolah_nama']) ?></dd>
      <dt>Kelas</dt><dd><?= e($siswa['kelas']) ?></dd>
      <dt>Peminatan</dt><dd><?= e($siswa['peminatan']) ?></dd>
    </dl>
  </section>
  <section class="card" aria-labelledby="atribut-title">
    <div class="card__head"><h2 id="atribut-title">Nilai dan kategori ID3</h2></div>
    <div class="table-wrap"><table class="table"><thead><tr><th>Atribut</th><th class="num">Nilai</th><th>Kategori</th></tr></thead><tbody>
      <?php foreach ($attributes as $attribute): $column = Discretizer::SOURCES[$attribute['kode']] ?? null; ?>
      <tr><td><?= e($attribute['nama']) ?></td><td class="num"><?= $column ? e(num($siswa['nilai'][$column] ?? null)) : '-' ?></td><td><?= badge($siswa['atribut'][$attribute['kode']] ?? '-') ?></td></tr>
      <?php endforeach; ?>
    </tbody></table></div>
  </section>
</div>
<?php if ($live): ?>
<section class="card" aria-labelledby="hasil-title">
  <div class="card__head"><h2 id="hasil-title">Rekomendasi model aktif</h2><p><?= e($model['nama']) ?></p></div>
  <p>Rumpun <?= badge($live['kelas']) ?> dengan keyakinan <?= e(pct($live['keyakinan'])) ?>.</p>
  <ol class="path" aria-label="Jalur keputusan"><?php foreach ($live['jalur'] as $step): ?><li><span class="path__step"><?= e($step['atribut'] . ' = ' . $step['nilai']) ?></span></li><?php endforeach; ?></ol>
  <h3>Contoh program studi</h3><ul class="legend"><?php foreach (PrediksiService::PRODI[$live['kelas']] ?? [] as $prodi): ?><li><?= e($prodi) ?></li><?php endforeach; ?></ul>
</section>
<?php else: ?>
<div class="card empty"><strong>Belum ada model aktif</strong>Rekomendasi akan tampil setelah model diaktifkan.</div>
<?php endif; ?>
<div class="notice"><?= icon('alert') ?><div>Hasil ini bahan diskusi dengan guru BK, bukan keputusan akhir.</div></div>
<div class="form-actions"><a class="btn btn--accent" href="<?= e(admin_url('prediksi/form')) ?>">Simulasi Rekomendasi</a><a class="btn btn--outline" href="<?= e(admin_url('prediksi/')) ?>">Riwayat Saya</a></div>
<?php endif; ?>
