# Validasi login dan foto baru

Tanggal: 17 Oktober 2026. Status: PASS untuk login, navigasi, dan latar login baru.

## Hasil

- PHP lint pada entry point, controller, model, core, dan view: PASS.
- JavaScript syntax check: PASS.
- `python tests/auth_http_test.py`: PASS. Mencakup validasi kosong/array, input SQL injection, escaping XSS, token CSRF, login admin langsung ke ruang kerja, rotasi session ID, ganti kata sandi sukarela, invalidasi sesi lain setelah password berubah, akses terlindungi, logout POST, dan rate limit tersimpan di database. Fixture masih membawa flag lama bernilai 1 untuk membuktikan login tidak memaksa penggantian.
- `python tests/auth_browser_test.py`: PASS. Mencakup login guru BK langsung ke ruang kerja, toggle kata sandi, bantuan keyboard, validasi visual, pergantian kata sandi melalui tautan yang dipilih pengguna, logout, navigasi Masuk/Panel Saya, serta domain localhost/subfolder dan web-id3.test.
- Login diperiksa di 360, 390, 768, 1280, dan 1440 px tanpa overflow horizontal. Zero JavaScript errors.
- Regresi hero melalui `output/qa/verify_hero.py`: PASS pada tujuh viewport 360 sampai 1920 px.
- Axe-core 4.10.3: zero violations pada login, bantuan, dan validasi form di tiga viewport. Bagian kontras foto memerlukan pengukuran tambahan.
- Pengukuran tambahan foto login final: 137 baris teks, minimum kontras 5,20:1 pada enam viewport/state. Melebihi ambang 4,5:1. Instrumentasi QA melewati CSP hanya dalam context browser pengujian; CSP aplikasi tetap dibatasi origin sendiri.
- Akses HTTP ke app, views, config, database, storage, tests, dan .gitignore menghasilkan 403. Aset foto login menghasilkan 200.
- Database `siminat`: tabel `users` dan `login_attempts` tersedia. Migrasi 002 menonaktifkan flag kewajiban penggantian dan menetapkan default 0. Hash akun admin tidak berubah dan tetap cocok dengan kata sandi awal. Tidak ada akun fixture tersisa setelah pengujian.
- Foto baru GPT Image tersimpan di assets/img/auth/bimbingan-karier-ai.webp, 1122×1402, 100.258 byte. Screenshot final di output/qa/login-*.png.

## Delivery Gate

- R-02 PASS: teks menggunakan bahasa Indonesia langsung dan tidak menambahkan em dash.
- R-03 PASS: lima viewport login dan tujuh viewport landing tanpa scroll horizontal.
- R-17 PASS: halaman tidak memuat statistik atau angka akurasi rekaan.
- R-18 PASS: tidak ada testimoni atau identitas fiktif yang diklaim nyata.
- R-23 PASS: foto baru diminta pengguna dan dibuat dengan GPT Image; logo tetap identitas SIMINAT yang sudah digunakan.
- R-24 PASS: tautan Masuk, Panel Saya, Kembali ke Beranda, dan Ganti kata sandi diuji sampai tujuan.
- R-25 PASS: pengukuran kontras foto baru minimum 5,20:1; field, placeholder, dan tombol memakai token teks kontras tinggi.
- R-26 PASS: semua kontrol login, bantuan, visibility password, navigasi, perubahan password, dan logout memiliki perilaku yang diuji.
- R-27 PASS: validasi server, pesan gagal kredensial, rate limit, error CSRF, serta status submit tersedia; exception menampilkan pesan umum.
- R-28 PASS: tidak menambah FAQ yang tidak diperlukan.
- R-32 PASS: fokus validasi, focus-visible, Tab/Shift+Tab pada bantuan, Escape, dan pengembalian fokus diuji.
- R-33 PASS: perilaku aplikasi ditulis di source PHP/CSS/JS; instrumentasi browser hanya untuk pemeriksaan.
- R-34 PASS: halaman login memakai ivory dan navy tetap; tidak ada toggle tema yang belum berfungsi.
- R-35 PASS: lint, tes HTTP, browser, screenshot, dan regresi landing dijalankan.
- R-36 PASS: tidak ada klaim sertifikasi, akurasi, atau jaminan keamanan yang dibuat-buat.
- R-37 PASS: design reference login dibuat sebelum implementasi; ENERGY 2 / RHYTHM 2 / MOTION 1 tercatat.
- R-38 PASS: foto diberi label ilustrasi AI di desktop dan mobile; ruang kerja awal menyatakan fitur siswa/ID3 masih dikembangkan.
- R-01 PASS: overlay foto menjaga teks tetap terbaca; permukaan form menggunakan warna solid.
- R-04 PASS: ikon mata, panah navigasi, dan buku berhubungan langsung dengan tindakan dan konteks pendidikan.
- R-06 PASS: Georgia dan Inter mempertahankan tipografi hero yang disetujui pengguna; ukuran heading dan body berbeda sesuai hierarki.
- R-07 PASS: tidak ada latar grid atau pola teknis generik.
- R-08 PASS: panah pada tombol utama menunjukkan kelanjutan alur; panah kembali menunjukkan navigasi balik.
- R-09 PASS: tidak ada badge capsule atau status rekaan di atas form.
- R-10 PASS: tidak ada glassmorphism bertumpuk; blur terbatas pada backdrop dialog bantuan.
- R-12 PASS: form terletak pada permukaan ivory tanpa kartu atau bayangan berlapis.
- R-13 PASS: tidak ada glow.
- R-14 PASS: form mengikuti urutan dua field login; tidak menggunakan feature cards tambahan.
- R-19 PASS: gerak hanya transisi tombol; prefers-reduced-motion menonaktifkannya.
- R-22 PASS: foto konsultasi guru dan siswa sesuai konteks pendampingan karier.
- Dial PASS: foto menjadi elemen visual utama, form tenang, gerak minimal, sesuai ENERGY 2 / RHYTHM 2 / MOTION 1.
- Fokus PASS: judul dan form menempati panel kanan desktop; mobile satu kolom.
- Whitespace PASS: jarak label-field, heading-intro, dan aksi-bantuan berbeda menurut hubungan konten.
- Aksen PASS: kuning digunakan pada identitas dan penekanan copy, tombol form memakai navy kontras tinggi.
- Motif PASS: identitas buku serta fotografi pendampingan mempertahankan karakter SIMINAT.
- Design Read PASS: halaman editorial institusi pendidikan untuk administrator dan guru BK, tercatat di login-implementation.md.
- C-1 PASS: warna, tipografi, foto, overlay, dan layout dijelaskan dalam catatan implementasi.
- C-2 PASS: semua kontrol terlihat diuji; tanpa JavaScript, tombol khusus JS disembunyikan dan login server tetap tersedia.
- C-3 PASS: halaman hanya memuat login dan kebutuhan akun yang langsung mendukung alur tersebut.
- C-4 PASS: login, dialog, validasi, serta alur admin/guru BK diperiksa di browser; aset tidak membutuhkan CDN saat runtime.
- C-5 PASS: tidak ada angka penggunaan atau hasil model buatan.
- R-05 PASS: komposisi dua panel mengikuti konsep khusus login; tidak menambah section template.
- R-11 PASS: field dan tombol radius 4px, dialog 6px; tidak memakai pill untuk semua elemen.
- R-15 PASS: CTA konkret: Masuk ke SIMINAT, Simpan Kata Sandi, dan Kembali ke Beranda.
- R-16 PASS: tidak ada slogan pemasaran AI generik atau klaim berlebihan.
- R-20 PASS: role guru BK/admin, identitas SIMINAT, dan foto konsultasi memberi konteks khusus aplikasi.
- R-21 PASS: foto navy mempertahankan identitas hero; form terang membantu keterbacaan.
- R-29 PASS: warna CSS tambahan didefinisikan dalam tokens.css.
- R-30 PASS: foto login baru dan identitas SIMINAT tidak menyalin branding referensi sekolah lain.
- R-31 PASS: keputusan visual dan autentikasi dicatat beserta bukti tes dan screenshot.

## Batas pengujian

Runtime browser menggunakan Chromium. Pemeriksaan otomatis dan pengukuran kontras bukan sertifikasi WCAG. Database saat ini memuat fondasi akun; fitur pengelolaan siswa, model, dan evaluasi belum diimplementasikan.
