<div class="page-head"><div><h1><?= e($pageTitle) ?></h1><p><?= $existing ? 'Kosongkan kata sandi bila tidak ingin mengubahnya. Mengubah kata sandi mengakhiri sesi akun tersebut di perangkat lain.' : 'Berikan username dan kata sandi awal kepada pemilik akun.' ?></p></div></div>
<form class="card form-grid form-grid--2" method="post" novalidate data-busy>
  <?= Csrf::field() ?>
  <?= text_field($form, $errors, 'nama', 'Nama lengkap', 100, 'required autocomplete="off"') ?>
  <?= text_field($form, $errors, 'username', 'Username', 100, 'required autocomplete="off" autocapitalize="none" spellcheck="false"') ?>
  <div class="field">
    <label for="role">Peran</label>
    <select id="role" name="role" required <?= invalid_attr($errors, 'role') ?>>
      <?php foreach (PenggunaService::ROLES as $value => $label): ?><option value="<?= e($value) ?>" <?= $form['role'] === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
    </select>
    <?= field_error($errors, 'role') ?>
  </div>
  <div></div>
  <div class="field">
    <label for="password">Kata sandi<?= $existing ? ' baru (opsional)' : '' ?></label>
    <input id="password" name="password" type="password" autocomplete="new-password" maxlength="72" <?= $existing ? '' : 'required' ?> <?= invalid_attr($errors, 'password') ?>>
    <p class="field__help">12 sampai 72 karakter.</p>
    <?= field_error($errors, 'password') ?>
  </div>
  <div class="field">
    <label for="password_confirmation">Konfirmasi kata sandi</label>
    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" maxlength="72">
  </div>
  <div class="form-actions span-2">
    <button class="btn" type="submit"><?= icon('check') ?> Simpan</button>
    <a class="btn btn--outline" href="<?= e(admin_url('pengguna/')) ?>">Batal</a>
  </div>
</form>
