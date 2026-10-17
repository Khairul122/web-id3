<?php if ($errors): ?><div class="auth-alert" role="alert" tabindex="-1" data-form-alert>Periksa kembali isian kata sandi Anda.</div><?php endif; ?>
<form class="auth-form" method="post" action="<?= e(url('admin/?page=password')) ?>" novalidate data-auth-form>
  <?= Csrf::field() ?>
  <?php foreach (['current_password' => 'Kata sandi saat ini', 'password' => 'Kata sandi baru', 'password_confirmation' => 'Konfirmasi kata sandi baru'] as $field => $label): ?>
    <div class="form-field">
      <label for="<?= e($field) ?>"><?= e($label) ?></label>
      <div class="password-field">
        <input type="password" id="<?= e($field) ?>" name="<?= e($field) ?>" autocomplete="<?= $field === 'current_password' ? 'current-password' : 'new-password' ?>" maxlength="72" required aria-describedby="<?= e($field) ?>-help<?= isset($errors[$field]) ? ' ' . e($field) . '-error' : '' ?>" <?= isset($errors[$field]) ? 'aria-invalid="true"' : '' ?>>
        <button class="password-toggle" type="button" data-password-toggle="<?= e($field) ?>" aria-label="Tampilkan <?= e(strtolower($label)) ?>" aria-pressed="false" hidden><?= icon('eye') ?></button>
      </div>
      <p id="<?= e($field) ?>-help" class="field-help"><?= $field === 'password' ? 'Gunakan minimal 12 karakter ASCII, maksimal 72 byte.' : ($field === 'current_password' ? 'Masukkan kata sandi yang digunakan untuk masuk.' : 'Ketik ulang kata sandi baru yang sama.') ?></p>
      <?php if (isset($errors[$field])): ?><p class="field-error" id="<?= e($field) ?>-error"><?= e($errors[$field]) ?></p><?php endif; ?>
    </div>
  <?php endforeach; ?>
  <button class="auth-submit" type="submit" data-submit>Simpan Kata Sandi <?= icon('arrow') ?></button>
  <p class="auth-status" role="status" data-submit-status></p>
</form>
<form method="post" action="<?= e(url('logout.php')) ?>" class="auth-secondary-form"><?= Csrf::field() ?><button class="auth-help-trigger" type="submit">Keluar dari akun</button></form>
