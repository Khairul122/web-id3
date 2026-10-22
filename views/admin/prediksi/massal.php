<div class="page-head"><div><h1>Prediksi Massal</h1><p>Memprediksi siswa memakai model aktif <strong><?= e($model['nama']) ?></strong>. Hasil massal sebelumnya dari model yang sama akan diganti.</p></div></div>
<form class="toolbar" method="get" aria-label="Cakupan prediksi massal">
  <label for="cakupan">Cakupan</label>
  <select id="cakupan" name="cakupan">
    <option value="">Siswa tanpa label (sesuai metode)</option>
    <option value="semua" <?= $all ? 'selected' : '' ?>>Semua siswa, termasuk berlabel</option>
  </select>
  <button class="btn" type="submit">Tampilkan</button>
</form>
<?php if ($all): ?>
<div class="notice"><?= icon('alert') ?><div>Siswa berlabel adalah data latih model. Hasil prediksinya dipakai untuk riwayat siswa dan perbandingan dengan label asli, bukan pengganti hasil pengujian k-fold.</div></div>
<?php endif; ?>
<div class="stat-grid">
  <div class="stat"><span class="stat__label"><?= $all ? 'Semua siswa' : 'Siswa tanpa label' ?></span><span class="stat__value"><?= count($students) ?></span><span class="stat__note">Akan diprediksi</span></div>
  <div class="stat"><span class="stat__label">Model</span><span class="stat__value"><?= (int) $model['jumlah_node'] ?></span><span class="stat__note">node pohon</span></div>
  <div class="stat"><span class="stat__label">Skenario model</span><span class="stat__value"><?= (int) $model['jumlah_data'] ?></span><span class="stat__note"><?= e(ModelId3::SCENARIOS[$model['skenario']] ?? '') ?></span></div>
  <div class="stat"><span class="stat__label">Akurasi latih</span><span class="stat__value"><?= e(pct($model['akurasi_latih'])) ?></span><span class="stat__note">Lihat juga hasil pengujian</span></div>
</div>
<?php if (!$students): ?>
  <div class="card empty"><?= icon('users') ?><strong>Tidak ada siswa untuk diprediksi</strong><?= $all ? 'Data siswa masih kosong.' : 'Semua siswa sudah memiliki label.' ?></div>
<?php else: ?>
<section class="card" aria-labelledby="daftar-title">
  <div class="card__head"><h2 id="daftar-title">Daftar siswa (10 pertama)</h2></div>
  <div class="table-wrap"><table class="table table--cards">
    <thead><tr><th>Kode</th><th>Nama</th><th>Peminatan</th><th>Kecenderungan</th><?php if ($all): ?><th>Label</th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach (array_slice($students, 0, 10) as $student): ?>
      <tr><td data-label="Kode"><?= e($student['kode_siswa']) ?></td><td class="cell-title"><?= e($student['nama']) ?></td><td data-label="Peminatan"><?= e($student['atribut']['Peminatan'] ?? '-') ?></td><td data-label="Kecenderungan"><?= e($student['atribut']['Kecenderungan'] ?? '-') ?></td><?php if ($all): ?><td data-label="Label"><?= badge($student['label']) ?></td><?php endif; ?></tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>
<div class="card form-actions">
  <form method="post" data-busy data-confirm="<?= count($students) ?> siswa akan diprediksi dengan model <?= e($model['nama']) ?>." data-confirm-title="Jalankan prediksi massal?" data-confirm-ok="Ya, jalankan">
    <?= Csrf::field() ?><input type="hidden" name="cakupan" value="<?= $all ? 'semua' : '' ?>">
    <button class="btn btn--accent" type="submit"><?= icon('play') ?> Jalankan Prediksi Massal</button>
  </form>
  <a class="btn btn--outline" href="<?= e(admin_url('prediksi/')) ?>">Batal</a>
</div>
<?php endif; ?>
