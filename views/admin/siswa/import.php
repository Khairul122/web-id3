<div class="page-head"><div><h1>Import Data Siswa</h1><p>Langkah 1 dari 2: pilih workbook. Data dibaca dan divalidasi dulu, belum disimpan.</p></div></div>
<div class="grid grid--sidebar">
  <form class="card form-grid" method="post" enctype="multipart/form-data" novalidate data-busy>
    <?= Csrf::field() ?>
    <fieldset class="field">
      <legend>Sumber data</legend>
      <div class="check-grid check-grid--list">
        <?php if (is_file($serverFile)): ?>
        <label class="check"><input type="radio" name="sumber" value="server" checked> <span>Dataset_Bersih_ID3.xlsx di server<small>File hasil pembersihan data yang ada di folder proyek.</small></span></label>
        <?php endif; ?>
        <label class="check"><input type="radio" name="sumber" value="upload" <?= is_file($serverFile) ? '' : 'checked' ?>> <span>Unggah file baru<small>Format sama dengan Dataset_Bersih_ID3.xlsx, maksimal 2 MB.</small></span></label>
      </div>
    </fieldset>
    <label class="file-drop" for="berkas">
      <?= icon('upload') ?><br><strong>Pilih file .xlsx</strong>
      <input id="berkas" name="berkas" type="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
    </label>
    <div class="form-actions">
      <button class="btn" type="submit">Baca dan Validasi <?= icon('arrow') ?></button>
      <a class="btn btn--outline" href="<?= e(admin_url('siswa/')) ?>">Batal</a>
    </div>
  </form>
  <section class="card" aria-labelledby="aturan-title">
    <div class="card__head"><h2 id="aturan-title">Aturan import</h2></div>
    <ul class="table-caption">
      <li>Sheet <strong>Data_Numerik</strong>: siswa berlabel beserta nilai rapor. Kategori dihitung dari aturan diskretisasi aktif.</li>
      <li>Sheet <strong>Data_Tanpa_Label</strong>: siswa tanpa label, memakai kategori yang tertulis di sheet.</li>
      <li>Hasil wajib <?= Importer::EXPECTED['total'] ?> siswa: <?= Importer::EXPECTED['berlabel'] ?> berlabel dan <?= Importer::EXPECTED['tanpa_label'] ?> tanpa label. Bila berbeda, import dihentikan.</li>
      <li>Import mengganti seluruh data siswa. Riwayat prediksi tetap ada tetapi terlepas dari siswa.</li>
    </ul>
  </section>
</div>
