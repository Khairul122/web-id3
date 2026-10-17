<div class="page-head">
  <div>
    <h1>Dashboard</h1>
    <p>Ringkasan data siswa kelas XII, model ID3 aktif, dan hasil prediksi minat jurusan.</p>
  </div>
  <div class="page-actions">
    <a class="btn btn--outline" href="<?= e(admin_url('prediksi/form')) ?>"><?= icon('target') ?> Cek Rekomendasi</a>
    <?php if ($isAdmin): ?><a class="btn btn--accent" href="<?= e(admin_url('training/form')) ?>"><?= icon('play') ?> Latih Model</a><?php endif; ?>
  </div>
</div>

<div class="stat-grid">
  <a class="stat" href="<?= e(admin_url('siswa/')) ?>"><span class="stat__label">Total siswa</span><span class="stat__value"><?= (int) $counts['total'] ?></span><span class="stat__note">Dua madrasah</span></a>
  <a class="stat" href="<?= e(admin_url('siswa/?label=labeled')) ?>"><span class="stat__label">Data latih</span><span class="stat__value"><?= (int) $counts['berlabel'] ?></span><span class="stat__note">Siswa berlabel</span></a>
  <a class="stat" href="<?= e(admin_url('siswa/?label=none')) ?>"><span class="stat__label">Tanpa label</span><span class="stat__value"><?= (int) $counts['tanpa_label'] ?></span><span class="stat__note">Siap diprediksi</span></a>
  <a class="stat" href="<?= e(admin_url($evaluation ? 'pengujian/detail?id=' . $evaluation['id'] : 'pengujian/')) ?>"><span class="stat__label">Akurasi model aktif</span><span class="stat__value"><?= $evaluation ? e(pct($evaluation['akurasi'])) : '-' ?></span><span class="stat__note"><?= $evaluation ? e($evaluation['k'] . '-fold, F1 macro ' . pct($evaluation['f1_macro'])) : 'Belum diuji' ?></span></a>
</div>

<div class="grid grid--sidebar">
  <section class="card" aria-labelledby="dist-title">
    <div class="card__head"><h2 id="dist-title">Distribusi label per sekolah</h2></div>
    <?php if ((int) $counts['total'] === 0): ?>
      <div class="empty"><?= icon('users') ?><strong>Belum ada data siswa</strong>Import Dataset_Bersih_ID3.xlsx untuk memulai.<br><a class="btn" href="<?= e(admin_url('siswa/import')) ?>"><?= icon('upload') ?> Import Data</a></div>
    <?php else: ?>
      <div class="bars">
        <?php foreach ($bySchool as $school): ?>
          <div class="bar">
            <div class="bar__label"><span class="cell-strong"><?= e($school['nama']) ?></span><span><?= (int) $school['total'] ?> siswa</span></div>
            <?= stacked_bar(['saintek' => (int) $school['saintek'], 'soshum' => (int) $school['soshum'], 'campuran' => (int) $school['campuran'], 'none' => (int) $school['tanpa_label']], (int) $school['total'], $school['nama']) ?>
            <p class="table-caption">Saintek <?= (int) $school['saintek'] ?> · Soshum <?= (int) $school['soshum'] ?> · Campuran <?= (int) $school['campuran'] ?> · Tanpa label <?= (int) $school['tanpa_label'] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
      <ul class="legend">
        <li><?= legend_dot('saintek') ?> Saintek</li><li><?= legend_dot('soshum') ?> Soshum</li><li><?= legend_dot('campuran') ?> Campuran</li><li><?= legend_dot('none') ?> Tanpa label</li>
      </ul>
    <?php endif; ?>
  </section>

  <section class="card" aria-labelledby="model-title">
    <div class="card__head"><h2 id="model-title">Model aktif</h2></div>
    <?php if ($model): ?>
      <dl class="dl">
        <dt>Nama</dt><dd><a href="<?= e(admin_url('training/detail?id=' . $model['id'])) ?>"><?= e($model['nama']) ?></a></dd>
        <dt>Skenario</dt><dd><?= e(ModelId3::SCENARIOS[$model['skenario']] ?? $model['skenario']) ?></dd>
        <dt>Data latih</dt><dd><?= (int) $model['jumlah_data'] ?> siswa</dd>
        <dt>Akar pohon</dt><dd><?= e($model['tree']['atribut'] ?? $model['tree']['kelas']) ?></dd>
        <dt>Node / daun</dt><dd><?= (int) $model['jumlah_node'] ?> / <?= (int) $model['jumlah_daun'] ?></dd>
        <dt>Akurasi latih</dt><dd><?= e(pct($model['akurasi_latih'])) ?></dd>
        <dt>Atribut aktif</dt><dd><?= $activeAttributes ?> atribut</dd>
      </dl>
    <?php else: ?>
      <div class="empty"><?= icon('branch') ?><strong>Belum ada model aktif</strong>Latih model ID3 dari data berlabel.</div>
    <?php endif; ?>
  </section>
</div>

<div class="grid grid--2">
  <section class="card" aria-labelledby="recent-title">
    <div class="card__head"><h2 id="recent-title">Prediksi terbaru</h2><a class="link-btn" href="<?= e(admin_url('prediksi/')) ?>">Lihat semua</a></div>
    <?php if (!$recent): ?>
      <div class="empty"><?= icon('target') ?><strong>Belum ada prediksi</strong>Hasil Cek Rekomendasi dan prediksi massal tampil di sini.</div>
    <?php else: ?>
      <div class="table-wrap"><table class="table table--cards">
        <thead><tr><th>Nama</th><th>Sumber</th><th>Hasil</th><th class="num">Keyakinan</th></tr></thead>
        <tbody>
        <?php foreach ($recent as $row): ?>
          <tr>
            <td class="cell-title"><a href="<?= e(admin_url('prediksi/detail?id=' . $row['id'])) ?>"><?= e($row['nama'] ?? 'Tanpa nama') ?></a></td>
            <td data-label="Sumber"><?= $row['sumber'] === 'massal' ? 'Massal' : 'Form' ?></td>
            <td data-label="Hasil"><?= badge($row['hasil']) ?></td>
            <td data-label="Keyakinan" class="num"><?= e(pct($row['keyakinan'])) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    <?php endif; ?>
  </section>

  <section class="card" aria-labelledby="steps-title">
    <div class="card__head"><h2 id="steps-title">Alur kerja penelitian</h2></div>
    <ol class="check-grid check-grid--list">
      <?php foreach ($steps as $number => [$label, $done, $href]): ?>
        <li class="check">
          <span class="badge <?= $done ? 'badge--aktif' : 'badge--none' ?>"><?= $done ? 'Selesai' : 'Langkah ' . ($number + 1) ?></span>
          <span><?= e($label) ?><?php if (!$done): ?><small><a href="<?= e(admin_url($href)) ?>">Kerjakan sekarang</a></small><?php endif; ?></span>
        </li>
      <?php endforeach; ?>
    </ol>
  </section>
</div>
