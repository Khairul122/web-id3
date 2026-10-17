<?php if ($notice !== '' || $errors): ?>
  <div class="auth-alert" role="alert" tabindex="-1" data-form-alert>
    <?= e($notice !== '' ? $notice : 'Periksa kembali isian form Anda.') ?>
  </div>
<?php endif; ?>
<form class="auth-form" method="post" action="<?= e(url('login.php')) ?>" novalidate data-auth-form>
  <?= Csrf::field() ?>
  <div class="auth-form__progress" aria-hidden="true"><span></span></div>
  <div class="auth-form__meta"><span>AKSES TERLINDUNGI</span><span>SIMINAT / 01</span></div>
  <div class="form-field">
    <label for="username">Username</label>
    <input type="text" id="username" name="username" autocomplete="username" autocapitalize="none" spellcheck="false" maxlength="100" value="<?= e($username) ?>" placeholder="Masukkan username Anda" required <?= isset($errors['username']) ? 'aria-invalid="true" aria-describedby="username-error"' : '' ?>>
    <?php if (isset($errors['username'])): ?><p class="field-error" id="username-error"><?= e($errors['username']) ?></p><?php endif; ?>
  </div>
  <div class="form-field">
    <label for="password">Kata sandi</label>
    <div class="password-field">
      <input type="password" id="password" name="password" autocomplete="current-password" maxlength="72" placeholder="Masukkan kata sandi Anda" required <?= isset($errors['password']) ? 'aria-invalid="true" aria-describedby="password-error"' : '' ?>>
      <button class="password-toggle" type="button" data-password-toggle="password" aria-label="Tampilkan kata sandi" aria-pressed="false" hidden><?= icon('eye') ?></button>
    </div>
    <?php if (isset($errors['password'])): ?><p class="field-error" id="password-error"><?= e($errors['password']) ?></p><?php endif; ?>
  </div>
  <button class="auth-submit" type="submit" data-submit>Masuk ke SIMINAT <?= icon('arrow') ?></button>
  <p class="auth-status" role="status" data-submit-status></p>
</form>
<button class="auth-help-trigger" type="button" data-help-open hidden>Butuh bantuan masuk?</button>
<noscript><p class="auth-small-note">Jika lupa akses akun, hubungi administrator sekolah.</p></noscript>
