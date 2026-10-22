<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#071d31">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($heading) ?> | SIMINAT</title>
  <link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>" type="image/svg+xml">
  <link rel="stylesheet" href="<?= e(url('assets/css/tokens.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('assets/css/hero.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('assets/css/auth.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('assets/css/dialog.css')) ?>">
  <script src="<?= e(url('assets/js/auth.js')) ?>" defer></script>
  <script src="<?= e(url('assets/js/dialog.js')) ?>" defer></script>
</head>
<body class="auth-page">
<a class="skip-link" href="#form-area">Lewati ke konten</a>
<main class="auth-shell">
  <aside class="auth-story" aria-label="Tentang SIMINAT">
    <div class="auth-story__top">
      <?= brand() ?>
      <span class="auth-story__edition">Portal sekolah</span>
    </div>
    <div class="auth-story__main">
      <p class="auth-story__label">Ruang belajar · ruang bertumbuh</p>
      <h2>Masa depan<br>dimulai <em>di sini.</em></h2>
      <p>Kenali minat, baca rekomendasi, dan lanjutkan percakapan bersama guru BK.</p>
    </div>
    <?php if ($view === 'login'): ?>
    <div class="auth-roles" aria-label="Panduan akses per peran">
      <p class="auth-roles__label">Kenali fitur tiap peran</p>
      <div class="auth-roles__options">
        <button type="button" data-role-copy="Lihat rekomendasi minat dan riwayat hasil milik Anda." aria-pressed="true">Siswa</button>
        <button type="button" data-role-copy="Dampingi siswa dan tinjau hasil rekomendasi untuk bimbingan." aria-pressed="false">Guru BK</button>
        <button type="button" data-role-copy="Kelola akun, data, dan model rekomendasi sekolah." aria-pressed="false">Admin</button>
      </div>
      <p class="auth-roles__description" data-role-description>Lihat rekomendasi minat dan riwayat hasil milik Anda.</p>
    </div>
    <?php endif; ?>
  </aside>
  <section class="auth-content" aria-labelledby="auth-title">
    <header class="auth-header">
      <span class="auth-clock" data-auth-clock>Waktu Indonesia Barat</span>
      <nav aria-label="Navigasi halaman masuk"><a class="auth-back" href="<?= e(url()) ?>"><?= icon('back') ?> Beranda</a></nav>
    </header>
    <div class="auth-content__main" id="form-area" tabindex="-1">
      <div class="auth-form-wrap">
        <p class="auth-eyebrow">Masuk ke akun SIMINAT</p>
        <h1 id="auth-title"><?= e($heading) ?></h1>
        <p class="auth-intro"><?= e($intro) ?></p>
        <?php require $contentView; ?>
        <footer class="auth-footer">Akun disediakan oleh sekolah untuk siswa, guru BK, dan administrator.</footer>
      </div>
    </div>
  </section>
</main>
<?php if ($view === 'login'): ?>
<dialog class="auth-help" id="login-help" aria-labelledby="help-title">
  <div class="auth-help__header"><span>SIMINAT</span><button class="icon-button" type="button" data-help-close aria-label="Tutup bantuan"><?= icon('close') ?></button></div>
  <h2 id="help-title">Butuh bantuan masuk?</h2>
  <p>Gunakan akun yang diberikan oleh sekolah. Jika lupa username atau kata sandi, siswa dapat menghubungi guru BK. Guru BK dan administrator dapat menghubungi administrator sekolah.</p>
  <p>Pendaftaran akun mandiri belum tersedia.</p>
  <button class="auth-submit" type="button" data-help-close>Kembali ke form masuk</button>
</dialog>
<?php endif; ?>
<?php require APP_ROOT . '/views/partials/dialogs.php'; ?>
</body>
</html>
