# AGENTS.md
Panduan untuk agen AI dan developer yang mengerjakan proyek SIMINAT.

## 1. Ringkasan Proyek

SIMINAT adalah web klasifikasi minat jurusan kuliah siswa kelas XII MAN 4 Matangkuli dan MAS Al-Muslimun Lhoksukon dengan algoritma ID3. Proyek ini bagian dari skripsi Nadia Umaira (Teknik Informatika, Universitas Malikussaleh).

- Rencana kerja: `docs/plan.md`
- Design system: `docs/design.md`
- Data sumber: `Dataset_Bersih_ID3.xlsx` dan log pembersihannya
- Script pembuat dataset: `scripts/build_dataset.py`

Baca ketiga dokumen itu sebelum menulis kode.

## 2. Stack Wajib

- PHP 8.1+ native. **Dilarang** memakai framework (Laravel, CodeIgniter, Slim) dan Composer.
- MySQL 8 / MariaDB lewat PDO.
- CSS murni dengan CSS variables dari `assets/css/tokens.css`. **Dilarang** Bootstrap dan Tailwind.
- Vanilla JavaScript ES6. **Dilarang** jQuery, React, Vue.
- Library CDN yang boleh: Lucide, Chart.js 4.4.1, Google Fonts (Plus Jakarta Sans, Inter). Selalu kunci versi.

## 3. Menjalankan Proyek

```bash
# 1. Salin folder ke htdocs XAMPP
#    C:\xampp\htdocs\siminat
# 2. Buat database
mysql -u root -e "CREATE DATABASE siminat CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root siminat < database/schema.sql
mysql -u root siminat < database/seed.sql
# 3. Salin config
cp config/config.example.php config/config.php
# 4. Buka http://localhost/siminat  (admin: /siminat/admin)
# 5. Uji mesin ID3
php tests/id3_manual_test.php
```

Akun awal (`seed.sql`): `admin` / `admin123`. Wajib diganti saat login pertama.

## 4. Aturan Kode

### PHP
1. `declare(strict_types=1);` di setiap file kelas.
2. Satu kelas per file. Nama kelas PascalCase, method camelCase, tabel dan kolom snake_case.
3. Query **hanya** lewat `Database::pdo()` dengan prepared statement. Jangan pernah menyisipkan `$_GET` atau `$_POST` ke string SQL.
4. Output ke HTML **selalu** lewat `e($teks)`. Untuk JSON gunakan `json_encode(..., JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE)`.
5. Form POST wajib berisi `<?= Csrf::field() ?>` dan controller wajib memanggil `Csrf::verify()`.
6. Controller tipis. Logika hitung ada di `app/services/`. Query ada di `app/models/`.
7. View hanya menampilkan data. Tidak ada query di view.
8. Error tampil ramah ke pengguna. Detail error masuk `storage/logs/app.log`.
9. Tanggal disimpan UTC, ditampilkan WIB (`Asia/Jakarta`).

### CSS
1. Pakai token dari `tokens.css`. Jangan menulis warna hex baru di file lain.
2. Penamaan kelas BEM ringan: `.card`, `.card__title`, `.card--feature`.
3. Mobile first: gaya dasar untuk layar kecil, lalu `@media (min-width: 768px)` dan seterusnya.
4. Tidak boleh ada scroll horizontal halaman di lebar 360px.

### JavaScript
1. Satu modul per fitur di `assets/js/`. Inisialisasi lewat atribut `data-*` (contoh `data-slider`, `data-counter`).
2. Ambil data dinamis dari endpoint JSON `index.php?api=...`, jangan dari teks HTML.
3. Hormati `prefers-reduced-motion`.

### Bahasa
- Semua teks antarmuka berbahasa Indonesia baku, kalimat pendek dan jelas.
- Komentar kode boleh Bahasa Indonesia.

## 5. Aturan Algoritma ID3

1. Entropy memakai log basis 2. `0 * log2(0)` dianggap 0.
2. Bila gain sama, pilih atribut dengan `urutan` terkecil di tabel `atribut`. Ini membuat hasil deterministik.
3. Cabang tanpa data jadi daun dengan kelas mayoritas induknya.
4. Nilai atribut yang tidak dikenal saat prediksi diarahkan ke kelas mayoritas node terakhir.
5. Setiap node menyimpan `distribusi`, `jumlah`, `entropy`.
6. Langkah perhitungan per node wajib disimpan di `model_langkah`.
7. K-fold memakai stratifikasi dan seed tetap (42).
8. Jangan mengubah aturan diskretisasi di kode. Ambil dari tabel `aturan_diskretisasi` (default Tinggi >= 95, Sedang 92 sampai < 95, Rendah < 92).

## 6. Data dan Privasi

1. Label: Saintek, Soshum, Campuran. Asal label berbeda per sekolah (MAN dari pilihan mapel TKA, MAS dari prodi SPAN-PTKIN). Jangan dicampur tanpa kolom `sumber_label`.
2. Import harus menghasilkan 247 siswa: 147 berlabel dan 100 tanpa label. Bila jumlah berbeda, hentikan dan laporkan.
3. Halaman publik hanya menampilkan data agregat. Nama, NIS, NISN, dan nama orang tua tidak boleh tampil di halaman publik.
4. Jangan menyimpan file data asli (foto formulir, legger) di folder publik.

## 7. Definisi Selesai (Definition of Done)

Sebuah tugas selesai bila:
- [ ] Kriteria selesai tahap di `plan.md` terpenuhi.
- [ ] Tidak ada error atau warning PHP (`error_reporting(E_ALL)` saat development).
- [ ] Tampilan dicek di lebar 360px, 768px, dan 1280px.
- [ ] Form punya validasi server dan pesan error.
- [ ] Query memakai prepared statement, output memakai `e()`.
- [ ] `php tests/id3_manual_test.php` lulus bila menyentuh mesin ID3.
- [ ] Tidak ada teks contoh (lorem ipsum) atau angka statis yang seharusnya dinamis.

## 8. Yang Harus Dihindari

- Menyalin nama, logo, atau foto dari referensi "Sekolah Nusantara".
- Menambah library baru tanpa mencatatnya di `plan.md`.
- Menyimpan password tanpa `password_hash`.
- Menghitung ulang model setiap kali halaman publik dibuka. Pakai model aktif yang tersimpan.
- Mengubah struktur tabel tanpa memperbarui `database/schema.sql` dan file migrasi.

## 9. Urutan Kerja yang Disarankan

Ikuti tahap 0 sampai 10 di `plan.md`. Untuk setiap tahap:
1. Baca kriteria selesai.
2. Tulis kode sekecil mungkin yang memenuhi kriteria.
3. Uji di browser dan, bila perlu, lewat CLI.
4. Catat perubahan di `CHANGELOG.md` (tanggal, tahap, ringkasan).

## 10. Referensi Cepat

| Butuh | Lihat |
|---|---|
| Skema tabel | `plan.md` bagian 6 dan `database/schema.sql` |
| Warna, font, komponen | `design.md` bagian 2 sampai 6 |
| Daftar halaman | `plan.md` bagian 9 |
| Keputusan pembersihan data | `Dataset_Bersih_ID3.xlsx` sheet `Log_Pembersihan` |
