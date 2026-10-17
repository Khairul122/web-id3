# Validasi hero SIMINAT

Tanggal: 17 Oktober 2026. Status: PASS untuk scope hero awal.

## Bukti pemeriksaan

- PHP: `php -l index.php`, tanpa kesalahan sintaks.
- JavaScript: `node --check assets/js/hero.js`, lulus.
- Runtime: Chromium melalui Playwright, Apache Laragon, `http://localhost/web-id3/`.
- Responsif: 360×800, 390×844, 768×1024, 1024×768, 1280×720, 1440×900, 1920×1080. Tidak ada overflow horizontal. Pada desktop, tinggi halaman tepat satu layar; pada ponsel, pita informasi berlanjut vertikal.
- Seluruh pemicu dialog desktop dan mobile diuji. Tombol tutup, kembali, Escape, backdrop, Tab, serta pengembalian fokus lulus.
- Zero JavaScript errors dan zero HTTP error pada aset halaman.
- Axe-core 4.10.3: tidak ada violations pada hero, dialog, dan menu, dengan aturan WCAG A/AA dan 2.1 AA. Ini bukan sertifikasi keseluruhan situs.
- Pemeriksaan tambahan kontras foto: 288 baris teks pada enam viewport/state, minimum 5,04:1. Teks disembunyikan sementara di browser pengujian untuk membaca latar aktual; file foto dan source halaman tidak diubah oleh pemeriksaan ini.
- Axe menandai kontras foto sebagai perlu pemeriksaan tambahan, sudah diperiksa melalui pengukuran tersebut. Relasi aria-controls pada dialog menu tertutup diverifikasi manual: elemen `#mobile-menu` ada dan terbuka melalui pemicunya.
- Reduced motion: transisi menjadi 0 detik dan scroll menjadi auto.
- HTTP 403 untuk direktori data, dokumentasi, script, output pengujian, workbook, dan PDF sumber melalui Apache.
- Screenshot dan data rinci tersedia di `output/qa/`. Hasil responsif: `results.json`, aksesibilitas: `accessibility.json`, kontras: `contrast.json`.

## Delivery Gate antislop

### Hard Gate

- R-02 PASS: source antarmuka tidak memuat em dash; salinan teks diperiksa.
- R-03 PASS: scrollWidth sama dengan viewport pada seluruh tujuh ukuran pengujian.
- R-17 PASS: hero tidak menampilkan statistik atau persentase model yang belum tersedia.
- R-18 PASS: tidak ada testimoni atau identitas siswa fiktif yang dipresentasikan sebagai nyata.
- R-23 PASS: referensi pengguna mengarahkan navigasi dan foto; logo buku merupakan identitas SIMINAT buatan, bukan logo resmi sekolah; foto diberi label ilustrasi AI.
- R-24 PASS: Beranda menuju halaman aktif; menu lain adalah tombol dialog, tanpa tautan halaman yang belum tersedia.
- R-25 PASS: pengukuran latar aktual menghasilkan kontras minimum 5,04:1, melewati ambang dokumen 4,5:1.
- R-26 PASS: setiap kontrol diuji membuka/menutup dialog, menu, atau menuju Beranda.
- R-27 PASS: tidak ada pemuatan data asinkron; fallback foto adalah navy dan fallback font tersedia. Pemberitahuan noscript menjelaskan kebutuhan JavaScript.
- R-28 PASS: tidak ada FAQ tambahan pada scope hero.
- R-32 PASS: penguncian Tab dan Shift+Tab, Escape, indikator focus-visible, dan pengembalian fokus tersedia dan diperiksa.
- R-33 PASS: fitur ditulis pada index.php, hero.css, tokens.css, dan hero.js. Instrumentasi browser hanya untuk QA.
- R-34 PASS: tidak ada toggle tema; hero menggunakan palet referensi yang tetap.
- R-35 PASS: halaman dijalankan melalui Apache; seluruh kontrol serta tujuh viewport diperiksa.
- R-36 PASS: tidak ada klaim akurasi, keamanan tersertifikasi, atau penggunaan oleh pelanggan.
- R-37 PASS: arah visual berasal dari referensi pengguna; ENERGY 2 / RHYTHM 2 / MOTION 1 tercatat di hero-implementation.md.
- R-38 PASS: foto diberi label ilustrasi AI; dialog menyatakan klasifikasi masih dikembangkan, tanpa hasil prediksi buatan.

### Purpose Gate

- R-01 PASS: gradasi navy berfungsi menjaga kontras foto, diverifikasi 5,04:1 minimum.
- R-04 PASS: ikon buku, percabangan pohon, dan wisuda mewakili nilai rapor, ID3, dan bimbingan pendidikan.
- R-06 PASS: Georgia mengikuti judul serif editorial referensi; Inter dipakai untuk keterbacaan navigasi dan isi. Alasan dicatat di hero-implementation.md.
- R-07 PASS: tidak ada grid atau pola dekoratif di latar.
- R-08 PASS: panah pada CTA utama menunjukkan pembukaan penjelasan; chevron pada menu mobile menunjukkan item yang dapat dibuka.
- R-09 PASS: tidak ada capsule badge atau status rekaan di atas judul.
- R-10 PASS: blur hanya pada backdrop dialog; navbar dan panel tidak memakai glassmorphism bertumpuk.
- R-12 PASS: tidak ada bayangan besar pada setiap komponen.
- R-13 PASS: tidak ada efek glow.
- R-14 PASS: tiga panel informasi setara mengikuti referensi dan menjelaskan tiga dasar pendekatan; tidak berupa kartu bertumpuk.
- R-19 PASS: hanya transisi hover 180 ms; tidak ada autoplay, animasi mengambang, atau gerak berulang.
- R-22 PASS: foto kelas menghubungkan hero dengan konteks siswa madrasah; status ilustrasi AI terlihat.

### Liveliness

- Dial PASS: ENERGY 2 / RHYTHM 2 / MOTION 1 dinyatakan dan terlihat melalui foto, judul editorial, serta gerak minimal.
- Fokus PASS: judul besar dan CTA utama menjadi pusat perhatian di kiri; panel dasar pendekatan berada di bawah.
- Whitespace PASS: jarak judul, deskripsi, dan aksi dibedakan; desktop 1280×720 tetap satu layar.
- Aksen PASS: kuning menghubungkan judul penekanan dan aksi utama dalam palet navy/ivory.
- Motif PASS: buku pada identitas dan panel mengikat tema pendidikan.
- Design Read PASS: arah editorial institusi pendidikan untuk siswa XII dan guru BK tercatat sebelum penyelesaian implementasi.

### Craftsmanship dan Quality Locks

- C-1 PASS: keputusan warna, tipografi, panel, ikon, dan overlay memiliki alasan dalam hero-implementation.md.
- C-2 PASS: click-through kontrol lulus pada desktop dan mobile.
- C-3 PASS: halaman hanya memiliki hero sesuai permintaan pengguna.
- C-4 PASS: tujuh ukuran layar, dialog, keyboard, dan reduced motion diuji.
- C-5 PASS: tidak ada statistik atau testimoni rekaan.
- R-05 PASS: komposisi berasal dari referensi spesifik pengguna; tidak menambahkan section template.
- R-11 PASS: tombol bersudut 3px dan dialog 6px; tidak semua elemen dibuat pill.
- R-15 PASS: CTA menyebut tindakan konkret, yaitu mengenali rumpun jurusan.
- R-16 PASS: teks tidak memakai klaim pemasaran AI generik.
- R-20 PASS: nama dua sekolah, rumpun jurusan, nilai rapor, dan ID3 memberi konteks khusus SIMINAT.
- R-21 PASS: latar gelap mendukung foto dan keterbacaan sesuai referensi pengguna.
- R-29 PASS: palet navy, ivory, dan satu aksen kuning; seluruh warna CSS berada pada tokens.css.
- R-30 PASS: identitas SIMINAT dan foto baru tidak menyalin branding Al-Izzah.
- R-31 PASS: keputusan utama dijelaskan dalam catatan implementasi dan dikonfirmasi melalui screenshot.

## Batas scope

Ini implementasi hero beserta navigasi dan dialog penjelasannya. Database, autentikasi, training ID3, dan prediksi belum dibuat. Pengujian runtime dilakukan pada Chromium. Tidak ada klaim pengujian langsung Safari atau Firefox.
