<div class="page-head"><div><h1>Uji Model</h1><p>Data skenario model dibagi menjadi k bagian dengan proporsi kelas sama. Setiap bagian bergiliran menjadi data uji, sisanya untuk melatih pohon baru dengan konfigurasi yang sama.</p></div></div>
<?php if (!$models): ?>
  <div class="card empty"><?= icon('branch') ?><strong>Belum ada model</strong>Latih model terlebih dahulu.<br><a class="btn" href="<?= e(admin_url('training/form')) ?>"><?= icon('play') ?> Latih Model</a></div>
<?php else: ?>
<form class="card form-grid form-grid--2" method="post" novalidate data-busy data-confirm="Model akan diuji dengan k-fold cross validation. Hasil disimpan ke riwayat pengujian." data-confirm-title="Jalankan pengujian?" data-confirm-ok="Ya, uji">
  <?= Csrf::field() ?>
  <div class="field">
    <label for="model_id">Model</label>
    <select id="model_id" name="model_id" required <?= invalid_attr($errors, 'model_id') ?>>
      <?php foreach ($models as $model): ?><option value="<?= (int) $model['id'] ?>" <?= $form['model_id'] === (string) $model['id'] ? 'selected' : '' ?>><?= e($model['nama'] . ' · ' . (ModelId3::SCENARIOS[$model['skenario']] ?? '') . ($model['aktif'] ? ' (aktif)' : '')) ?></option><?php endforeach; ?>
    </select>
    <?= field_error($errors, 'model_id') ?>
  </div>
  <div class="field">
    <label for="k">Jumlah fold (k)</label>
    <input id="k" name="k" type="number" min="3" max="10" required value="<?= e($form['k']) ?>" <?= invalid_attr($errors, 'k') ?>>
    <p class="field__help">Default 5. Data latih 147 siswa sehingga k-fold lebih stabil daripada satu kali pembagian.</p>
    <?= field_error($errors, 'k') ?>
  </div>
  <div class="form-actions span-2">
    <button class="btn btn--accent" type="submit"><?= icon('play') ?> Jalankan Pengujian</button>
    <a class="btn btn--outline" href="<?= e(admin_url('pengujian/')) ?>">Batal</a>
  </div>
</form>
<?php endif; ?>
