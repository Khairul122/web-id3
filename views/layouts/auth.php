<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#071d31">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($heading) ?> | SIMINAT</title>
  <link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>" type="image/svg+xml">
  <link rel="preload" href="<?= e(url('assets/img/auth/bimbingan-karier-ai.webp')) ?>" as="image" fetchpriority="high">
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
  <aside class="auth-story" aria-label="SIMINAT untuk pendampingan siswa">
    <?= brand() ?>
    <div class="auth-story__copy">
      <h2><span>Mendampingi langkah,</span><em><span>merencanakan</span><span>masa depan.</span></em></h2>
      <p>Ruang kerja untuk guru BK dan administrator sekolah.</p>
      <ul class="auth-story__signals" aria-label="Fokus SIMINAT">
        <li><strong>01</strong><span>Kenali minat</span></li>
        <li><strong>02</strong><span>Baca jalur</span></li>
        <li><strong>03</strong><span>Diskusikan pilihan</span></li>
      </ul>
    </div>
    <div class="auth-story__stamp"><span>SIMINAT</span><strong>RUANG KERJA</strong></div>
  </aside>
  <section class="auth-content" aria-labelledby="auth-title">
    <nav class="auth-content__nav" aria-label="Navigasi halaman masuk"><a class="auth-back" href="<?= e(url()) ?>"><?= icon('back') ?> Kembali ke Beranda</a></nav>
    <div class="auth-content__main" id="form-area" tabindex="-1">
      <div class="auth-form-wrap">
        <p class="auth-eyebrow"><?= $user ? 'Ruang Pengelola' : 'Akses Pengelola' ?></p>
        <h1 id="auth-title"><?= e($heading) ?></h1>
        <p class="auth-intro"><?= e($intro) ?></p>
        <?php require $contentView; ?>
      </div>
    </div>
    <footer class="auth-footer">Akses khusus administrator dan guru BK.</footer>
  </section>
</main>
<?php if ($view === 'login'): ?>
<dialog class="auth-help" id="login-help" aria-labelledby="help-title">
  <div class="auth-help__header"><span>SIMINAT</span><button class="icon-button" type="button" data-help-close aria-label="Tutup bantuan"><?= icon('close') ?></button></div>
  <h2 id="help-title">Butuh bantuan masuk?</h2>
  <p>Gunakan akun yang diberikan oleh administrator sekolah. Jika Anda lupa username atau kata sandi, hubungi administrator sekolah untuk memulihkan akses.</p>
  <p>Pendaftaran akun mandiri belum tersedia.</p>
  <button class="auth-submit" type="button" data-help-close>Kembali ke form masuk</button>
</dialog>
<?php endif; ?>
<?php require APP_ROOT . '/views/partials/dialogs.php'; ?>
</body>
</html>
