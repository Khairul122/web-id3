<div class="page-head">
  <div><h1>Akun Saya</h1><p>Informasi akun yang sedang dipakai.</p></div>
  <div class="page-actions"><a class="btn" href="<?= e(admin_url('akun/password')) ?>"><?= icon('edit') ?> Ganti Kata Sandi</a></div>
</div>
<section class="card">
  <dl class="dl">
    <dt>Nama</dt><dd><?= e($user['nama']) ?></dd>
    <dt>Username</dt><dd><?= e($user['username']) ?></dd>
    <dt>Peran</dt><dd><?= e(PenggunaService::ROLES[$user['role']]) ?></dd>
    <dt>Login terakhir</dt><dd><?= e(wib($user['last_login'])) ?></dd>
    <dt>Batas sesi</dt><dd>Keluar otomatis setelah 30 menit tidak aktif atau 8 jam sejak masuk.</dd>
  </dl>
</section>
