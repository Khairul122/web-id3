<?php require APP_ROOT . '/views/partials/print_head.php'; ?>
<section class="card">
  <div class="card__head"><h2><?= e($model['nama']) ?></h2></div>
  <dl class="dl">
    <dt>Skenario</dt><dd><?= e(ModelId3::SCENARIOS[$model['skenario']] ?? $model['skenario']) ?></dd>
    <dt>Data latih</dt><dd><?= (int) $model['jumlah_data'] ?> siswa (<?= e(distribution_text($model['tree']['distribusi'])) ?>)</dd>
    <dt>Atribut</dt><dd><?= e(implode(', ', $model['atribut'])) ?></dd>
    <dt>Pruning</dt><dd>Minimal sampel <?= (int) $model['min_sampel'] ?>, kedalaman maksimal <?= $model['max_depth'] === null ? 'tanpa batas' : (int) $model['max_depth'] ?></dd>
    <dt>Struktur</dt><dd><?= (int) $model['jumlah_node'] ?> node, <?= (int) $model['jumlah_daun'] ?> daun, kedalaman <?= (int) $model['kedalaman'] ?></dd>
    <dt>Akurasi latih</dt><dd><?= e(pct($model['akurasi_latih'])) ?></dd>
  </dl>
</section>
<section class="card">
  <div class="card__head"><h2>Pohon keputusan</h2></div>
  <ul class="tree"><?= tree_html($model['tree'], null, 0, true) ?></ul>
</section>
<section class="card">
  <div class="card__head"><h2>Langkah perhitungan</h2></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th class="num">No</th><th>Node</th><th class="num">Data</th><th class="num">Entropy</th><th>Gain per atribut</th><th>Terpilih</th></tr></thead>
    <tbody>
    <?php foreach ($steps as $step): ?>
      <tr>
        <td class="num"><?= (int) $step['urutan'] ?></td><td><?= e($step['node_path']) ?></td><td class="num"><?= (int) $step['jumlah'] ?></td><td class="num"><?= e(num($step['entropy_parent'], 4)) ?></td>
        <td><?= e(implode('; ', array_map(static fn (array $g): string => $g['atribut'] . ' ' . num($g['gain'], 4), $step['detail_gain']))) ?></td>
        <td><?= e($step['atribut_terpilih'] ?? 'daun') ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>
<section class="card">
  <div class="card__head"><h2>Aturan IF-THEN</h2></div>
  <div class="table-wrap"><table class="table">
    <thead><tr><th class="num">No</th><th>Aturan</th><th class="num">Support</th><th class="num">Keyakinan</th></tr></thead>
    <tbody>
    <?php foreach ($rules as $rule): ?>
      <tr><td class="num"><?= (int) $rule['no_aturan'] ?></td><td><?= e(ModelId3::ruleText($rule['kondisi'], $rule['kelas'])) ?></td><td class="num"><?= (int) $rule['support'] ?></td><td class="num"><?= e(pct($rule['keyakinan'])) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>
