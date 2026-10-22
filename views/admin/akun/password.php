<div class="page-head"><div><h1>Ganti Kata Sandi</h1><p>Setelah diganti, sesi akun ini di perangkat lain otomatis berakhir.</p></div></div>
<form class="card form-grid" method="post" novalidate data-busy data-confirm="Kata sandi akun Anda akan diganti." data-confirm-title="Ganti kata sandi?" data-confirm-ok="Ya, ganti">
  <?= Csrf::field() ?>
  <?php foreach (['current_password' => ['Kata sandi saat ini', 'current-password', ''], 'password' => ['Kata sandi baru', 'new-password', '8 sampai 72 byte, berbeda dari kata sandi saat ini.'], 'password_confirmation' => ['Konfirmasi kata sandi baru', 'new-password', '']] as $key => [$label, $autocomplete, $help]): ?>
    <div class="field">
      <label for="<?= e($key) ?>"><?= e($label) ?></label>
      <input id="<?= e($key) ?>" name="<?= e($key) ?>" type="password" autocomplete="<?= e($autocomplete) ?>" maxlength="72" required <?= invalid_attr($errors, $key) ?>>
      <?php if ($help): ?><p class="field__help"><?= e($help) ?></p><?php endif; ?>
      <?= field_error($errors, $key) ?>
    </div>
  <?php endforeach; ?>
  <div class="form-actions">
    <button class="btn" type="submit"><?= icon('check') ?> Simpan Kata Sandi</button>
    <a class="btn btn--outline" href="<?= e(admin_url('akun/')) ?>">Batal</a>
  </div>
</form>
