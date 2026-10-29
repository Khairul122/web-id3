<div class="page-head">
  <div><h1>Laporan</h1><p>Cetak laporan untuk lampiran skripsi (A4) atau unduh data dalam format CSV.</p></div>
</div>
<div class="grid grid--2">
  <section class="card" aria-labelledby="siswa-title">
    <div class="card__head"><h2 id="siswa-title">Data siswa</h2><p>Atribut ID3 dan label tiap siswa. Nama tampil karena laporan hanya untuk internal sekolah.</p></div>
    <form class="form-grid" method="get" action="<?= e(admin_url('laporan/cetak')) ?>">
      <input type="hidden" name="jenis" value="siswa">
      <div class="form-grid form-grid--2">
        <div class="field">
          <label for="sekolah_id">Sekolah</label>
          <select id="sekolah_id" name="sekolah_id"><option value="">Semua sekolah</option><?php foreach ($schools as $school): ?><option value="<?= (int) $school['id'] ?>"><?= e($school['kode']) ?></option><?php endforeach; ?></select>
        </div>
        <div class="field">
          <label for="label">Label</label>
          <select id="label" name="label"><option value="">Semua</option><option value="labeled">Berlabel</option><option value="none">Tanpa label</option></select>
        </div>
      </div>
      <div class="page-actions">
        <button class="btn" type="submit"><?= icon('printer') ?> Cetak</button>
        <a class="btn btn--outline" href="<?= e(admin_url('laporan/csv?jenis=siswa')) ?>"><?= icon('download') ?> Unduh CSV</a>
      </div>
    </form>
  </section>
  <section class="card" aria-labelledby="prediksi-title">
    <div class="card__head"><h2 id="prediksi-title">Hasil prediksi</h2><p>Seluruh riwayat Cek Rekomendasi dan prediksi massal.</p></div>
    <div class="page-actions">
      <a class="btn" href="<?= e(admin_url('laporan/cetak?jenis=prediksi')) ?>"><?= icon('printer') ?> Cetak</a>
      <a class="btn btn--outline" href="<?= e(admin_url('laporan/csv?jenis=prediksi')) ?>"><?= icon('download') ?> Unduh CSV</a>
    </div>
  </section>
</div>
<?php if ($isAdmin): ?>
<div class="grid grid--2">
  <section class="card" aria-labelledby="model-title">
    <div class="card__head"><h2 id="model-title">Model, pohon, dan aturan</h2></div>
    <?php if (!$models): ?><p class="table-caption">Belum ada model.</p><?php else: ?>
    <div class="table-wrap"><table class="table table--cards">
      <thead><tr><th>Model</th><th class="num">Node</th><th class="actions">Cetak</th></tr></thead>
      <tbody><?php foreach ($models as $model): ?>
        <tr><td class="cell-title"><?= e($model['nama']) ?> <?= $model['aktif'] ? '<span class="badge badge--aktif">Aktif</span>' : '' ?></td><td data-label="Node" class="num"><?= (int) $model['jumlah_node'] ?></td><td class="actions"><a class="link-btn" href="<?= e(admin_url('laporan/cetak?jenis=model&id=' . $model['id'])) ?>"><?= icon('printer') ?> Cetak</a></td></tr>
      <?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>
  </section>
  <section class="card" aria-labelledby="evaluasi-title">
    <div class="card__head"><h2 id="evaluasi-title">Pengujian model</h2></div>
    <?php if (!$evaluations): ?><p class="table-caption">Belum ada pengujian.</p><?php else: ?>
    <div class="table-wrap"><table class="table table--cards">
      <thead><tr><th>Model</th><th class="num">Akurasi</th><th class="actions">Cetak</th></tr></thead>
      <tbody><?php foreach ($evaluations as $evaluation): ?>
        <tr><td class="cell-title"><?= e($evaluation['model_nama'] . ' · ' . $evaluation['k'] . '-fold') ?></td><td data-label="Akurasi" class="num"><?= e(pct($evaluation['akurasi'])) ?></td><td class="actions"><a class="link-btn" href="<?= e(admin_url('laporan/cetak?jenis=evaluasi&id=' . $evaluation['id'])) ?>"><?= icon('printer') ?> Cetak</a></td></tr>
      <?php endforeach; ?></tbody>
    </table></div>
    <?php endif; ?>
  </section>
</div>
<?php endif; ?>
