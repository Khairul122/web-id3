<?php
// Variabel: $pageTitle, $active (kunci menu), $crumbs ([label => url|null]), $user, $isAdmin, $isSiswa.
$crumbs ??= [];
// Menu per peran: admin mengelola data dan model, guru BK membimbing siswa pada sekolahnya, siswa memakai simulasi pribadi.
$menus = [
    'admin' => [
        'Utama' => [
            'dashboard' => ['Dashboard', 'dashboard/', 'grid'],
        ],
        'Kelola Data' => [
            'siswa' => ['Data Siswa', 'siswa/', 'users'],
            'sekolah' => ['Data Sekolah', 'sekolah/', 'school'],
            'atribut' => ['Atribut ID3', 'atribut/', 'list'],
            'diskretisasi' => ['Diskretisasi Nilai', 'diskretisasi/', 'sliders'],
        ],
        'Proses ID3' => [
            'training' => ['Training Model', 'training/', 'branch'],
            'pengujian' => ['Pengujian Model', 'pengujian/', 'chart'],
            'prediksi' => ['Prediksi', 'prediksi/', 'target'],
        ],
        'Administrasi' => [
            'pengguna' => ['Pengguna', 'pengguna/', 'user'],
            'laporan' => ['Laporan', 'laporan/', 'file'],
        ],
    ],
    'guru_bk' => [
        'Utama' => [
            'dashboard' => ['Dashboard', 'dashboard/', 'grid'],
        ],
        'Bimbingan' => [
            'simulasi' => ['Cek Rekomendasi', 'prediksi/form', 'target'],
            'prediksi' => ['Riwayat Prediksi', 'prediksi/', 'list'],
            'siswa' => ['Data Siswa', 'siswa/', 'users'],
            'laporan' => ['Laporan', 'laporan/', 'file'],
        ],
        'Referensi (hanya lihat)' => [
            'atribut' => ['Atribut ID3', 'atribut/', 'list'],
            'diskretisasi' => ['Diskretisasi Nilai', 'diskretisasi/', 'sliders'],
            'sekolah' => ['Data Sekolah', 'sekolah/', 'school'],
        ],
    ],
    'siswa' => [
        'Utama' => [
            'dashboard' => ['Dashboard', 'dashboard/', 'grid'],
            'simulasi' => ['Cek Rekomendasi', 'prediksi/form', 'target'],
            'riwayat' => ['Riwayat Saya', 'prediksi/', 'list'],
        ],
    ],
];
$menu = $menus[$user['role']] ?? $menus['siswa'];
$menu['Akun'] = ['akun' => ['Akun Saya', 'akun/', 'sliders']];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#071d31">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($pageTitle) ?> | Panel SIMINAT</title>
  <link rel="icon" href="<?= e(url('assets/img/logo.svg')) ?>" type="image/svg+xml">
  <link rel="stylesheet" href="<?= e(url('assets/css/tokens.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>?v=<?= filemtime(APP_ROOT . '/assets/css/admin.css') ?>">
  <link rel="stylesheet" href="<?= e(url('assets/css/dialog.css')) ?>?v=<?= filemtime(APP_ROOT . '/assets/css/dialog.css') ?>">
  <script src="<?= e(url('assets/js/dialog.js')) ?>?v=<?= filemtime(APP_ROOT . '/assets/js/dialog.js') ?>" defer></script>
  <script src="<?= e(url('assets/js/admin.js')) ?>?v=<?= filemtime(APP_ROOT . '/assets/js/admin.js') ?>" defer></script>
</head>
<body class="admin">
<a class="skip-link" href="#konten">Lewati ke konten</a>
<div class="admin-shell">
  <aside class="sidebar" id="sidebar" aria-label="Menu panel">
    <a class="sidebar__brand" href="<?= e(admin_url('dashboard/')) ?>">
      <svg viewBox="0 0 64 72" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M11 30V19L32 6l21 13v11M6 34c10-2 18 1 26 7 8-6 16-9 26-7v29c-10-2-18 1-26 7-8-6-16-9-26-7V34Z"/><path d="M32 41v29M10 57c8-1 15 2 22 7 7-5 14-8 22-7"/><path d="m32 20 4 6-4 6-4-6 4-6Z" fill="currentColor" stroke="none"/></svg>
      <span><strong>SIMINAT</strong><span>Panel <?= e(PenggunaService::ROLES[$user['role']]) ?></span></span>
    </a>
    <nav aria-label="Navigasi panel">
      <?php foreach ($menu as $group => $items): ?>
        <p class="nav-group"><?= e($group) ?></p>
        <?php foreach ($items as $key => [$label, $path, $iconName]): ?>
          <a class="nav-item" href="<?= e(admin_url($path)) ?>" <?= $active === $key ? 'aria-current="page"' : '' ?>><?= icon($iconName) ?> <?= e($label) ?></a>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </nav>
  </aside>
  <button class="sidebar-backdrop" type="button" data-sidebar-close aria-label="Tutup menu" tabindex="-1"></button>
  <div class="admin-main">
    <header class="topbar">
      <button class="topbar__menu" type="button" data-sidebar-toggle aria-controls="sidebar" aria-expanded="false" aria-label="Buka menu"><?= icon('menu') ?></button>
      <div class="topbar__title">
        <ol class="crumbs" aria-label="Breadcrumb">
          <li><a href="<?= e(admin_url('dashboard/')) ?>">Panel</a></li>
          <?php foreach ($crumbs as $label => $href): ?>
            <li><?php if ($href): ?><a href="<?= e(admin_url($href)) ?>"><?= e($label) ?></a><?php else: ?><span aria-current="page"><?= e($label) ?></span><?php endif; ?></li>
          <?php endforeach; ?>
        </ol>
      </div>
      <details class="topbar__user-menu" data-user-menu>
        <summary class="topbar__user" data-user-menu-toggle aria-label="Menu akun: <?= e($user['nama']) ?>">
          <span class="topbar__avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($user['nama'], 0, 1))) ?></span>
          <span class="topbar__user-info">
            <span class="topbar__user-name"><?= e($user['nama']) ?></span>
            <small class="topbar__user-role"><?= e(PenggunaService::ROLES[$user['role']]) ?></small>
          </span>
          <?= icon('chevron', 'topbar__user-chevron') ?>
        </summary>
        <div class="user-dropdown" data-user-menu-dropdown role="menu">
          <div class="user-dropdown__header">
            <div class="user-dropdown__name"><?= e($user['nama']) ?></div>
            <div class="user-dropdown__role"><?= e(PenggunaService::ROLES[$user['role']]) ?></div>
          </div>
          <div class="user-dropdown__divider" role="separator"></div>
          <a class="user-dropdown__item" href="<?= e(admin_url('akun/')) ?>" role="menuitem">
            <?= icon('user') ?>
            <span>Profil Saya</span>
          </a>
          <a class="user-dropdown__item" href="<?= e(admin_url('akun/password')) ?>" role="menuitem">
            <?= icon('edit') ?>
            <span>Ganti Kata Sandi</span>
          </a>
          <div class="user-dropdown__divider" role="separator"></div>
          <form method="post" action="<?= e(url('logout.php')) ?>" data-confirm="Anda akan keluar dari panel SIMINAT." data-confirm-title="Keluar dari akun?" data-confirm-ok="Ya, keluar" data-confirm-danger>
            <?= Csrf::field() ?>
            <button class="user-dropdown__item user-dropdown__item--danger sidebar__logout" type="submit" role="menuitem">
              <?= icon('logout') ?>
              <span>Keluar</span>
            </button>
          </form>
        </div>
      </details>
    </header>
    <main class="admin-content" id="konten" tabindex="-1">
