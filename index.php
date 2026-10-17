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
  <link rel="stylesheet" href="assets/css/landing-sections.css?v=20261017-redesign">
  <script src="assets/js/hero.js?v=20261017-motion" defer></script>
  <script src="assets/js/landing-sections.js?v=20261017-redesign" defer></script>
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
        <p class="hero__description">Langkah awal mengenali rumpun jurusan kuliah melalui nilai rapor dan peminatanmu.</p>
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
          <div><h2>Nilai dan Peminatan</h2><p>Data masukan untuk mengenali kecenderungan bidang.</p></div>
        </article>
        <article class="foundation__item">
          <?= icon('branch', 'foundation__icon') ?>
          <div><h2>Pohon Keputusan ID3</h2><p>Jalur keputusan dapat dibaca kembali.</p></div>
        </article>
        <article class="foundation__item">
          <?= icon('cap', 'foundation__icon') ?>
          <div><h2>Pendampingan Guru BK</h2><p>Hasil dibahas sebelum menentukan langkah.</p></div>
        </article>
      </div>
      <div class="hero__credit container"><span>SIMINAT · Klasifikasi Minat Jurusan Kuliah</span></div>
    </div>
  </section>
  <section class="landing-section section--ivory explore-section" id="rumpun" aria-labelledby="rumpun-title" tabindex="-1">
    <div class="container">
      <div class="chapter-heading"><p class="chapter-label"><span>01 /</span> Kenali pilihanmu</p><span class="chapter-aside">Rasa ingin tahu adalah titik awal.</span></div>
      <div class="explore-heading"><h2 class="section-title" id="rumpun-title">Ada banyak jalan.<br><em>Jelajahi bidangmu.</em></h2><p class="section-intro">Kenali tiga rumpun jurusan, lihat bidang yang dipelajari, lalu hubungkan dengan hal yang kamu sukai.</p></div>
      <div class="field-explorer" data-tabs>
        <div class="field-tabs" data-tab-list aria-label="Jelajahi rumpun jurusan" hidden>
          <button id="tab-saintek" type="button" data-tab="panel-saintek"><span>01</span> Saintek <?= icon('arrow') ?></button>
          <button id="tab-soshum" type="button" data-tab="panel-soshum"><span>02</span> Soshum <?= icon('arrow') ?></button>
          <button id="tab-campuran" type="button" data-tab="panel-campuran"><span>03</span> Campuran <?= icon('arrow') ?></button>
        </div>
        <div class="explore-stage">
          <figure class="explore-photo"><div class="photo-window"><img data-parallax="photo" src="assets/img/landing/jelajah-rumpun-ai.webp" srcset="assets/img/landing/jelajah-rumpun-ai-768.webp 768w, assets/img/landing/jelajah-rumpun-ai.webp 1536w" sizes="(min-width: 768px) 42vw, 100vw" width="1536" height="1024" loading="lazy" decoding="async" alt="Ilustrasi dua siswa madrasah membaca panduan bidang studi bersama di perpustakaan."></div><figcaption>Mulai dari hal yang<br><em>ingin kamu pahami.</em></figcaption></figure>
          <div class="field-panels">
            <article class="field-panel" id="panel-saintek" data-tab-panel>
              <p class="field-category">Sains &amp; teknologi <span aria-hidden="true">01 / 03</span></p>
              <h3>Saintek<span class="field-period">.</span></h3><p class="field-description">Untuk rasa ingin tahu tentang cara alam bekerja, teknologi berkembang, dan masalah dipecahkan.</p>
              <h4>Yang akan kamu jelajahi</h4><ul class="subject-tags" aria-label="Contoh bidang Saintek"><li>Matematika</li><li>Ilmu alam</li><li>Informatika</li><li>Teknik</li><li>Kesehatan</li></ul>
              <div class="reflection"><span>Pertanyaan untuk dirimu</span><p>“Apakah aku menikmati eksperimen, logika, atau mencari cara kerja sesuatu?”</p></div>
            </article>
            <article class="field-panel" id="panel-soshum" data-tab-panel>
              <p class="field-category">Sosial &amp; humaniora <span aria-hidden="true">02 / 03</span></p>
              <h3>Soshum<span class="field-period">.</span></h3><p class="field-description">Untuk ketertarikan pada manusia, bahasa, masyarakat, dan cara kita berinteraksi dengan dunia.</p>
              <h4>Yang akan kamu jelajahi</h4><ul class="subject-tags" aria-label="Contoh bidang Soshum"><li>Ekonomi</li><li>Hukum</li><li>Pendidikan</li><li>Komunikasi</li><li>Bahasa</li></ul>
              <div class="reflection"><span>Pertanyaan untuk dirimu</span><p>“Apakah aku tertarik memahami perilaku manusia, berdiskusi, atau menyampaikan gagasan?”</p></div>
            </article>
            <article class="field-panel" id="panel-campuran" data-tab-panel>
              <p class="field-category">Pilihan lintas rumpun <span aria-hidden="true">03 / 03</span></p>
              <h3>Campuran<span class="field-period">.</span></h3><p class="field-description">Ketertarikanmu bisa melintasi satu bidang. Kenali hubungan antara sains, teknologi, dan kehidupan sosial.</p>
              <h4>Sudut pandang yang bisa dipadukan</h4><ul class="subject-tags" aria-label="Pendekatan lintas rumpun"><li>Sains</li><li>Sosial</li><li>Teknologi</li><li>Humaniora</li></ul>
              <div class="reflection"><span>Pertanyaan untuk dirimu</span><p>“Apakah aku ingin menggabungkan pendekatan dari beberapa bidang untuk menjawab satu persoalan?”</p></div>
            </article>
          </div>
        </div>
      </div>
      <div class="explore-footnote"><p>Ini peta awal untuk mengenali bidang, bukan batas pilihanmu.</p><a href="#cara-kerja">Lalu, bagaimana SIMINAT membantu? <?= icon('arrow') ?></a></div>
    </div>
  </section>
  <section class="landing-section section--paper journey-section" id="cara-kerja" aria-labelledby="cara-kerja-title" tabindex="-1">
    <div class="container">
      <div class="chapter-heading"><p class="chapter-label"><span>02 /</span> Perjalanan rekomendasi</p><span class="chapter-aside">Data masuk. Arah mulai terbaca.</span></div>
      <div class="journey-heading"><h2 class="section-title" id="cara-kerja-title">Nilai punya cerita.<br><em>Mari baca arahnya.</em></h2><p class="section-intro">Empat tahap menghubungkan data belajar dengan percakapan tentang masa depanmu.</p></div>
      <div class="journey-workspace" data-journey>
        <ol class="journey-steps" role="list">
          <li><a href="#tahap-data" data-journey-step><span>01</span><div><strong>Siapkan data</strong><small>Nilai rapor &amp; peminatan</small></div><?= icon('chevron') ?></a></li>
          <li><a href="#tahap-kategori" data-journey-step><span>02</span><div><strong>Kenali polanya</strong><small>Pengelompokan nilai</small></div><?= icon('chevron') ?></a></li>
          <li><a href="#tahap-id3" data-journey-step><span>03</span><div><strong>Telusuri jalurnya</strong><small>Pohon keputusan ID3</small></div><?= icon('chevron') ?></a></li>
          <li><a href="#tahap-diskusi" data-journey-step><span>04</span><div><strong>Bicarakan hasil</strong><small>Pendampingan guru BK</small></div><?= icon('chevron') ?></a></li>
        </ol>
        <div class="journey-panels">
          <article class="journey-panel" id="tahap-data" data-journey-panel><div class="journey-visual input-visual" aria-hidden="true"><div class="report-sheet"><span>SIMINAT / DATA MASUKAN</span><strong>Perjalanan belajarmu</strong><div><i></i> Nilai rapor <b>01</b></div><div><i></i> Peminatan <b>02</b></div><p>Disiapkan bersama guru BK</p></div><span class="sheet-stamp"><?= icon('book') ?></span></div><div class="journey-caption"><p class="visual-label">Tahap 01 / Masukan</p><h3>Mulai dari yang sudah kamu pelajari.</h3><p>Siapkan nilai rapor dan informasi peminatan. Guru BK membantu memastikan data yang digunakan sesuai dengan catatan belajarmu.</p></div></article>
          <article class="journey-panel" id="tahap-kategori" data-journey-panel><div class="journey-visual category-visual" aria-hidden="true"><span>Nilai rapor</span><div class="category-flow"><span>Tinggi</span><span>Sedang</span><span>Rendah</span></div><p>Aturan kategori yang tersimpan di sistem</p></div><div class="journey-caption"><p class="visual-label">Tahap 02 / Kategorisasi</p><h3>Angka dikelompokkan agar dapat dibaca.</h3><p>Nilai dipetakan menjadi Tinggi, Sedang, atau Rendah sesuai aturan sistem. Kategori ini menjadi atribut yang dibaca oleh pohon keputusan.</p></div></article>
          <article class="journey-panel" id="tahap-id3" data-journey-panel><div class="journey-visual branch-visual" aria-hidden="true"><span>Atribut terpilih</span><div class="mini-branches"><span>Cabang A</span><span>Cabang B</span></div><p>Gain tertinggi menentukan pemisah</p></div><div class="journey-caption"><p class="visual-label">Tahap 03 / Pengolahan ID3</p><h3>Setiap percabangan memiliki alasan.</h3><p>ID3 menggunakan information gain untuk memilih atribut pemisah. Data kemudian ditelusuri melalui jalur pohon hingga mencapai kelas rekomendasi.</p></div></article>
          <article class="journey-panel" id="tahap-diskusi" data-journey-panel><div class="journey-visual discussion-visual" aria-hidden="true"><div class="discussion-bubble">Apa yang membuatmu<br>tertarik pada bidang ini?</div><div class="discussion-bubble">Mari lihat pilihan dan<br>alasan rekomendasinya.</div></div><div class="journey-caption"><p class="visual-label">Tahap 04 / Pendampingan</p><h3>Hasil menjadi pembuka percakapan.</h3><p>Baca rumpun rekomendasi beserta jalur keputusannya bersama guru BK. Hubungkan hasil dengan minat, pilihan program studi, dan rencana kuliahmu.</p></div></article>
        </div>
      </div>
      <div class="journey-note"><span><?= icon('branch') ?> Pahami prosesnya, bukan hanya hasilnya.</span><p>Visual di atas menjelaskan alur sistem, bukan hasil prediksi siswa.</p></div>
    </div>
  </section>

  <section class="landing-section section--navy method-section" id="metode" aria-labelledby="metode-title" tabindex="-1">
    <div class="container">
      <div class="chapter-heading"><p class="chapter-label"><span>03 /</span> Di balik rekomendasi</p><span class="chapter-aside">Metode Iterative Dichotomiser 3</span></div>
      <div class="method-layout">
      <div class="method-copy">
        <h2 class="section-title" id="metode-title">Bukan sekadar hasil.<br><em>Ada alasan di setiap cabang.</em></h2>
        <p class="section-intro">Pohon keputusan ID3 menghubungkan atribut dengan rumpun rekomendasi melalui jalur yang dapat dibaca kembali.</p>
        <dl class="method-principles"><div><dt><span>01</span> Ukur keragamannya</dt><dd>Entropy mengukur seberapa beragam kelas dalam satu kelompok data.</dd></div><div><dt><span>02</span> Pilih pemisahnya</dt><dd>Information gain menunjukkan atribut yang paling mengurangi keragaman tersebut.</dd></div><div><dt><span>03</span> Ikuti percabangannya</dt><dd>Proses berulang pada kelompok berikutnya sampai kondisi berhenti tercapai.</dd></div></dl>
        <details class="method-technical"><summary>Lihat rumus &amp; kondisi berhenti<span aria-hidden="true">+</span></summary>
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
        </details>
      </div>
      <figure class="method-tree">
        <div class="tree-toolbar"><span>Memahami pohon keputusan</span><span>ID3</span></div>
        <p class="tree-heading">Satu titik awal.<br><em>Beberapa kemungkinan.</em></p>
        <svg viewBox="0 0 560 380" role="img" aria-labelledby="tree-title tree-description">
          <title id="tree-title">Ilustrasi struktur pohon keputusan ID3</title>
          <desc id="tree-description">Atribut terpilih bercabang berdasarkan nilai A dan nilai B. Cabang A memiliki atribut berikutnya, dengan daun Saintek atau Campuran. Cabang B memiliki daun Soshum. Skema ini hanya ilustrasi struktur, bukan hasil data siswa.</desc>
          <g class="tree-paths" fill="none"><path pathLength="1" d="M280 70 160 148"/><path pathLength="1" d="M280 70 438 148"/><path pathLength="1" d="M160 202 91 288"/><path pathLength="1" d="M160 202 280 288"/></g>
          <g class="tree-labels"><text x="184" y="115">Nilai A</text><text x="394" y="115">Nilai B</text></g>
          <g class="tree-node"><rect x="175" y="16" width="210" height="54" rx="3"/><text x="280" y="50">Atribut terpilih</text></g>
          <g class="tree-node"><rect x="50" y="148" width="220" height="54" rx="3"/><text x="160" y="182">Atribut berikutnya</text></g>
          <g class="tree-node tree-node--leaf tree-leaf--soshum"><rect x="365" y="148" width="145" height="54" rx="3"/><text x="437.5" y="182">Soshum</text></g>
          <g class="tree-node tree-node--leaf tree-leaf--saintek"><rect x="20" y="288" width="142" height="50" rx="3"/><text x="91" y="320">Saintek</text></g>
          <g class="tree-node tree-node--leaf tree-leaf--campuran"><rect x="200" y="288" width="160" height="50" rx="3"/><text x="280" y="320">Campuran</text></g>
        </svg>
        <figcaption>Ilustrasi, bukan pohon hasil data siswa</figcaption>
        <p>Nama node dan jalur menggambarkan struktur percabangan, bukan aturan rekomendasi yang sebenarnya.</p>
        <button type="button" class="tree-replay" data-tree-replay hidden>Putar ulang ilustrasi <?= icon('arrow') ?></button>
      </figure>
      </div>
      <div class="method-bottom"><span>Transparan dalam proses.</span><p>Jalur membantu menjelaskan rekomendasi. Pilihan kuliah tetap perlu mempertimbangkan minat dan percakapan dengan guru BK.</p></div>
    </div>
  </section>

  <section class="landing-section section--ivory preparation-section" id="panduan" aria-labelledby="panduan-title" tabindex="-1">
    <div class="container">
      <div class="chapter-heading"><p class="chapter-label"><span>04 /</span> Langkah berikutnya</p><span class="chapter-aside">Kamu tidak perlu memikirkannya sendiri.</span></div>
      <div class="preparation-layout">
        <div class="preparation-photo"><figure><div class="photo-window"><img data-parallax="photo" src="assets/img/landing/pendampingan-guru-ai.webp" srcset="assets/img/landing/pendampingan-guru-ai-768.webp 768w, assets/img/landing/pendampingan-guru-ai.webp 1536w" sizes="(min-width: 768px) 44vw, 100vw" width="1536" height="1024" loading="lazy" decoding="async" alt="Ilustrasi guru BK mendampingi siswi membaca catatan pilihan kuliah."></div><figcaption>Rencanakan bersama.<br><em>Melangkah lebih terarah.</em></figcaption></figure><div class="counselor-access"><div><span>Ruang pendampingan</span><h3>Untuk guru BK</h3><p>Jalankan rekomendasi dan dampingi siswa membaca alasan di balik hasilnya.</p></div><a href="<?= e($accountUrl) ?>" class="counselor-link" aria-label="Masuk ke panel guru BK"><?= icon('arrow') ?></a></div></div>
        <div class="preparation-copy">
          <h2 class="section-title" id="panduan-title">Pilihanmu layak<br><em>dibicarakan.</em></h2>
          <p class="section-intro">Sebelum bertemu guru BK, luangkan waktu untuk menyiapkan ini. Centang yang sudah siap.</p>
          <fieldset class="preparation-checklist" data-checklist><legend>Persiapan konsultasi</legend>
            <label><input type="checkbox"><span class="check-mark" aria-hidden="true"></span><span><strong>Catatan nilai rapor</strong><small>Siapkan nilai yang dibutuhkan untuk pengolahan data.</small></span></label>
            <label><input type="checkbox"><span class="check-mark" aria-hidden="true"></span><span><strong>Hal yang kamu sukai</strong><small>Catat pelajaran dan aktivitas yang membuatmu tertarik.</small></span></label>
            <label><input type="checkbox"><span class="check-mark" aria-hidden="true"></span><span><strong>Bidang yang ingin dijelajahi</strong><small>Tulis rumpun atau program studi yang membuatmu penasaran.</small></span></label>
            <label><input type="checkbox"><span class="check-mark" aria-hidden="true"></span><span><strong>Pertanyaan untuk guru BK</strong><small>Mulai dari hal yang masih membuatmu ragu.</small></span></label>
          </fieldset>
          <div class="preparation-progress" data-checklist-progress hidden><div><span data-checklist-status role="status" aria-live="polite">0 dari 4 persiapan selesai</span><span>Daftar persiapanmu</span></div><progress value="0" max="4" aria-label="Progres persiapan konsultasi"></progress><p>Centang hanya untuk membantumu bersiap, tidak disimpan setelah halaman dimuat ulang.</p></div>
        </div>
      </div>
      <p class="preparation-reminder"><?= icon('cap') ?><span>Rekomendasi adalah bahan pertimbangan.<br><strong>Keputusan akhirnya tumbuh dari pemahaman tentang dirimu.</strong></span></p>
    </div>
  </section>
</main>

<footer class="landing-footer redesigned-footer" aria-label="Informasi dan kontak SIMINAT">
  <div class="container">
    <div class="closing-invitation"><div><p>Langkah kecil hari ini.</p><h2>Arah yang lebih jelas<br><em>untuk esok hari.</em></h2></div><div><p>Mulai percakapan dengan guru BK di madrasahmu. Bawa rasa ingin tahu dan rencana yang ingin kamu wujudkan.</p><a href="#panduan">Siapkan bahan konsultasimu <?= icon('arrow') ?></a></div></div>
    <div class="closing-identity"><div><?= brand() ?><p>Pendamping diskusi pilihan kuliah<br>untuk siswa dan guru BK.</p></div><div class="closing-schools"><p>Dua madrasah. Satu tujuan.</p><span>MAN 4 Matangkuli</span><span>MAS Al-Muslimun Lhoksukon</span></div></div>
    <div class="footer-navigation"><nav aria-label="Navigasi footer"><a href="#rumpun">Rumpun Jurusan</a><a href="#cara-kerja">Cara Kerja</a><a href="#metode">Metode ID3</a><a href="#panduan">Panduan</a></nav><a class="back-to-top" href="#beranda">Kembali ke atas <?= icon('arrow') ?></a></div>
    <div class="footer-wordmark" aria-hidden="true">SIMINAT<span>Kenali minat.<br>Rencanakan masa depan.</span></div>
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
