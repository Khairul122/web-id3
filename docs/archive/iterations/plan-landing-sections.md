# Rencana: Section Tambahan Landing Page

Status: selesai diimplementasikan. Antislop diterapkan selama pengerjaan; hasil verifikasi dan Delivery Gate dicatat di docs/archive/iterations/landing-validation.md. Foto tambahan pada Rumpun Jurusan dan Panduan dibuat menggunakan GPT Image sesuai permintaan pengguna.

Design Read: landing informasi publik untuk siswa XII, orang tua, dan guru BK, gaya editorial institusi pendidikan yang meneruskan hero (navy, ivory, satu aksen kuning, judul Georgia, teks Inter lokal). Dial ENERGY 2 / RHYTHM 2 / MOTION 1.

Aturan isi: tanpa data siswa, nilai, jumlah data, atau angka hasil model. Semua teks bersifat penjelasan umum.

## Urutan halaman

| No | Section | Latar | Komposisi | Isi |
|---|---|---|---|---|
| 0 | Hero (sudah ada) | Foto + navy | Tetap | CTA utama diubah ke `#rumpun`, tautan "Bagaimana cara kerjanya?" ke `#cara-kerja` |
| 1 | `#rumpun` Rumpun jurusan | Ivory | Dua kolom: judul + pengantar di kiri, tiga baris definisi bergaris di kanan (bukan kartu) | Saintek, Soshum, Campuran; contoh bidang studi umum; Campuran dijelaskan sebagai pilihan yang mencakup kedua rumpun |
| 2 | `#cara-kerja` Cara kerja | Paper | Garis waktu 4 tahap bernomor tipografis (horizontal di desktop, vertikal di ponsel) | Data siswa, kategori nilai (tanpa menyebut angka batas), penelusuran pohon ID3, rekomendasi + jalur keputusan untuk diskusi dengan guru BK |
| 3 | `#metode` Metode ID3 | Navy (jeda ritme) | Dua kolom: penjelasan + rumus di kiri, skema pohon SVG di kanan | Rumus Entropy dan Gain (teks HTML, tanda minus U+2212), tiga kriteria berhenti, skema berlabel "Ilustrasi, bukan pohon hasil data siswa" |
| 4 | `#panduan` Panduan | Ivory | Dua kolom tidak simetris: langkah siswa (daftar bernomor) dan blok guru BK dengan tombol "Masuk ke Panel" | Catatan: rekomendasi bukan keputusan akhir |
| 5 | Footer kontak | Navy gelap | Tiga kelompok yang berbeda bentuk (bukan template 4 kolom) | Identitas SIMINAT, dua madrasah dan lokasinya, tautan section, ajakan bertanya ke guru BK masing-masing, label foto ilustrasi AI. Tanpa email/telepon karena tidak ada data resmi |

## Perubahan file

1. `index.php`
   - Hapus blok PHP ringkasan model aktif (`$modelSummary`) dan dialog info beserta semua `<template id="content-...">`.
   - Navigasi desktop dan menu ponsel: Beranda, Rumpun Jurusan, Cara Kerja, Metode ID3, Panduan, Masuk/Panel Saya, semuanya `href="#..."` ke section yang ada (R-24, R-26).
   - Tambahkan section 1 sampai 5 di bawah `</section>` hero, di dalam `<main>`; footer di luar `<main>`.
   - Perbarui teks `<noscript>`.
2. `assets/css/landing.css` (baru, dimuat setelah `hero.css`)
   - Section terang memakai `--paper`/`--ivory` dan teks `--ink`; teks kuning di latar terang memakai `--gold-ink` agar kontras 4.5:1.
   - Grid dua kolom mulai 768px, garis waktu 4 kolom mulai 1024px, satu kolom di ponsel tanpa scroll horizontal.
   - Tanpa bayangan besar, tanpa glow, tanpa glassmorphism, radius 3 sampai 6px seperti hero. Warna baru (bila perlu) ditambahkan ke `tokens.css`.
3. `assets/js/hero.js`
   - Hapus logika dialog info (`[data-dialog]`, `#info-dialog`).
   - Tautan di menu ponsel menutup dialog menu saat diklik, lalu menggulir ke section.
4. `output/qa/verify_hero.py` dan `tests/auth_browser_test.py`
   - Ganti pemeriksaan dialog info dengan pemeriksaan tautan section (setiap `href="#..."` punya target).
5. `CHANGELOG.md` dan `docs/archive/iterations/hero-implementation.md`: catat section baru dan alasan desainnya (R-31).

## Alasan desain singkat (R-31)

- Latar terang untuk section isi: teks panjang lebih mudah dibaca, sedangkan hero tetap gelap mengikuti foto.
- Metode ID3 di latar navy: satu jeda ritme yang menandai bagian teknis.
- Nomor tahap berupa angka Georgia, bukan ikon bulat: meneruskan motif tipografi editorial hero.
- Skema pohon ilustratif, bukan pohon asli: halaman umum tidak memuat hasil penelitian.

## Verifikasi (R-35)

- `php -l index.php`, `node --check assets/js/hero.js`.
- Playwright di 360, 390, 768, 1024, 1280, 1440 px: tanpa overflow, setiap tautan menu menuju section, menu ponsel terbuka dan tertutup (Escape, tombol, backdrop), tidak ada error konsol.
- Kontras teks di setiap section memenuhi WCAG AA; fokus keyboard terlihat.
- Jalankan ulang `python output/qa/verify_hero.py` dan `python tests/auth_browser_test.py`.
- Laporan Delivery Gate PASS/FAIL per item.
