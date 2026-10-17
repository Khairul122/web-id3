<?php $class = strtolower($prediction['hasil']); $total = array_sum($prediction['distribusi_json']); ?>
<div class="page-head">
  <div><h1>Hasil Rekomendasi</h1><p><?= e($prediction['nama'] ?? 'Tanpa nama') ?> · <?= $prediction['sumber'] === 'massal' ? 'Prediksi massal' : 'Cek Rekomendasi' ?> · <?= e(wib($prediction['created_at'])) ?></p></div>
  <div class="page-actions">
    <button class="btn btn--outline no-print" type="button" data-print><?= icon('printer') ?> Cetak Hasil</button>
    <a class="btn" href="<?= e(admin_url('prediksi/form')) ?>"><?= icon('target') ?> Cek Lagi</a>
  </div>
</div>

<div class="result-head result-head--<?= e($class) ?>">
  <?= icon('cap', 'icon--xl') ?>
  <div><span>Rekomendasi rumpun</span><strong><?= e($prediction['hasil']) ?></strong><span>Keyakinan <?= e(pct($prediction['keyakinan'])) ?> dari distribusi daun pohon keputusan</span></div>
</div>

<div class="grid grid--2">
  <section class="card" aria-labelledby="yakin-title">
    <div class="card__head"><h2 id="yakin-title">Tingkat keyakinan</h2><p>Proporsi kelas data latih pada daun yang dicapai.</p></div>
    <div class="bars">
      <?php foreach ($prediction['distribusi_json'] as $label => $count): if (!$count) continue; ?>
        <div class="bar">
          <div class="bar__label"><span><?= e($label) ?></span><span><?= (int) $count ?> data · <?= e(pct($total ? $count / $total : 0)) ?></span></div>
          <?= stacked_bar([strtolower($label) => (int) $count, 'none' => $total - (int) $count], $total, $label . ' ' . pct($total ? $count / $total : 0)) ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
  <section class="card" aria-labelledby="jalur-title">
    <div class="card__head"><h2 id="jalur-title">Jalur keputusan</h2><p>Atribut yang ditelusuri dari akar pohon sampai daun.</p></div>
    <ol class="path">
      <?php foreach ($prediction['jalur_json'] as $step): ?><li><span class="path__step"><?= e($step['atribut'] . ' = ' . $step['nilai']) ?><?= $step['dikenal'] ? '' : ' (tidak dikenal, memakai mayoritas node)' ?></span></li><?php endforeach; ?>
      <li><?= badge($prediction['hasil']) ?></li>
    </ol>
    <?php if ($prediction['label_asli']): ?><p class="table-caption">Label asli siswa: <?= badge($prediction['label_asli']) ?></p><?php endif; ?>
  </section>
</div>

<div class="grid grid--2">
  <section class="card" aria-labelledby="prodi-title">
    <div class="card__head"><h2 id="prodi-title">Contoh program studi</h2><p>Dari data prodi yang diterima alumni (Mapping_Label dataset).</p></div>
    <ul class="legend"><?php foreach ($prodi as $name): ?><li class="badge badge--none"><?= e($name) ?></li><?php endforeach; ?></ul>
  </section>
  <section class="card" aria-labelledby="input-title">
    <div class="card__head"><h2 id="input-title">Data masukan</h2></div>
    <dl class="dl">
      <?php foreach ($attributeList as $attribute): ?>
        <dt><?= e($attribute['nama']) ?></dt><dd><?= e($prediction['input_json']['atribut'][$attribute['kode']] ?? '-') ?><?php $column = Discretizer::SOURCES[$attribute['kode']] ?? null; if ($column && isset($prediction['input_json']['nilai'][$column])): ?> <small>(nilai <?= e(num($prediction['input_json']['nilai'][$column])) ?>)</small><?php endif; ?></dd>
      <?php endforeach; ?>
      <dt>Model</dt><dd><?= e($prediction['model_nama']) ?><?= $prediction['model_aktif'] ? ' (aktif)' : '' ?></dd>
      <dt>Diproses oleh</dt><dd><?= e($prediction['pembuat'] ?? '-') ?></dd>
    </dl>
  </section>
</div>
<div class="notice"><?= icon('alert') ?><div>Hasil ini adalah rekomendasi, bukan keputusan akhir. Diskusikan dengan guru BK dan orang tua.</div></div>
