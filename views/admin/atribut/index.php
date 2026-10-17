<div class="page-head">
  <div>
    <h1>Atribut ID3</h1>
    <p>Atribut yang dipakai untuk membentuk pohon keputusan. Bila gain sama, atribut dengan urutan lebih kecil dipilih. Atribut nonaktif tidak ditawarkan saat training.</p>
  </div>
</div>
<div class="table-wrap">
  <table class="table table--cards">
    <thead><tr><th class="num">Urutan</th><th>Kode</th><th>Nama</th><th>Nilai yang mungkin</th><th>Status</th><th class="actions">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($attributes as $attribute): ?>
      <tr>
        <td data-label="Urutan" class="num"><?= (int) $attribute['urutan'] ?></td>
        <td data-label="Kode" class="nowrap"><code><?= e($attribute['kode']) ?></code></td>
        <td class="cell-title"><a href="<?= e(admin_url('atribut/detail?id=' . $attribute['id'])) ?>"><?= e($attribute['nama']) ?></a></td>
        <td data-label="Nilai"><?= e($attribute['domain']) ?></td>
        <td data-label="Status"><span class="badge badge--<?= $attribute['aktif'] ? 'aktif' : 'nonaktif' ?>"><?= $attribute['aktif'] ? 'Aktif' : 'Nonaktif' ?></span></td>
        <td class="actions">
          <a class="link-btn" href="<?= e(admin_url('atribut/detail?id=' . $attribute['id'])) ?>">Detail</a>
          <?php if ($isAdmin): ?><a class="link-btn" href="<?= e(admin_url('atribut/form?id=' . $attribute['id'])) ?>">Ubah</a><?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
