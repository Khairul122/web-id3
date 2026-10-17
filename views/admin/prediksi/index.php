<div class="page-head">
  <div>
    <h1>Prediksi</h1>
    <p>Riwayat hasil Cek Rekomendasi dan prediksi massal. <?= $model ? 'Model aktif: ' . e($model['nama']) . '.' : 'Belum ada model aktif.' ?></p>
  </div>
  <?php if ($model): ?>
  <div class="page-actions">
    <?php if ($isAdmin): ?><a class="btn btn--outline" href="<?= e(admin_url('prediksi/massal')) ?>"><?= icon('users') ?> Prediksi Massal</a><?php endif; ?>
    <a class="btn btn--accent" href="<?= e(admin_url('prediksi/form')) ?>"><?= icon('target') ?> Cek Rekomendasi</a>
  </div>
  <?php endif; ?>
</div>

<form class="toolbar" method="get" aria-label="Filter prediksi">
  <label class="sr-only" for="sumber">Sumber</label>
  <select id="sumber" name="sumber">
    <?php foreach (['' => 'Semua sumber', 'form' => 'Form Cek Rekomendasi', 'massal' => 'Prediksi massal'] as $value => $text): ?><option value="<?= e($value) ?>" <?= $filter['sumber'] === $value ? 'selected' : '' ?>><?= e($text) ?></option><?php endforeach; ?>
  </select>
  <label class="sr-only" for="hasil">Hasil</label>
  <select id="hasil" name="hasil">
    <option value="">Semua hasil</option>
    <?php foreach (Id3::CLASSES as $class): ?><option <?= $filter['hasil'] === $class ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?>
  </select>
  <div class="toolbar__actions">
    <button class="btn" type="submit"><?= icon('search') ?> Tampilkan</button>
    <a class="btn btn--outline" href="<?= e(admin_url('prediksi/')) ?>">Reset</a>
  </div>
</form>

<?php if (!$result['rows']): ?>
  <div class="card empty"><?= icon('target') ?><strong>Belum ada prediksi</strong>Jalankan Cek Rekomendasi atau prediksi massal.</div>
<?php else: ?>
<div class="table-wrap">
  <table class="table table--cards">
    <thead><tr><th>Nama</th><th>Sumber</th><th>Model</th><th>Hasil</th><th class="num">Keyakinan</th><th>Waktu</th><th class="actions">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($result['rows'] as $row): ?>
      <tr>
        <td class="cell-title"><a href="<?= e(admin_url('prediksi/detail?id=' . $row['id'])) ?>"><?= e($row['nama'] ?? 'Tanpa nama') ?></a><?php if ($row['kode_siswa']): ?> <small><?= e($row['kode_siswa']) ?></small><?php endif; ?></td>
        <td data-label="Sumber"><?= $row['sumber'] === 'massal' ? 'Massal' : 'Form' ?></td>
        <td data-label="Model"><?= e($row['model_nama']) ?></td>
        <td data-label="Hasil"><?= badge($row['hasil']) ?></td>
        <td data-label="Keyakinan" class="num"><?= e(pct($row['keyakinan'])) ?></td>
        <td data-label="Waktu" class="nowrap"><?= e(wib($row['created_at'])) ?></td>
        <td class="actions">
          <a class="link-btn" href="<?= e(admin_url('prediksi/detail?id=' . $row['id'])) ?>">Detail</a>
          <?php if ($isAdmin): ?>
          <form class="inline-form" method="post" action="<?= e(admin_url('prediksi/hapus')) ?>" data-confirm="Hasil prediksi ini akan dihapus dari riwayat." data-confirm-title="Hapus prediksi?" data-confirm-ok="Ya, hapus" data-confirm-danger>
            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="link-btn link-btn--danger" type="submit">Hapus</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
<?php
$total = $result['total'];
$params = array_filter($filter);
require APP_ROOT . '/views/partials/pagination.php';
