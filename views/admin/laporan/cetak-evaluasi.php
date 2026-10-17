<?php require APP_ROOT . '/views/partials/print_head.php'; ?>
<section class="card">
  <div class="card__head"><h2><?= e($evaluation['model_nama']) ?> · <?= (int) $evaluation['k'] ?>-fold cross validation</h2></div>
  <dl class="dl">
    <dt>Skenario</dt><dd><?= e(ModelId3::SCENARIOS[$evaluation['skenario']] ?? '') ?></dd>
    <dt>Jumlah data uji</dt><dd><?= (int) $evaluation['jumlah_data'] ?></dd>
    <dt>Seed acak</dt><dd><?= Evaluator::SEED ?> (stratified)</dd>
    <dt>Akurasi</dt><dd><?= e(pct($evaluation['akurasi'])) ?></dd>
    <dt>Precision / Recall / F1 macro</dt><dd><?= e(pct($evaluation['precision_macro']) . ' / ' . pct($evaluation['recall_macro']) . ' / ' . pct($evaluation['f1_macro'])) ?></dd>
    <dt>Waktu pengujian</dt><dd><?= e(wib($evaluation['created_at'])) ?></dd>
  </dl>
</section>
<div class="grid grid--2">
  <section class="card">
    <div class="card__head"><h2>Confusion matrix</h2></div>
    <table class="matrix">
      <thead><tr><th scope="col">Asli \ Prediksi</th><?php foreach ($classes as $class): ?><th scope="col"><?= e($class) ?></th><?php endforeach; ?></tr></thead>
      <tbody><?php foreach ($classes as $actual): ?><tr><th scope="row"><?= e($actual) ?></th><?php foreach ($classes as $predicted): $value = (int) $matrix[$actual][$predicted]; ?><td class="<?= heat($value, $max) ?><?= $actual === $predicted ? ' diag' : '' ?>"><?= $value ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody>
    </table>
  </section>
  <section class="card">
    <div class="card__head"><h2>Metrik per kelas</h2></div>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Kelas</th><th class="num">Precision</th><th class="num">Recall</th><th class="num">F1</th><th class="num">Support</th></tr></thead>
      <tbody><?php foreach ($evaluation['per_kelas_json'] as $class => $metric): ?><tr><td><?= e($class) ?></td><td class="num"><?= e(pct($metric['precision'])) ?></td><td class="num"><?= e(pct($metric['recall'])) ?></td><td class="num"><?= e(pct($metric['f1'])) ?></td><td class="num"><?= (int) $metric['support'] ?></td></tr><?php endforeach; ?></tbody>
    </table></div>
  </section>
</div>
<section class="card">
  <div class="card__head"><h2>Hasil per fold</h2></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th class="num">Fold</th><th class="num">Data latih</th><th class="num">Data uji</th><th class="num">Benar</th><th class="num">Akurasi</th></tr></thead>
    <tbody><?php foreach ($evaluation['detail_fold_json'] as $fold): ?><tr><td class="num"><?= (int) $fold['fold'] ?></td><td class="num"><?= (int) $fold['latih'] ?></td><td class="num"><?= (int) $fold['uji'] ?></td><td class="num"><?= (int) $fold['benar'] ?></td><td class="num"><?= e(pct($fold['akurasi'])) ?></td></tr><?php endforeach; ?></tbody>
  </table></div>
</section>
