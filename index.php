<?php
declare(strict_types=1);

require __DIR__ . '/app/core/bootstrap.php';
$accountUrl = url(Auth::isSignedIn() ? 'admin/' : 'login.php');
$accountLabel = Auth::isSignedIn() ? 'Panel Saya' : 'Masuk';
// Ringkasan model aktif untuk dialog Metode ID3. Halaman tetap tampil bila database belum siap.
$modelSummary = '';
try {
    $model = ModelId3::active();
    $evaluation = $model ? Evaluasi::latestForModel((int) $model['id']) : null;
    if ($model) {
        $modelSummary = 'Model aktif saat ini dilatih dari ' . (int) $model['jumlah_data'] . ' data siswa berlabel ('
            . strtolower(ModelId3::SCENARIOS[$model['skenario']] ?? $model['skenario']) . ') dengan akar pohon '
            . ($model['tree']['atribut'] ?? $model['tree']['kelas']) . '.'
            . ($evaluation ? ' Pada pengujian ' . (int) $evaluation['k'] . '-fold cross validation, akurasinya ' . pct($evaluation['akurasi']) . ' dan F1 macro ' . pct($evaluation['f1_macro']) . '.' : '');
    }
} catch (Throwable $exception) {
    error_log('Ringkasan model beranda: ' . $exception->getMessage());
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#071d31">
  <meta name="description" content="SIMINAT membantu siswa mengenali rumpun jurusan kuliah melalui nilai rapor dan peminatan sebagai bahan diskusi bersama guru BK.">
  <title>SIMINAT | Kenali Minat, Rencanakan Masa Depan</title>
  <link rel="icon" href="assets/img/logo.svg" type="image/svg+xml">
  <link rel="preload" href="assets/img/hero/kelas-madrasah-ai.webp" as="image" fetchpriority="high">
  <link rel="stylesheet" href="assets/css/tokens.css">
  <link rel="stylesheet" href="assets/css/hero.css">
  <script src="assets/js/hero.js" defer></script>
</head>
<body>
<a class="skip-link" href="#konten">Lewati ke konten</a>
<div class="school-bar">
  <div class="container school-bar__inner">
    <p>MAN 4 Matangkuli <span aria-hidden="true">/</span> MAS Al-Muslimun Lhoksukon</p>
    <p class="school-bar__purpose">Sistem pendukung bimbingan karier</p>
  </div>
</div>
<main id="konten" tabindex="-1">
  <section class="hero" aria-labelledby="hero-title">
    <div class="hero__photo" aria-hidden="true"></div>
    <header class="site-header container">
      <a class="brand" href="./" aria-label="SIMINAT, beranda">
        <svg class="brand__mark" viewBox="0 0 64 72" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M11 30V19L32 6l21 13v11M6 34c10-2 18 1 26 7 8-6 16-9 26-7v29c-10-2-18 1-26 7-8-6-16-9-26-7V34Z"/><path d="M32 41v29M10 57c8-1 15 2 22 7 7-5 14-8 22-7"/><path d="m32 20 4 6-4 6-4-6 4-6Z" fill="currentColor" stroke="none"/></svg>
        <span><span class="brand__name">SIMINAT</span><span class="brand__tagline">Kenali minat. Rencanakan masa depan.</span></span>
      </a>
      <nav class="desktop-nav" aria-label="Navigasi utama">
        <a class="nav-link nav-link--active" href="./" aria-current="page">Beranda</a>
        <button class="nav-link" type="button" data-dialog="tentang">Tentang Sistem</button>
        <button class="nav-link" type="button" data-dialog="metode">Metode ID3</button>
        <button class="nav-link" type="button" data-dialog="panduan">Panduan</button>
        <a class="button button--outline" href="<?= e($accountUrl) ?>"><?= e($accountLabel) ?></a>
      </nav>
      <button class="menu-toggle" type="button" data-menu-open aria-label="Buka menu navigasi" aria-haspopup="dialog" aria-controls="mobile-menu"><?= icon('menu') ?></button>
    </header>

    <div class="hero__main container">
      <div class="hero__copy">
        <p class="hero__eyebrow">Dari ruang kelas, menuju masa depan</p>
        <h1 id="hero-title">Kenali Minatmu.<br>Temukan<br><em>Arah Kuliahmu.</em></h1>
        <p class="hero__description">Langkah awal mengenali rumpun jurusan kuliah<br class="desktop-break"> melalui nilai rapor dan peminatanmu.</p>
        <div class="hero__actions">
          <button class="button button--primary" type="button" data-dialog="rumpun">Kenali Rumpun Jurusan <?= icon('arrow') ?></button>
          <button class="text-link" type="button" data-dialog="panduan">Bagaimana cara kerjanya?</button>
        </div>
      </div>
    </div>

    <div class="hero__foundation">
      <div class="foundation container" role="group" aria-label="Dasar pendekatan SIMINAT">
        <article class="foundation__item">
          <?= icon('book', 'foundation__icon') ?>
          <div><h2>Berangkat dari Nilai Rapor</h2><p>Nilai rapor dan peminatan menjadi<br class="desktop-break"> dasar untuk mengenali rumpun jurusan.</p></div>
        </article>
        <article class="foundation__item">
          <?= icon('branch', 'foundation__icon') ?>
          <div><h2>Metode ID3 yang Terbuka</h2><p>Pelajari bagaimana pohon keputusan<br class="desktop-break"> membentuk hasil klasifikasi.</p></div>
        </article>
        <article class="foundation__item">
          <?= icon('cap', 'foundation__icon') ?>
          <div><h2>Diskusi Bersama Guru BK</h2><p>Kenali pilihanmu sebagai bahan diskusi<br class="desktop-break"> untuk merencanakan langkah berikutnya.</p></div>
        </article>
      </div>
      <div class="hero__credit container"><span>SIMINAT · Klasifikasi Minat Jurusan Kuliah</span><span>Visual ilustrasi dibuat dengan AI</span></div>
    </div>
  </section>
</main>

<dialog id="mobile-menu" class="mobile-menu" aria-labelledby="menu-title">
  <div class="mobile-menu__header"><span id="menu-title">SIMINAT</span><button class="icon-button" type="button" data-close aria-label="Tutup menu"><?= icon('close') ?></button></div>
  <nav aria-label="Navigasi ponsel">
    <a href="./" aria-current="page">Beranda <?= icon('chevron') ?></a>
    <button type="button" data-dialog="tentang">Tentang Sistem <?= icon('chevron') ?></button>
    <button type="button" data-dialog="metode">Metode ID3 <?= icon('chevron') ?></button>
    <button type="button" data-dialog="panduan">Panduan <?= icon('chevron') ?></button>
    <a href="<?= e($accountUrl) ?>"><?= e($accountLabel) ?> <?= icon('chevron') ?></a>
  </nav>
  <p class="mobile-menu__note">Kenali minat.<br>Rencanakan masa depan.</p>
</dialog>

<dialog id="info-dialog" class="info-dialog" aria-labelledby="dialog-title">
  <div class="info-dialog__header"><span class="info-dialog__label">SIMINAT</span><button class="icon-button" type="button" data-close aria-label="Tutup penjelasan"><?= icon('close') ?></button></div>
  <div id="dialog-content"></div>
  <button class="button button--dialog" type="button" data-close>Kembali ke beranda</button>
</dialog>

<template id="content-tentang"><h2 id="dialog-title">Mengenal SIMINAT</h2><p>SIMINAT adalah proyek sistem klasifikasi minat jurusan kuliah untuk siswa kelas XII MAN 4 Matangkuli dan MAS Al-Muslimun Lhoksukon.</p><p>Sistem ini menggunakan nilai rapor dan peminatan untuk mengenali rumpun Saintek, Soshum, atau Campuran melalui algoritma ID3.</p><p>Hasilnya menjadi bahan pertimbangan bersama guru BK. Pilihan akhir tetap mempertimbangkan minat, tujuan, dan kondisi masing-masing siswa.</p></template>
<template id="content-metode"><h2 id="dialog-title">Bagaimana ID3 bekerja?</h2><p>ID3 membentuk pohon keputusan dari data siswa yang sudah memiliki label rumpun jurusan.</p><ol><li>Nilai rapor diubah menjadi kategori sesuai aturan diskretisasi.</li><li>Entropy dan information gain dihitung untuk memilih atribut pemisah.</li><li>Pohon keputusan ditelusuri menggunakan atribut siswa untuk menghasilkan klasifikasi.</li></ol><p>Jalur keputusan ditampilkan bersama hasil agar dasar klasifikasi dapat ditinjau.</p><?php if ($modelSummary !== ''): ?><p class="info-dialog__notice"><?= e($modelSummary) ?></p><?php endif; ?></template>
<template id="content-panduan"><h2 id="dialog-title">Mulai dari mengenali dirimu</h2><ol><li>Kenali perbedaan rumpun Saintek, Soshum, dan Campuran.</li><li>Siapkan nilai rapor serta informasi peminatanmu.</li><li>Minta guru BK menjalankan Cek Rekomendasi di panel SIMINAT, lalu diskusikan hasil dan jalur keputusannya bersama orang tua.</li></ol><p class="info-dialog__notice">Hasil SIMINAT adalah rekomendasi, bukan keputusan akhir.</p></template>
<template id="content-guru"><h2 id="dialog-title">Untuk Guru Bimbingan Konseling</h2><p>SIMINAT dirancang untuk mendukung percakapan tentang pilihan kuliah melalui nilai rapor, peminatan, dan jalur keputusan yang dapat ditinjau.</p><p>Gunakan klasifikasi sebagai bahan diskusi bersama siswa, dengan tetap memperhatikan ketertarikan dan rencana karier mereka.</p><p class="info-dialog__notice">Masuk ke panel untuk mengelola data siswa, melatih dan menguji model, serta menjalankan Cek Rekomendasi.</p></template>
<template id="content-rumpun"><h2 id="dialog-title">Kenali rumpun jurusanmu</h2><dl class="rumpun-list"><div><dt>Saintek</dt><dd>Rumpun sains dan teknologi, seperti matematika, ilmu alam, dan informatika.</dd></div><div><dt>Soshum</dt><dd>Rumpun sosial dan humaniora, seperti ekonomi, ilmu sosial, dan bahasa.</dd></div><div><dt>Campuran</dt><dd>Kategori dalam dataset untuk pilihan yang mencakup rumpun Saintek dan Soshum.</dd></div></dl><p class="info-dialog__notice">Rekomendasi rumpun berdasarkan nilai rapor dijalankan oleh guru BK melalui panel SIMINAT, lengkap dengan jalur keputusannya.</p></template>
<noscript><p class="noscript-notice">Aktifkan JavaScript untuk membuka menu dan penjelasan. SIMINAT mengklasifikasikan minat rumpun jurusan kuliah dengan algoritma ID3. Rekomendasi dijalankan oleh guru BK melalui panel.</p></noscript>
</body>
</html>
