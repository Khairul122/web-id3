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
  <div class="field" data-kode-siswa>
    <label for="kode_siswa">Kode siswa</label>
    <input id="kode_siswa" name="kode_siswa" value="<?= e($form['kode_siswa'] ?? '') ?>" maxlength="20" <?= invalid_attr($errors, 'kode_siswa') ?>>
    <p class="field__help">Isi bila peran Siswa. Kode harus ada di Data Siswa.</p>
    <?= field_error($errors, 'kode_siswa') ?>
  </div>
  <div class="field" data-sekolah-guru>
    <label for="sekolah_id">Sekolah guru BK</label>
    <select id="sekolah_id" name="sekolah_id" <?= invalid_attr($errors, 'sekolah_id') ?>>
      <option value="">Pilih sekolah</option>
      <?php foreach ($schools as $school): ?><option value="<?= (int) $school['id'] ?>" <?= $form['sekolah_id'] === (string) $school['id'] ? 'selected' : '' ?>><?= e($school['nama']) ?></option><?php endforeach; ?>
    </select>
    <?= field_error($errors, 'sekolah_id') ?>
  </div>
  <div class="field">
    <label for="password">Kata sandi<?= $existing ? ' baru (opsional)' : '' ?></label>
    <input id="password" name="password" type="password" autocomplete="new-password" maxlength="72" <?= $existing ? '' : 'required' ?> <?= invalid_attr($errors, 'password') ?>>
    <p class="field__help">8 sampai 72 byte.</p>
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
