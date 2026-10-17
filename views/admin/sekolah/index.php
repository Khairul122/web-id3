<div class="page-head">
  <div>
    <h1>Data Sekolah</h1>
    <p>Madrasah asal data penelitian. Kode sekolah dipakai sebagai nilai atribut Sekolah_Asal sehingga tidak dapat diubah.</p>
  </div>
</div>
<div class="table-wrap">
  <table class="table table--cards">
    <thead><tr><th>Kode</th><th>Nama sekolah</th><th>Alamat</th><th class="num">Siswa</th><th class="num">Berlabel</th><th class="actions">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($schools as $school): ?>
      <tr>
        <td data-label="Kode"><span class="badge badge--gold"><?= e($school['kode']) ?></span></td>
        <td class="cell-title"><?= e($school['nama']) ?></td>
        <td data-label="Alamat"><?= e($school['alamat'] ?? '-') ?></td>
        <td data-label="Siswa" class="num"><?= (int) $school['jumlah_siswa'] ?></td>
        <td data-label="Berlabel" class="num"><?= (int) $school['jumlah_berlabel'] ?></td>
        <td class="actions">
          <a class="link-btn" href="<?= e(admin_url('sekolah/detail?id=' . $school['id'])) ?>"><?= icon('eye') ?> Detail</a>
          <?php if ($isAdmin): ?><a class="link-btn" href="<?= e(admin_url('sekolah/form?id=' . $school['id'])) ?>"><?= icon('edit') ?> Ubah</a><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
