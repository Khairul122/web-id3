<?php
// Variabel: $pageTitle, $active (kunci menu), $crumbs ([label => url|null]), $user, $isAdmin, $isSiswa.
$crumbs ??= [];
$menu = [
    'Utama' => [
        'dashboard' => ['Dashboard', 'dashboard/', 'grid', null],
        'simulasi' => ['Cek Rekomendasi', 'prediksi/form', 'target', ['siswa']],
        'riwayat' => ['Riwayat Saya', 'prediksi/', 'list', ['siswa']],
    ],
    'Master Data' => [
        'siswa' => ['Data Siswa', 'siswa/', 'users', ['admin', 'guru_bk']],
        'sekolah' => ['Data Sekolah', 'sekolah/', 'school', ['admin', 'guru_bk']],
        'atribut' => ['Atribut ID3', 'atribut/', 'list', ['admin', 'guru_bk']],
        'diskretisasi' => ['Diskretisasi Nilai', 'diskretisasi/', 'sliders', ['admin', 'guru_bk']],
    ],
    'Proses ID3' => [
        'training' => ['Training Model', 'training/', 'branch', ['admin', 'guru_bk']],
        'pengujian' => ['Pengujian Model', 'pengujian/', 'chart', ['admin', 'guru_bk']],
        'prediksi' => ['Prediksi', 'prediksi/', 'target', ['admin', 'guru_bk']],
    ],
    'Lainnya' => [
        'laporan' => ['Laporan', 'laporan/', 'file', ['admin', 'guru_bk']],
        'pengguna' => ['Pengguna', 'pengguna/', 'user', ['admin']],
        'akun' => ['Akun Saya', 'akun/', 'sliders', null],
    ],
];
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
  <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
  <link rel="stylesheet" href="<?= e(url('assets/css/dialog.css')) ?>">
  <script src="<?= e(url('assets/js/dialog.js')) ?>" defer></script>
  <script src="<?= e(url('assets/js/admin.js')) ?>" defer></script>
</head>
<body class="admin">
<a class="skip-link" href="#konten">Lewati ke konten</a>
<div class="admin-shell">
  <aside class="sidebar" id="sidebar" aria-label="Menu panel">
    <a class="sidebar__brand" href="<?= e(admin_url('dashboard/')) ?>">
      <svg viewBox="0 0 64 72" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M11 30V19L32 6l21 13v11M6 34c10-2 18 1 26 7 8-6 16-9 26-7v29c-10-2-18 1-26 7-8-6-16-9-26-7V34Z"/><path d="M32 41v29M10 57c8-1 15 2 22 7 7-5 14-8 22-7"/><path d="m32 20 4 6-4 6-4-6 4-6Z" fill="currentColor" stroke="none"/></svg>
      <span><strong>SIMINAT</strong><span><?= $isSiswa ? 'Panel Siswa' : 'Panel Pengelola' ?></span></span>
    </a>
    <nav aria-label="Navigasi panel">
      <?php foreach ($menu as $group => $items): ?>
        <?php if (!array_filter($items, static fn ($item): bool => $item[3] === null || in_array($user['role'], $item[3], true))) continue; ?>
        <p class="nav-group"><?= e($group) ?></p>
        <?php foreach ($items as $key => [$label, $path, $iconName, $roles]): ?>
          <?php if ($roles !== null && !in_array($user['role'], $roles, true)) continue; ?>
          <a class="nav-item" href="<?= e(admin_url($path)) ?>" <?= $active === $key ? 'aria-current="page"' : '' ?>><?= icon($iconName) ?> <?= e($label) ?></a>
        <?php endforeach; ?>
      <?php endforeach; ?>
      <p class="nav-group">Situs</p>
      <a class="nav-item" href="<?= e(url()) ?>"><?= icon('back') ?> Lihat Beranda</a>
    </nav>
    <div class="sidebar__foot">
      <form method="post" action="<?= e(url('logout.php')) ?>" data-confirm="Anda akan keluar dari panel SIMINAT." data-confirm-title="Keluar dari akun?" data-confirm-ok="Ya, keluar" data-confirm-danger>
        <?= Csrf::field() ?>
        <button class="sidebar__logout" type="submit"><?= icon('logout') ?> Keluar</button>
      </form>
    </div>
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
      <a class="topbar__user" href="<?= e(admin_url('akun/')) ?>">
        <span class="topbar__avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($user['nama'], 0, 1))) ?></span>
        <span><?= e($user['nama']) ?><small><?= e(PenggunaService::ROLES[$user['role']]) ?></small></span>
      </a>
    </header>
    <main class="admin-content" id="konten" tabindex="-1">
