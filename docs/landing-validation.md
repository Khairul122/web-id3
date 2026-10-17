# Validasi section landing SIMINAT

Status: PASS. Tanggal: 17 Oktober 2026. Acuan: docs/plan-landing-sections.md.

## Implementasi

Hero tetap menjadi pembuka. Section Rumpun Jurusan, Cara Kerja, Metode ID3, Panduan, dan footer ditambahkan sesuai urutan plan. Dua foto baru dibuat menggunakan GPT Image, dioptimalkan ke WebP dengan varian 768/1536 px, dan diberi label ilustrasi AI. Diagram metode dibuat sebagai SVG aksesibel, bukan gambar pohon hasil penelitian. Rumus berupa teks HTML dengan tanda minus U+2212.

Navigasi desktop, menu ponsel, CTA hero, dan footer menggunakan tujuan section yang ada. Menu ponsel menutup saat tautan dipilih. Login/Panel Saya dan CTA guru BK menuju autentikasi yang sudah tersedia. Dialog informasi serta template lama dihapus. Index tidak lagi memanggil ModelId3/Evaluasi atau menampilkan ringkasan model aktif.

## Pengujian

- `php -l index.php`: PASS.
- `node --check assets/js/hero.js`: PASS.
- `git diff --check`: PASS, tanpa whitespace errors.
- `python output/qa/verify_hero.py`: PASS. Entry point kompatibilitas menjalankan tests/landing_sections_test.py agar pemeriksaan tersimpan dalam satu tempat.
- Viewport: 360×800, 390×844, 768×1024, 1024×768, 1280×720, 1440×900, 1920×1080. Tanpa overflow horizontal; seluruh menu mencapai target. Hero tetap muat satu layar pada ukuran desktop yang diperiksa.
- Semua href fragment memiliki target unik. CTA hero, navigasi footer, kembali ke atas, dan skip-link diklik. Menu ponsel diuji dengan Tab, Escape, tombol tutup, dan backdrop. Reduced motion dan halaman tanpa JavaScript diperiksa.
- Semua gambar yang digunakan berhasil dimuat; tidak ada console errors atau HTTP asset failures.
- `python tests/auth_browser_test.py`: PASS. Alur login guru BK, dashboard, perubahan kata sandi sukarela, logout, dan navigasi akun tetap berjalan. Tautan section publik juga diperiksa pada alur tersebut.
- Axe-core 4.10.3: zero violations untuk WCAG A/AA dan 2.1 AA pada landing dan menu di tiga viewport. Pemeriksaan otomatis menandai foto sebagai perlu verifikasi kontras tambahan. Relasi aria-controls menu tertutup diperiksa langsung: target #mobile-menu tersedia dan terbuka melalui pemicunya.
- Pengukuran kontras tambahan pada latar aktual: 986 baris teks, minimum 4,98:1, tanpa hasil di bawah 4,5:1. Label SVG menggunakan ivory/gold/muted pada navy; warna tidak menjadi satu-satunya pembeda informasi.
- Functional tests menjalankan CSP aplikasi secara normal. Context terpisah dengan bypass CSP hanya digunakan untuk instrumentasi axe/kontras dan screenshot section panjang. Tidak ada pelonggaran CSP pada aplikasi.
- Screenshot desktop dan ponsel diperiksa secara visual. Bukti: output/qa/landing-results.json, landing-accessibility.json, contrast.json, auth-browser-results.json, dan landing-section-*.png.

## Delivery Gate antislop

- R-02 PASS: salinan landing tidak memuat em dash. Minus pada rumus memakai U+2212.
- R-03 PASS: scrollWidth tidak melebihi viewport pada tujuh ukuran layar.
- R-17 PASS: tidak ada jumlah siswa, nilai siswa, persentase akurasi, atau hasil model. Angka 01 sampai 04 hanya urutan penjelasan.
- R-18 PASS: tidak ada testimoni atau profil orang fiktif yang dipresentasikan sebagai nyata.
- R-23 PASS: dua foto dibuat atas permintaan pengguna; semua diberi label AI. Identitas buku SIMINAT tetap memakai logo lokal yang sudah tersedia.
- R-24 PASS: setiap tautan fragment memiliki satu target; link akun diperiksa melalui tes autentikasi.
- R-25 PASS: pengukuran tambahan minimum 4,98:1; teks gold pada latar terang menggunakan gold-ink.
- R-26 PASS: seluruh navigasi, CTA, menu, skip-link, dan kembali ke atas berfungsi. Skema pohon merupakan gambar penjelasan statis, bukan kontrol interaktif.
- R-27 PASS: section tidak memuat data asinkron. Gambar memiliki dimensi dan latar fallback; konten serta tautan tetap tersedia tanpa JavaScript.
- R-28 PASS: tidak menambahkan FAQ yang tidak diminta.
- R-32 PASS: focus-visible, skip-link, focus trap menu, Escape, dan fokus section tujuan diperiksa di browser.
- R-33 PASS: HTML, CSS, SVG, dan JavaScript ditulis dalam source. Instrumentasi screenshot tidak mengubah aplikasi.
- R-34 PASS: tidak ada toggle tema. Latar terang isi dan latar navy metode/footer diperiksa pada desktop dan ponsel.
- R-35 PASS: lint, pengujian browser, screenshot, pemeriksaan kontras, dan regresi autentikasi dijalankan.
- R-36 PASS: tidak menambah klaim keamanan, performa, sertifikasi, akurasi, atau penggunaan yang tidak bersumber.
- R-37 PASS: Design Read dan ENERGY 2 / RHYTHM 2 / MOTION 1 ditetapkan dalam plan sebelum implementasi.
- R-38 PASS: foto diberi label ilustrasi AI dan skema pohon diberi label bukan hasil data siswa.
- R-01 PASS: overlay hero menjaga kontras foto; section isi memakai warna solid.
- R-04 PASS: ikon buku, pohon, dan wisuda tetap sesuai konteks pendidikan; panah hanya memberi arah navigasi.
- R-06 PASS: Georgia meneruskan tipografi editorial hero, Inter dipakai untuk isi dan navigasi. Nomor besar mewakili urutan alur.
- R-07 PASS: tidak ada grid dekoratif, blueprint, atau pola titik di latar.
- R-08 PASS: panah CTA menunjukkan kelanjutan ke penjelasan/panel; panah kembali ke atas menunjukkan arah yang sesuai.
- R-09 PASS: tidak ada capsule badge atau indikator status buatan pada section baru.
- R-10 PASS: tidak menambahkan glassmorphism; blur hanya pada backdrop menu yang sudah ada.
- R-12 PASS: section baru tidak menggunakan shadow besar atau kartu mengambang.
- R-13 PASS: tidak ada glow.
- R-14 PASS: definisi memakai baris bergaris, proses memakai timeline, panduan memakai daftar dan blok guru BK; tidak menyeragamkan konten menjadi feature cards.
- R-19 PASS: tidak menambahkan autoplay atau animasi berulang; prefers-reduced-motion menonaktifkan gerak.
- R-22 PASS: foto membaca panduan dan pendampingan guru BK memiliki hubungan langsung dengan isi section.
- Dial PASS: foto memberi ENERGY 2, variasi baris/timeline/diagram/daftar memberi RHYTHM 2, gerak minimal memberi MOTION 1.
- Fokus PASS: setiap section memiliki satu judul utama; diagram dan foto mendukung penjelasan.
- Whitespace PASS: jarak judul, paragraf, baris definisi, dan langkah memakai hierarki konsisten. Tampilan ponsel diperiksa terpisah.
- Aksen PASS: satu warna kuning mengikat identitas dan elemen penting; varian gold-ink menjaga kontras pada ivory/paper.
- Motif PASS: tipografi Georgia, nomor editorial, dan konteks madrasah mempertahankan identitas SIMINAT.
- Design Read PASS: landing informasi publik bagi siswa XII, orang tua, dan guru BK, sesuai plan yang dibaca sebelum generasi konsep.
- C-1 PASS: keputusan layout, warna, foto, diagram, dan responsivitas dicatat di hero-implementation.md.
- C-2 PASS: kontrol yang terlihat memiliki tujuan dan sudah diuji. Tanpa JavaScript, pemicu menu disembunyikan dan navigasi alternatif tersedia.
- C-3 PASS: section mengikuti isi dan urutan plan; tidak menambahkan pricing, testimoni, atau section pengisi.
- C-4 PASS: desktop, ponsel, keyboard, menu, reduced motion, dan noscript diperiksa.
- C-5 PASS: tidak ada testimoni atau statistik buatan.
- R-05 PASS: komposisi bervariasi menurut jenis isi yang direncanakan; tidak menambah template halaman generik.
- R-11 PASS: foto dan tombol memakai radius kecil 3px; tidak membuat semua elemen menjadi pill.
- R-15 PASS: CTA konkret Kenali Rumpun Jurusan, Masuk ke Panel, dan Kembali ke atas.
- R-16 PASS: tidak menambahkan buzzword pemasaran AI pada isi halaman.
- R-20 PASS: rumpun kuliah, ID3, guru BK, dan dua madrasah menjadi konteks yang khusus untuk SIMINAT.
- R-21 PASS: metode navy menjadi jeda ritme teknis; section baca panjang memakai ivory/paper.
- R-29 PASS: landing.css memakai token warna yang sudah ada, tanpa hex baru.
- R-30 PASS: identitas SIMINAT tetap; foto baru dan section tidak menyalin branding referensi sekolah lain.
- R-31 PASS: alasan keputusan desain tercatat dan dibandingkan dengan konsep per section serta screenshot runtime.

## Batas pemeriksaan

Pengujian runtime menggunakan Chromium. Hasil otomatis dan pemeriksaan kontras tidak merupakan sertifikasi WCAG. Tidak ada perubahan pada mesin ID3, data penelitian, akun, atau kebijakan kata sandi dalam pekerjaan landing ini.
