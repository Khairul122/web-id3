<div class="page-head">
  <div><h1>Hasil Pengujian</h1><p><a href="<?= e(admin_url('training/detail?id=' . $evaluation['model_id'])) ?>"><?= e($evaluation['model_nama']) ?></a> · <?= e(ModelId3::SCENARIOS[$evaluation['skenario']] ?? '') ?> · <?= (int) $evaluation['k'] ?>-fold · <?= e(wib($evaluation['created_at'])) ?></p></div>
  <div class="page-actions"><a class="btn btn--outline" href="<?= e(admin_url('laporan/cetak?jenis=evaluasi&id=' . $evaluation['id'])) ?>"><?= icon('printer') ?> Cetak</a></div>
</div>

<div class="stat-grid">
  <div class="stat"><span class="stat__label">Akurasi</span><span class="stat__value"><?= e(pct($evaluation['akurasi'])) ?></span><span class="stat__note"><?= (int) $evaluation['jumlah_data'] ?> data uji</span></div>
  <div class="stat"><span class="stat__label">Precision macro</span><span class="stat__value"><?= e(pct($evaluation['precision_macro'])) ?></span></div>
  <div class="stat"><span class="stat__label">Recall macro</span><span class="stat__value"><?= e(pct($evaluation['recall_macro'])) ?></span></div>
  <div class="stat"><span class="stat__label">F1 macro</span><span class="stat__value"><?= e(pct($evaluation['f1_macro'])) ?></span></div>
</div>

<div class="grid grid--2">
  <section class="card" aria-labelledby="matrix-title">
    <div class="card__head"><h2 id="matrix-title">Confusion matrix</h2><p>Baris = label asli, kolom = hasil prediksi. Sel bergaris hijau adalah prediksi benar.</p></div>
    <div class="table-wrap"><table class="matrix">
      <thead><tr><th scope="col">Asli \ Prediksi</th><?php foreach ($classes as $class): ?><th scope="col"><?= e($class) ?></th><?php endforeach; ?></tr></thead>
      <tbody>
      <?php foreach ($classes as $actual): ?>
        <tr><th scope="row"><?= e($actual) ?></th>
          <?php foreach ($classes as $predicted): $value = (int) $matrix[$actual][$predicted]; ?>
            <td class="<?= heat($value, $max) ?><?= $actual === $predicted ? ' diag' : '' ?>"><?= $value ?></td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
  <section class="card" aria-labelledby="kelas-title">
    <div class="card__head"><h2 id="kelas-title">Metrik per kelas</h2></div>
    <div class="table-wrap"><table class="table table--cards">
      <thead><tr><th>Kelas</th><th class="num">Precision</th><th class="num">Recall</th><th class="num">F1</th><th class="num">Support</th></tr></thead>
      <tbody>
      <?php foreach ($evaluation['per_kelas_json'] as $class => $metric): ?>
        <tr>
          <td class="cell-title"><?= badge($class) ?></td>
          <td data-label="Precision" class="num"><?= e(pct($metric['precision'])) ?></td>
          <td data-label="Recall" class="num"><?= e(pct($metric['recall'])) ?></td>
          <td data-label="F1" class="num"><?= e(pct($metric['f1'])) ?></td>
          <td data-label="Support" class="num"><?= (int) $metric['support'] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
</div>

<section class="card" aria-labelledby="fold-title">
  <div class="card__head"><h2 id="fold-title">Akurasi per fold</h2></div>
  <div class="bars">
    <?php foreach ($evaluation['detail_fold_json'] as $fold): ?>
      <div class="bar">
        <div class="bar__label"><span>Fold <?= (int) $fold['fold'] ?> · latih <?= (int) $fold['latih'] ?>, uji <?= (int) $fold['uji'] ?></span><span><?= (int) $fold['benar'] ?> benar · <?= e(pct($fold['akurasi'])) ?></span></div>
        <?= stacked_bar(['navy' => (int) $fold['benar'], 'none' => (int) $fold['uji'] - (int) $fold['benar']], (int) $fold['uji'], 'Fold ' . $fold['fold'] . ' akurasi ' . pct($fold['akurasi'])) ?>
      </div>
    <?php endforeach; ?>
  </div>
</section>
