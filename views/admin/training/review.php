<div class="page-head"><div><h1>Tinjau Training</h1><p>Langkah 2 dari 2: periksa data latih dan perhitungan di akar sebelum pohon dibentuk.</p></div></div>
<?php if ($jumlah < 2): ?>
  <div class="notice notice--error" role="alert"><?= icon('alert') ?><div>Data latih untuk skenario ini kurang dari 2 siswa. Import data atau pilih skenario lain.</div></div>
<?php endif; ?>
<div class="stat-grid">
  <div class="stat"><span class="stat__label">Data latih</span><span class="stat__value"><?= $jumlah ?></span><span class="stat__note"><?= e(ModelId3::SCENARIOS[$config['skenario']]) ?></span></div>
  <div class="stat"><span class="stat__label">Atribut</span><span class="stat__value"><?= count($config['atribut']) ?></span><span class="stat__note">dipakai</span></div>
  <div class="stat"><span class="stat__label">Entropy akar</span><span class="stat__value"><?= e(num($entropy, 4)) ?></span><span class="stat__note"><?= e(distribution_text($distribusi)) ?></span></div>
  <div class="stat"><span class="stat__label">Pruning</span><span class="stat__value"><?= (int) $config['min_sampel'] ?></span><span class="stat__note">min. sampel, kedalaman <?= $config['max_depth'] ?? 'bebas' ?></span></div>
</div>
<div class="grid grid--sidebar">
  <section class="card" aria-labelledby="gain-title">
    <div class="card__head"><h2 id="gain-title">Information gain di akar</h2><p>Atribut dengan gain tertinggi menjadi akar pohon. Baris hijau adalah calon akar.</p></div>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Atribut</th><th class="num">Gain</th></tr></thead>
      <tbody>
      <?php foreach ($gains as $gain): ?>
        <tr class="<?= $gain['gain'] === $gain_terbaik && $gain_terbaik > 0 ? 'row-best' : '' ?>"><td><?= e($gain['atribut']) ?></td><td class="num"><?= e(num($gain['gain'], 4)) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
  <section class="card" aria-labelledby="config-title">
    <div class="card__head"><h2 id="config-title">Konfigurasi</h2></div>
    <dl class="dl">
      <dt>Nama</dt><dd><?= e($config['nama']) ?></dd>
      <dt>Skenario</dt><dd><?= e(ModelId3::SCENARIOS[$config['skenario']]) ?></dd>
      <dt>Atribut</dt><dd><?= e(implode(', ', $config['atribut'])) ?></dd>
      <dt>Aktifkan</dt><dd><?= $config['aktifkan'] ? 'Ya' : 'Tidak' ?></dd>
    </dl>
  </section>
</div>
<div class="card form-actions">
  <?php if ($jumlah >= 2): ?>
  <form method="post" data-busy data-confirm="ID3 akan membentuk pohon dari <?= $jumlah ?> data latih<?= $config['aktifkan'] ? ' dan model ini menggantikan model aktif' : '' ?>." data-confirm-title="Jalankan training?" data-confirm-ok="Ya, jalankan">
    <?= Csrf::field() ?>
    <button class="btn btn--accent" type="submit"><?= icon('play') ?> Jalankan Training</button>
  </form>
  <?php endif; ?>
  <a class="btn btn--outline" href="<?= e(admin_url('training/form?draft=1')) ?>"><?= icon('edit') ?> Ubah Konfigurasi</a>
</div>
