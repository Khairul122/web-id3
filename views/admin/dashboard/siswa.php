<div class="page-head"><div><h1>Halo, <?= e($siswa['nama'] ?? $user['nama']) ?></h1><p>Profil, nilai, dan rekomendasi minat jurusan Anda.</p></div><?php if ($siswa): ?><div class="page-actions"><a class="btn btn--accent" href="<?= e(admin_url('prediksi/form')) ?>">Simulasi Rekomendasi</a><a class="btn btn--outline" href="<?= e(admin_url('prediksi/')) ?>">Riwayat Saya</a></div><?php endif; ?></div>
<?php if (!$siswa): ?>
<div class="card empty"><strong>Data Anda belum tersedia</strong>Hubungi guru BK untuk menghubungkan akun dengan data siswa.</div>
<?php else:
$mapelNames = Importer::SUBJECT_NAMES;
$topMapel = $siswa['mapel'];
arsort($topMapel);
$topMapel = array_slice($topMapel, 0, 3, true);
/** Bar berskala 60 sampai 100 supaya selisih antar nilai terlihat; nilai asli tetap tertulis. */
$scale = static fn (float $value): float => max(0.0, min(100.0, ($value - 60) / 40 * 100));
?>
<section class="hero" aria-labelledby="hero-title">
  <div>
    <p class="hero__eyebrow" id="hero-title">Rekomendasi rumpun untuk Anda</p>
    <?php if ($live): ?>
    <p class="hero__result"><?= e($live['kelas']) ?></p>
    <p class="hero__text">Berdasarkan nilai dan peminatan Anda, dihitung dengan model <?= e($model['nama']) ?>.</p>
    <div class="bar">
      <div class="hero__conf"><span>Tingkat keyakinan</span><strong><?= e(pct($live['keyakinan'])) ?></strong></div>
      <?= stacked_bar(['saintek' => (int) round($live['keyakinan'] * 100)], 100, 'Keyakinan ' . pct($live['keyakinan'])) ?>
    </div>
    <?php else: ?>
    <p class="hero__result">Belum tersedia</p>
    <p class="hero__text">Rekomendasi akan tampil setelah guru BK mengaktifkan model.</p>
    <?php endif; ?>
  </div>
  <dl class="hero__facts">
    <div><dt>Rumpun tercatat</dt><dd><?= e($siswa['label'] ?? 'Belum ada') ?><?php if ($siswa['detail_label']): ?><small><?= e($siswa['detail_label']) ?></small><?php endif; ?></dd></div>
    <div><dt>Rata-rata umum</dt><dd><?= e(num($siswa['nilai']['rata_umum'] ?? null)) ?><small><?= count($siswa['mapel']) ?> mapel legger</small></dd></div>
    <div><dt>Kecenderungan</dt><dd><?= e($siswa['atribut']['Kecenderungan'] ?? '-') ?><small>MIPA dibanding IPS</small></dd></div>
    <div><dt>Prediksi tersimpan</dt><dd><?= count($history) ?><small><?= $history ? 'Terakhir ' . e(wib($history[0]['created_at'])) : 'Belum ada riwayat' ?></small></dd></div>
  </dl>
</section>

<div class="grid grid--2">
  <section class="card" aria-labelledby="skor-title">
    <div class="card__head"><h2 id="skor-title">Profil nilai rumpun</h2><p>Rata-rata per kelompok mapel beserta kategori yang dipakai model. Bar berskala 60 sampai 100.</p></div>
    <ul class="score-list">
      <?php foreach ($attributes as $attribute): $column = Discretizer::SOURCES[$attribute['kode']] ?? null; if (!$column || ($siswa['nilai'][$column] ?? null) === null) continue; $value = (float) $siswa['nilai'][$column]; ?>
      <li class="bar">
        <div class="bar__label"><span><?= e(preg_replace('/^Kategori /', '', $attribute['nama'])) ?> <?= badge($siswa['atribut'][$attribute['kode']] ?? null) ?></span><span><?= e(num($value)) ?></span></div>
        <?= stacked_bar(['saintek' => $scale($value)], 100, $attribute['nama'] . ' ' . num($value)) ?>
      </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <section class="card" aria-labelledby="profil-title">
    <div class="card__head"><h2 id="profil-title">Profil Saya</h2></div>
    <dl class="dl">
      <dt>Nama</dt><dd><?= e($siswa['nama']) ?></dd>
      <dt>Kode siswa</dt><dd><?= e($siswa['kode_siswa']) ?></dd>
      <?php if ($siswa['nisn']): ?><dt>NISN</dt><dd><?= e($siswa['nisn']) ?></dd><?php endif; ?>
      <dt>Sekolah</dt><dd><?= e($siswa['sekolah_nama']) ?></dd>
      <dt>Kelas</dt><dd><?= e($siswa['kelas']) ?></dd>
      <dt>Peminatan</dt><dd><?= e($siswa['peminatan']) ?></dd>
      <?php if ($siswa['sumber_label']): ?><dt>Dasar label</dt><dd><?= e($siswa['sumber_label']) ?></dd><?php endif; ?>
    </dl>
  </section>
</div>

<?php if ($siswa['mapel']): ?>
<section class="card" aria-labelledby="mapel-title">
  <div class="card__head"><h2 id="mapel-title">Nilai mapel</h2><p>Bar berskala 60 sampai 100. Mapel terkuat: <?= e(implode(', ', array_map(static fn ($code) => ($mapelNames[$code] ?? $code), array_keys($topMapel)))) ?>.</p></div>
  <ul class="mapel-grid"><?php foreach ($siswa['mapel'] as $code => $value): $name = $mapelNames[$code] ?? $code; ?>
    <li class="bar"><div class="bar__label"><span><?= e($name) ?></span><strong><?= (int) $value ?></strong></div><?= stacked_bar(['saintek' => $scale((float) $value)], 100, $name . ' ' . (int) $value) ?></li>
  <?php endforeach; ?></ul>
</section>
<?php endif; ?>

<?php if ($live): ?>
<section class="card" aria-labelledby="hasil-title">
  <div class="card__head"><h2 id="hasil-title">Bagaimana hasil ini didapat</h2><p>Model memeriksa atribut Anda satu per satu sampai mencapai rumpun <?= e($live['kelas']) ?>.</p></div>
  <ol class="steps" aria-label="Jalur keputusan"><?php foreach ($live['jalur'] as $step): ?><li><?= e($step['atribut']) ?> = <strong><?= e($step['nilai']) ?></strong></li><?php endforeach; ?></ol>
  <h3>Contoh program studi <?= badge($live['kelas']) ?></h3>
  <ul class="prodi"><?php foreach (PrediksiService::PRODI[$live['kelas']] ?? [] as $prodi): ?><li><?= e($prodi) ?></li><?php endforeach; ?></ul>
</section>
<?php endif; ?>

<section class="card" aria-labelledby="riwayat-title">
  <div class="card__head"><h2 id="riwayat-title">Riwayat prediksi saya</h2><?php if ($history): ?><a class="link-btn" href="<?= e(admin_url('prediksi/')) ?>">Lihat semua</a><?php endif; ?></div>
  <?php if (!$history): ?>
    <p class="table-caption">Belum ada prediksi tersimpan. Coba Simulasi Rekomendasi.</p>
  <?php else: ?>
  <div class="table-wrap"><table class="table table--cards">
    <thead><tr><th>Waktu</th><th>Model</th><th>Hasil</th><th class="num">Keyakinan</th><th class="actions">Aksi</th></tr></thead>
    <tbody>
    <?php foreach (array_slice($history, 0, 5) as $row): ?>
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
<div class="notice"><?= icon('alert') ?><div>Hasil ini bahan diskusi dengan guru BK, bukan keputusan akhir.</div></div>
<?php endif; ?>
