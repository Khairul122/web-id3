<div class="page-head"><div><h1>Tinjau Data Siswa</h1><p>Langkah 2 dari 2: periksa data dan kategori atribut ID3 sebelum disimpan.</p></div></div>
<div class="grid grid--2">
  <section class="card" aria-labelledby="identitas-title">
    <div class="card__head"><h2 id="identitas-title">Identitas</h2></div>
    <dl class="dl">
      <dt>Kode siswa</dt><dd><?= e($record['kode_siswa']) ?></dd>
      <dt>Nama</dt><dd><?= e($record['nama']) ?></dd>
      <dt>Sekolah</dt><dd><?= e($school['nama']) ?></dd>
      <dt>Kelas</dt><dd><?= e($record['kelas']) ?></dd>
      <dt>NIS / NISN</dt><dd><?= e(($record['nis'] ?: '-') . ' / ' . ($record['nisn'] ?: '-')) ?></dd>
      <dt>Jenis kelamin</dt><dd><?= e(['L' => 'Laki-laki', 'P' => 'Perempuan'][$record['jk']] ?? '-') ?></dd>
      <dt>Label</dt><dd><?= badge($record['label'] ?: null) ?></dd>
      <dt>Sumber label</dt><dd><?= e($record['sumber_label'] ?: '-') ?></dd>
      <dt>Detail label</dt><dd><?= e($record['detail_label'] ?: '-') ?></dd>
      <dt>Jurusan diterima</dt><dd><?= e($record['jurusan_diterima'] ?: '-') ?></dd>
    </dl>
  </section>
  <section class="card" aria-labelledby="atribut-title">
    <div class="card__head"><h2 id="atribut-title">Atribut ID3</h2><p>Kategori dari nilai memakai aturan diskretisasi aktif.</p></div>
    <div class="table-wrap"><table class="table">
      <thead><tr><th>Atribut</th><th class="num">Nilai</th><th>Kategori</th></tr></thead>
      <tbody>
      <?php foreach ($attributes as $attribute): $column = Discretizer::SOURCES[$attribute['kode']] ?? null; ?>
        <tr>
          <td><?= e($attribute['nama']) ?></td>
          <td class="num"><?= $column ? e(num($record['nilai'][$column])) : '-' ?></td>
          <td><?= badge($record['atribut'][$attribute['kode']]) ?><?php if ($column && $record['nilai'][$column] === null): ?> <small>(manual)</small><?php endif; ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table></div>
  </section>
</div>
<form class="card form-actions" method="post" data-busy data-confirm="Data <?= e($record['nama']) ?> akan <?= $id ? 'diperbarui' : 'ditambahkan' ?>." data-confirm-title="Simpan data siswa?" data-confirm-ok="Ya, simpan">
  <?= Csrf::field() ?>
  <button class="btn" type="submit"><?= icon('check') ?> Simpan Data</button>
  <a class="btn btn--outline" href="<?= e(admin_url($formUrl)) ?>"><?= icon('edit') ?> Ubah Lagi</a>
</form>
