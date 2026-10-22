<div class="page-head"><div><h1>Dashboard Guru BK</h1><p>Ringkasan hasil bimbingan<?= $model ? ' dengan model ' . e($model['nama']) : '; belum ada model aktif' ?>.</p></div><div class="page-actions"><?php if ($model): ?><a class="btn btn--accent" href="<?= e(admin_url('prediksi/form')) ?>">Cek Rekomendasi</a><?php endif; ?></div></div>
<div class="stat-grid">
  <a class="stat" href="<?= e(admin_url('siswa/')) ?>"><span class="stat__label">Total siswa</span><span class="stat__value"><?= (int) $counts['total'] ?></span></a>
  <a class="stat" href="<?= e(admin_url('siswa/?label=none')) ?>"><span class="stat__label">Tanpa label, belum diprediksi</span><span class="stat__value"><?= $unpredicted ?></span></a>
  <?php foreach ($byResult as $label => $total): ?><div class="stat"><span class="stat__label"><?= e($label) ?></span><span class="stat__value"><?= (int) $total ?></span></div><?php endforeach; ?>
</div>
<section class="card"><div class="card__head"><h2>Prediksi terbaru</h2><a class="link-btn" href="<?= e(admin_url('prediksi/')) ?>">Lihat semua</a></div>
<?php if (!$recent): ?><div class="empty"><strong>Belum ada prediksi</strong>Hasil bimbingan akan tampil di sini.</div><?php else: ?>
<div class="table-wrap"><table class="table table--cards"><thead><tr><th>Nama</th><th>Hasil</th><th>Waktu</th></tr></thead><tbody>
<?php foreach ($recent as $row): ?><tr><td class="cell-title"><a href="<?= e(admin_url('prediksi/detail?id=' . $row['id'])) ?>"><?= e($row['nama'] ?? 'Tanpa nama') ?></a></td><td data-label="Hasil"><?= badge($row['hasil']) ?></td><td data-label="Waktu"><?= e(wib($row['created_at'])) ?></td></tr><?php endforeach; ?>
</tbody></table></div><?php endif; ?></section>
