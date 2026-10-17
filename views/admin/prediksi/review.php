<div class="page-head"><div><h1>Tinjau Input</h1><p>Langkah 2 dari 2: pastikan kategori atribut sudah benar sebelum prediksi dijalankan.</p></div></div>
<div class="grid grid--sidebar">
  <section class="card" aria-labelledby="atribut-title">
    <div class="card__head"><h2 id="atribut-title">Atribut ID3</h2></div>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Atribut</th><th class="num">Nilai</th><th>Kategori</th></tr></thead>
      <tbody>
      <?php foreach ($attributeList as $attribute): $column = Discretizer::SOURCES[$attribute['kode']] ?? null; ?>
        <tr>
          <td><?= e($attribute['nama']) ?></td>
          <td class="num"><?= $column && $form['nilai'][$column] !== '' ? e(num($form['nilai'][$column])) : '-' ?></td>
          <td><?= badge($attributes[$attribute['kode']]) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
  <section class="card" aria-labelledby="info-title">
    <div class="card__head"><h2 id="info-title">Ringkasan</h2></div>
    <dl class="dl">
      <dt>Nama</dt><dd><?= e($form['nama'] !== '' ? $form['nama'] : 'Tidak diisi') ?></dd>
      <dt>Sekolah</dt><dd><?= e($form['sekolah']) ?></dd>
      <dt>Peminatan</dt><dd><?= e($form['peminatan']) ?></dd>
      <dt>Model</dt><dd><?= e($model['nama']) ?></dd>
    </dl>
  </section>
</div>
<div class="card form-actions">
  <form method="post" data-busy data-confirm="Rekomendasi akan dihitung dengan model aktif dan disimpan ke riwayat." data-confirm-title="Proses rekomendasi?" data-confirm-ok="Ya, proses">
    <?= Csrf::field() ?>
    <button class="btn btn--accent" type="submit"><?= icon('target') ?> Proses Rekomendasi</button>
  </form>
  <a class="btn btn--outline" href="<?= e(admin_url('prediksi/form?draft=1')) ?>"><?= icon('edit') ?> Ubah Lagi</a>
</div>
