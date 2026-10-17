<div class="page-head">
  <div>
    <h1>Data Siswa</h1>
    <p>Siswa kelas XII beserta label minat jurusan. Siswa berlabel menjadi data latih, siswa tanpa label menjadi data prediksi.</p>
  </div>
  <?php if ($isAdmin): ?>
  <div class="page-actions">
    <a class="btn btn--outline" href="<?= e(admin_url('siswa/import')) ?>"><?= icon('upload') ?> Import Excel</a>
    <a class="btn" href="<?= e(admin_url('siswa/form')) ?>"><?= icon('plus') ?> Tambah Siswa</a>
  </div>
  <?php endif; ?>
</div>

<form class="toolbar" method="get" role="search" aria-label="Filter data siswa">
  <label class="sr-only" for="q">Cari</label>
  <input id="q" name="q" type="search" placeholder="Cari nama, kode, atau NISN" value="<?= e($filter['q']) ?>">
  <label class="sr-only" for="sekolah_id">Sekolah</label>
  <select id="sekolah_id" name="sekolah_id">
    <option value="">Semua sekolah</option>
    <?php foreach ($schools as $school): ?><option value="<?= (int) $school['id'] ?>" <?= $filter['sekolah_id'] === (int) $school['id'] ? 'selected' : '' ?>><?= e($school['kode']) ?></option><?php endforeach; ?>
  </select>
  <label class="sr-only" for="kelas">Kelas</label>
  <select id="kelas" name="kelas">
    <option value="">Semua kelas</option>
    <?php foreach ($classes as $kelas): ?><option <?= $filter['kelas'] === $kelas ? 'selected' : '' ?>><?= e($kelas) ?></option><?php endforeach; ?>
  </select>
  <label class="sr-only" for="label">Label</label>
  <select id="label" name="label">
    <?php foreach ($labelOptions as $value => $text): ?><option value="<?= e($value) ?>" <?= $filter['label'] === $value ? 'selected' : '' ?>><?= e($text) ?></option><?php endforeach; ?>
  </select>
  <div class="toolbar__actions">
    <button class="btn" type="submit"><?= icon('search') ?> Cari</button>
    <a class="btn btn--outline" href="<?= e(admin_url('siswa/')) ?>">Reset</a>
  </div>
</form>

<?php if (!$result['rows']): ?>
  <div class="card empty"><?= icon('users') ?><strong>Data tidak ditemukan</strong>Ubah kata kunci atau filter, atau import data siswa.</div>
<?php else: ?>
<div class="table-wrap">
  <table class="table table--cards">
    <thead><tr><th>Kode</th><th>Nama</th><th>Sekolah</th><th>Kelas</th><th>Peminatan</th><th>Label</th><th class="actions">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($result['rows'] as $row): ?>
      <tr>
        <td data-label="Kode" class="nowrap"><?= e($row['kode_siswa']) ?></td>
        <td class="cell-title"><a href="<?= e(admin_url('siswa/detail?id=' . $row['id'])) ?>"><?= e($row['nama']) ?></a></td>
        <td data-label="Sekolah"><?= e($row['sekolah_kode']) ?></td>
        <td data-label="Kelas"><?= e($row['kelas']) ?></td>
        <td data-label="Peminatan"><?= e($row['peminatan']) ?></td>
        <td data-label="Label"><?= badge($row['label']) ?></td>
        <td class="actions">
          <a class="link-btn" href="<?= e(admin_url('siswa/detail?id=' . $row['id'])) ?>">Detail</a>
          <?php if ($isAdmin): ?>
            <a class="link-btn" href="<?= e(admin_url('siswa/form?id=' . $row['id'])) ?>">Ubah</a>
            <form class="inline-form" method="post" action="<?= e(admin_url('siswa/hapus')) ?>" data-confirm="Data <?= e($row['nama']) ?> beserta nilai dan atributnya akan dihapus permanen." data-confirm-title="Hapus siswa?" data-confirm-ok="Ya, hapus" data-confirm-danger>
              <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
              <button class="link-btn link-btn--danger" type="submit">Hapus</button>
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
