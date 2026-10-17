<div class="page-head">
  <div><h1><?= e($school['nama']) ?></h1><p><?= e($school['alamat'] ?? 'Alamat belum diisi.') ?></p></div>
  <div class="page-actions">
    <a class="btn btn--outline" href="<?= e(admin_url('siswa/?sekolah_id=' . $school['id'])) ?>"><?= icon('users') ?> Lihat Siswa</a>
    <?php if ($isAdmin): ?><a class="btn" href="<?= e(admin_url('sekolah/form?id=' . $school['id'])) ?>"><?= icon('edit') ?> Ubah</a><?php endif; ?>
  </div>
</div>
<div class="grid grid--sidebar">
  <section class="card" aria-labelledby="kelas-title">
    <div class="card__head"><h2 id="kelas-title">Siswa per kelas</h2></div>
    <?php if (!$classes): ?>
      <div class="empty"><?= icon('users') ?><strong>Belum ada siswa</strong>Import data untuk sekolah ini.</div>
    <?php else: ?>
    <div class="table-wrap"><table class="table table--cards">
      <thead><tr><th>Kelas</th><th class="num">Total</th><th class="num">Saintek</th><th class="num">Soshum</th><th class="num">Campuran</th><th class="num">Tanpa label</th></tr></thead>
      <tbody>
      <?php foreach ($classes as $row): ?>
        <tr>
          <td class="cell-title"><a href="<?= e(admin_url('siswa/?' . http_build_query(['sekolah_id' => $school['id'], 'kelas' => $row['kelas']]))) ?>"><?= e($row['kelas']) ?></a></td>
          <td data-label="Total" class="num"><?= (int) $row['total'] ?></td>
          <td data-label="Saintek" class="num"><?= (int) $row['saintek'] ?></td>
          <td data-label="Soshum" class="num"><?= (int) $row['soshum'] ?></td>
          <td data-label="Campuran" class="num"><?= (int) $row['campuran'] ?></td>
          <td data-label="Tanpa label" class="num"><?= (int) $row['tanpa_label'] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
    <?php endif; ?>
  </section>
  <section class="card" aria-labelledby="info-title">
    <div class="card__head"><h2 id="info-title">Informasi</h2></div>
    <dl class="dl">
      <dt>Kode atribut</dt><dd><?= e($school['kode']) ?></dd>
      <dt>Jumlah kelas</dt><dd><?= count($classes) ?></dd>
      <dt>Jumlah siswa</dt><dd><?= $total ?></dd>
      <dt>Sumber label</dt><dd><?= $school['kode'] === 'MAN' ? 'Mapel pilihan TKA 2025' : 'Prodi diterima SPAN-PTKIN 2026' ?></dd>
    </dl>
  </section>
</div>
