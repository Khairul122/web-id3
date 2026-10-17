<?php require APP_ROOT . '/views/partials/print_head.php'; ?>
<p class="table-caption"><?= count($rows) ?> hasil prediksi.</p>
<div class="table-wrap"><table class="table">
  <thead><tr><th class="num">ID</th><th>Waktu</th><th>Sumber</th><th>Nama</th><th>Model</th><th>Hasil</th><th class="num">Keyakinan</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $row): ?>
    <tr><td class="num"><?= (int) $row['id'] ?></td><td class="nowrap"><?= e(wib($row['created_at'])) ?></td><td><?= $row['sumber'] === 'massal' ? 'Massal' : 'Form' ?></td><td><?= e(($row['kode_siswa'] ? $row['kode_siswa'] . ' ' : '') . ($row['nama'] ?? '-')) ?></td><td><?= e($row['model_nama']) ?></td><td><?= e($row['hasil']) ?></td><td class="num"><?= e(pct($row['keyakinan'])) ?></td></tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
