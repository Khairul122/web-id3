<div class="page-head">
  <div><h1><?= e($attribute['nama']) ?></h1><p><?= e($attribute['deskripsi'] ?? '') ?></p></div>
  <?php if ($isAdmin): ?><div class="page-actions"><a class="btn" href="<?= e(admin_url('atribut/form?id=' . $attribute['id'])) ?>"><?= icon('edit') ?> Ubah</a></div><?php endif; ?>
</div>
<div class="grid grid--sidebar">
  <section class="card" aria-labelledby="dist-title">
    <div class="card__head"><h2 id="dist-title">Sebaran nilai terhadap label</h2><p>Jumlah siswa per nilai atribut. Sebaran yang condong ke satu label membuat gain atribut tinggi.</p></div>
    <div class="table-wrap"><table class="table table--cards">
      <thead><tr><th>Nilai</th><th class="num">Saintek</th><th class="num">Soshum</th><th class="num">Campuran</th><th class="num">Tanpa label</th><th class="num">Total</th></tr></thead>
      <tbody>
      <?php foreach ($distribution as $row): ?>
        <tr>
          <td class="cell-title"><?= badge($row['nilai']) ?></td>
          <td data-label="Saintek" class="num"><?= (int) $row['saintek'] ?></td>
          <td data-label="Soshum" class="num"><?= (int) $row['soshum'] ?></td>
          <td data-label="Campuran" class="num"><?= (int) $row['campuran'] ?></td>
          <td data-label="Tanpa label" class="num"><?= (int) $row['tanpa_label'] ?></td>
          <td data-label="Total" class="num"><?= (int) $row['total'] ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
  <section class="card" aria-labelledby="info-title">
    <div class="card__head"><h2 id="info-title">Informasi</h2></div>
    <dl class="dl">
      <dt>Kode</dt><dd><code><?= e($attribute['kode']) ?></code></dd>
      <dt>Urutan</dt><dd><?= (int) $attribute['urutan'] ?></dd>
      <dt>Status</dt><dd><span class="badge badge--<?= $attribute['aktif'] ? 'aktif' : 'nonaktif' ?>"><?= $attribute['aktif'] ? 'Aktif' : 'Nonaktif' ?></span></dd>
      <dt>Sumber nilai</dt><dd><?= isset(Discretizer::SOURCES[$attribute['kode']]) ? e(Discretizer::SCORE_LABELS[Discretizer::SOURCES[$attribute['kode']]]) . ', didiskretisasi' : 'Kategori langsung' ?></dd>
      <dt>Gain di akar</dt><dd><?= $rootGain === null ? 'Tidak dipakai model aktif' : e(num($rootGain, 4)) ?></dd>
    </dl>
  </section>
</div>
