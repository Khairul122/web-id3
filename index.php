<?php
declare(strict_types=1);

require __DIR__ . '/app/core/bootstrap.php';
$accountUrl = url(Auth::isSignedIn() ? 'admin/' : 'login.php');
$accountLabel = Auth::isSignedIn() ? 'Panel Saya' : 'Masuk';
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
  <link rel="stylesheet" href="assets/css/landing.css?v=20261017-motion">
  <script src="assets/js/hero.js?v=20261017-motion" defer></script>
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
  <section class="hero" id="beranda" aria-labelledby="hero-title" tabindex="-1">
    <div class="hero__visual" aria-hidden="true"><div class="hero__photo" data-parallax="hero"></div></div>
    <div class="header-shell" data-header>
    <header class="site-header container">
      <a class="brand" href="./" aria-label="SIMINAT, beranda">
        <svg class="brand__mark" viewBox="0 0 64 72" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M11 30V19L32 6l21 13v11M6 34c10-2 18 1 26 7 8-6 16-9 26-7v29c-10-2-18 1-26 7-8-6-16-9-26-7V34Z"/><path d="M32 41v29M10 57c8-1 15 2 22 7 7-5 14-8 22-7"/><path d="m32 20 4 6-4 6-4-6 4-6Z" fill="currentColor" stroke="none"/></svg>
        <span><span class="brand__name">SIMINAT</span><span class="brand__tagline">Kenali minat. Rencanakan masa depan.</span></span>
      </a>
      <nav class="desktop-nav" aria-label="Navigasi utama">
        <a class="nav-link nav-link--active" href="#beranda" aria-current="location">Beranda</a>
        <a class="nav-link" href="#rumpun">Rumpun Jurusan</a>
        <a class="nav-link" href="#cara-kerja">Cara Kerja</a>
        <a class="nav-link" href="#metode">Metode ID3</a>
        <a class="nav-link" href="#panduan">Panduan</a>
        <a class="button button--outline" href="<?= e($accountUrl) ?>"><?= e($accountLabel) ?></a>
      </nav>
      <button class="menu-toggle" type="button" data-menu-open aria-label="Buka menu navigasi" aria-haspopup="dialog" aria-controls="mobile-menu" hidden><?= icon('menu') ?></button>
    </header>
    <div class="reading-progress" aria-hidden="true"><span data-reading-progress></span></div>
    </div>
    <div class="header-space" aria-hidden="true"></div>

    <div class="hero__main container">
      <div class="hero__copy">
        <p class="hero__eyebrow">Dari ruang kelas, menuju masa depan</p>
        <h1 id="hero-title"><span class="hero__line"><span>Kenali Minatmu.</span></span><span class="hero__line"><span>Temukan</span></span><span class="hero__line"><span><em>Arah Kuliahmu.</em></span></span></h1>
        <p class="hero__description">Langkah awal mengenali rumpun jurusan kuliah<br class="desktop-break"> melalui nilai rapor dan peminatanmu.</p>
        <div class="hero__actions">
          <a class="button button--primary" href="#rumpun">Kenali Rumpun Jurusan <?= icon('arrow') ?></a>
          <a class="text-link" href="#cara-kerja">Bagaimana cara kerjanya?</a>
        </div>
      </div>
      <a class="hero__scroll" href="#rumpun"><span>Jelajahi pilihanmu</span><?= icon('arrow') ?></a>
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
  <section class="landing-section section--ivory" id="rumpun" aria-labelledby="rumpun-title" tabindex="-1">
    <div class="container rumpun-layout">
      <div>
        <p class="section-eyebrow">Rumpun jurusan</p>
        <h2 class="section-title" id="rumpun-title">Kenali bidangnya.<br><em>Temukan minatmu.</em></h2>
        <p class="section-intro">Setiap rumpun membuka banyak pilihan. Mulailah dengan memahami bidang yang ingin kamu pelajari.</p>
        <figure class="editorial-photo">
          <div class="editorial-photo__frame"><div class="photo-window"><img data-parallax="photo" src="assets/img/landing/jelajah-rumpun-ai.webp" srcset="assets/img/landing/jelajah-rumpun-ai-768.webp 768w, assets/img/landing/jelajah-rumpun-ai.webp 1536w" sizes="(min-width: 768px) 44vw, 100vw" width="1536" height="1024" loading="lazy" decoding="async" alt="Ilustrasi dua siswa madrasah membaca panduan bidang studi bersama di perpustakaan."></div><p class="photo-caption">Mulai dari rasa ingin tahu.</p></div>
          <figcaption>Ilustrasi dibuat dengan AI.</figcaption>
        </figure>
      </div>
      <div class="rumpun-details">
        <dl class="rumpun-definitions">
          <div>
            <dt><span class="row-number" aria-hidden="true">01</span>Saintek</dt>
            <dd><strong>Sains dan teknologi</strong><p>Matematika, ilmu alam, informatika, dan bidang teknologi.</p></dd>
          </div>
          <div>
            <dt><span class="row-number" aria-hidden="true">02</span>Soshum</dt>
            <dd><strong>Sosial dan humaniora</strong><p>Ekonomi, ilmu sosial, bahasa, dan humaniora.</p></dd>
          </div>
          <div>
            <dt><span class="row-number" aria-hidden="true">03</span>Campuran</dt>
            <dd><strong>Pilihan lintas rumpun</strong><p>Pilihan yang mencakup bidang Saintek dan Soshum.</p></dd>
          </div>
        </dl>
        <p class="section-note">Contoh bidang di atas membantu mengenali pilihan, tanpa membatasi ketertarikanmu pada bidang lain.</p>
      </div>
    </div>
  </section>
  <section class="landing-section section--paper" id="cara-kerja" aria-labelledby="cara-kerja-title" tabindex="-1">
    <div class="container">
      <div class="section-heading-row">
        <div><p class="section-eyebrow">Cara kerja</p><h2 class="section-title" id="cara-kerja-title">Dari nilai rapor<br>ke <em>bahan diskusi.</em></h2></div>
        <p class="section-intro">Kenali alur pengolahan sebelum membaca rekomendasinya.</p>
      </div>
      <ol class="process-list" role="list">
        <li><span class="process-number" aria-hidden="true">01</span><div><h3>Data siswa</h3><p>Siapkan nilai rapor dan informasi peminatan sebagai masukan untuk sistem.</p></div></li>
        <li><span class="process-number" aria-hidden="true">02</span><div><h3>Kategori nilai</h3><p>Nilai diubah menjadi kategori sesuai aturan diskretisasi yang digunakan sistem.</p></div></li>
        <li><span class="process-number" aria-hidden="true">03</span><div><h3>Pohon ID3</h3><p>Sistem menelusuri cabang pohon keputusan berdasarkan atribut siswa.</p></div></li>
        <li><span class="process-number" aria-hidden="true">04</span><div><h3>Diskusi hasil</h3><p>Baca rekomendasi dan jalur keputusannya bersama guru BK untuk memahami dasar hasilnya.</p></div></li>
      </ol>
      <p class="process-note"><span class="process-note__statement">Hasil menjadi <em>bahan percakapan</em> bersama guru BK.</span><span class="process-note__detail">Kenali dasarnya, lalu pertimbangkan pilihanmu.</span></p>
    </div>
  </section>

  <section class="landing-section section--navy" id="metode" aria-labelledby="metode-title" tabindex="-1">
    <div class="container method-layout">
      <div>
        <p class="section-eyebrow">Metode ID3</p>
        <h2 class="section-title" id="metode-title">Keputusan yang<br><em>bisa ditelusuri.</em></h2>
        <p class="section-intro">ID3 memilih atribut dengan <em>information gain</em> tertinggi untuk membentuk percabangan pohon keputusan.</p>
        <div class="method-formulas">
          <div class="formula">
            <p class="formula__expression" role="math" aria-label="Entropy S sama dengan negatif jumlah proporsi setiap kelas dikali logaritma basis dua proporsi kelas tersebut."><span>Entropy(S) =</span><span>− ∑<sub>c</sub> p(c) log<sub>2</sub> p(c)</span></p>
            <p>Entropy mengukur keragaman kelas dalam sekelompok data.</p>
          </div>
          <div class="formula">
            <p class="formula__expression" role="math" aria-label="Gain S A sama dengan entropy S dikurangi jumlah bobot tiap bagian data dikali entropy bagian tersebut."><span>Gain(S, A) = Entropy(S)</span><span>− ∑<sub>v</sub> (|S<sub>v</sub>| / |S|) Entropy(S<sub>v</sub>)</span></p>
            <p>Gain menunjukkan berkurangnya keragaman setelah data dipisahkan menurut suatu atribut.</p>
          </div>
        </div>
        <p class="formula-notation">S adalah kelompok data, p(c) adalah proporsi kelas c, dan S<sub>v</sub> adalah bagian data dengan nilai atribut v. Jika p(c) = 0, suku tersebut dianggap 0.</p>
        <h3 class="stop-title">Kapan percabangan berhenti?</h3>
        <ul class="stop-list"><li>Seluruh data pada node memiliki kelas yang sama.</li><li>Atribut untuk memisahkan data sudah habis.</li><li>Data pada cabang habis; kelas mayoritas induk digunakan.</li></ul>
      </div>
      <figure class="method-tree">
        <p class="tree-heading">Dari atribut<br><em>menuju rekomendasi.</em></p>
        <svg viewBox="0 0 560 380" role="img" aria-labelledby="tree-title tree-description">
          <title id="tree-title">Ilustrasi struktur pohon keputusan ID3</title>
          <desc id="tree-description">Atribut terpilih bercabang berdasarkan nilai A dan nilai B. Cabang A memiliki atribut berikutnya, dengan daun Saintek atau Campuran. Cabang B memiliki daun Soshum. Skema ini hanya ilustrasi struktur, bukan hasil data siswa.</desc>
          <g class="tree-paths" fill="none"><path pathLength="1" d="M280 70 160 148"/><path pathLength="1" d="M280 70 438 148"/><path pathLength="1" d="M160 202 91 288"/><path pathLength="1" d="M160 202 280 288"/></g>
          <g class="tree-labels"><text x="184" y="115">Nilai A</text><text x="394" y="115">Nilai B</text></g>
          <g class="tree-node"><rect x="175" y="16" width="210" height="54" rx="3"/><text x="280" y="50">Atribut terpilih</text></g>
          <g class="tree-node"><rect x="50" y="148" width="220" height="54" rx="3"/><text x="160" y="182">Atribut berikutnya</text></g>
          <g class="tree-node tree-node--leaf"><rect x="365" y="148" width="145" height="54" rx="3"/><text x="437.5" y="182">Soshum</text></g>
          <g class="tree-node tree-node--leaf"><rect x="20" y="288" width="142" height="50" rx="3"/><text x="91" y="320">Saintek</text></g>
          <g class="tree-node tree-node--leaf"><rect x="200" y="288" width="160" height="50" rx="3"/><text x="280" y="320">Campuran</text></g>
        </svg>
        <figcaption>Ilustrasi, bukan pohon hasil data siswa</figcaption>
        <p>Nama node dan jalur menggambarkan struktur percabangan, bukan aturan rekomendasi yang sebenarnya.</p>
        <button type="button" class="tree-replay" data-tree-replay hidden>Putar ulang ilustrasi <?= icon('arrow') ?></button>
      </figure>
    </div>
  </section>

  <section class="landing-section section--ivory" id="panduan" aria-labelledby="panduan-title" tabindex="-1">
    <div class="container">
      <div class="guide-layout">
        <div>
          <p class="section-eyebrow">Panduan siswa</p>
          <h2 class="section-title" id="panduan-title">Siapkan pilihanmu,<br><em>bicarakan bersama.</em></h2>
          <p class="section-intro">Mulai dengan memahami minatmu dan menyiapkan informasi yang dibutuhkan.</p>
          <ol class="student-steps" role="list">
            <li><span aria-hidden="true">01</span><p>Siapkan nilai rapor dan informasi peminatanmu.</p></li>
            <li><span aria-hidden="true">02</span><p>Minta pendampingan guru BK untuk menjalankan rekomendasi melalui panel SIMINAT.</p></li>
            <li><span aria-hidden="true">03</span><p>Baca jalur keputusan dan catat pertanyaanmu tentang hasil rekomendasi.</p></li>
            <li><span aria-hidden="true">04</span><p>Diskusikan pilihan bersama guru BK dan orang tua, sesuai minat dan rencana kuliahmu.</p></li>
          </ol>
        </div>
        <div class="teacher-guide">
          <figure class="editorial-photo">
            <div class="editorial-photo__frame"><div class="photo-window"><img data-parallax="photo" src="assets/img/landing/pendampingan-guru-ai.webp" srcset="assets/img/landing/pendampingan-guru-ai-768.webp 768w, assets/img/landing/pendampingan-guru-ai.webp 1536w" sizes="(min-width: 768px) 38vw, 100vw" width="1536" height="1024" loading="lazy" decoding="async" alt="Ilustrasi guru BK mendampingi siswi membaca catatan pilihan kuliah."></div></div>
            <figcaption>Ilustrasi dibuat dengan AI.</figcaption>
          </figure>
          <div class="teacher-guide__content"><h3>Untuk Guru BK</h3>
          <p>Dampingi siswa membaca rekomendasi, lalu hubungkan dengan ketertarikan dan rencana kariernya.</p>
          <a class="button button--navy" href="<?= e($accountUrl) ?>">Masuk ke Panel <?= icon('arrow') ?></a></div>
        </div>
      </div>
      <p class="guide-note">Rekomendasi adalah bahan pertimbangan, bukan keputusan akhir.</p>
    </div>
  </section>
</main>

<footer class="landing-footer" aria-label="Informasi dan kontak SIMINAT">
  <div class="container">
    <div class="footer-invitation"><p>Pilihan yang matang<br>dimulai dari <em>percakapan.</em></p><a href="#beranda" class="footer-top" aria-label="Kembali ke beranda"><?= icon('arrow') ?></a></div>
    <div class="footer-groups">
      <div class="footer-identity"><?= brand() ?><p>Pendamping diskusi pilihan kuliah untuk siswa dan guru BK.</p></div>
      <div class="footer-schools"><h2>Dua madrasah,<br>satu tujuan.</h2><ul><li><strong>MAN 4 Matangkuli</strong><span>Matangkuli</span></li><li><strong>MAS Al-Muslimun Lhoksukon</strong><span>Lhoksukon</span></li></ul></div>
      <div class="footer-contact"><p class="section-eyebrow">Ada pertanyaan?</p><h2>Mulai percakapan<br>dengan guru BK.</h2><p>Hubungi guru BK di madrasah masing-masing untuk informasi dan pendampingan.</p></div>
    </div>
    <div class="footer-navigation"><nav aria-label="Navigasi footer"><a href="#rumpun">Rumpun Jurusan</a><a href="#cara-kerja">Cara Kerja</a><a href="#metode">Metode ID3</a><a href="#panduan">Panduan</a></nav><a class="back-to-top" href="#beranda">Kembali ke atas <?= icon('arrow') ?></a></div>
    <p class="footer-disclosure">Foto pada halaman ini merupakan ilustrasi yang dibuat dengan AI.</p>
  </div>
</footer>

<dialog id="mobile-menu" class="mobile-menu" aria-labelledby="menu-title">
  <div class="mobile-menu__header"><span id="menu-title">SIMINAT</span><button class="icon-button" type="button" data-close aria-label="Tutup menu"><?= icon('close') ?></button></div>
  <nav aria-label="Navigasi ponsel">
    <a href="#beranda" aria-current="location">Beranda <?= icon('chevron') ?></a>
    <a href="#rumpun">Rumpun Jurusan <?= icon('chevron') ?></a>
    <a href="#cara-kerja">Cara Kerja <?= icon('chevron') ?></a>
    <a href="#metode">Metode ID3 <?= icon('chevron') ?></a>
    <a href="#panduan">Panduan <?= icon('chevron') ?></a>
    <a href="<?= e($accountUrl) ?>"><?= e($accountLabel) ?> <?= icon('chevron') ?></a>
  </nav>
  <p class="mobile-menu__note">Kenali minat.<br>Rencanakan masa depan.</p>
</dialog>

<noscript><div class="noscript-notice"><p>Seluruh penjelasan dapat dibaca tanpa JavaScript. Gunakan tautan ini untuk berpindah bagian:</p><nav aria-label="Navigasi tanpa JavaScript"><a href="#rumpun">Rumpun Jurusan</a><a href="#cara-kerja">Cara Kerja</a><a href="#metode">Metode ID3</a><a href="#panduan">Panduan</a><a href="<?= e($accountUrl) ?>"><?= e($accountLabel) ?></a></nav></div></noscript>
</body>
</html>
