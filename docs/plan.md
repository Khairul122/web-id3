# Implementation Plan
## Sistem Klasifikasi Minat Jurusan Kuliah dengan Algoritma ID3
MAN 4 Matangkuli dan MAS Al-Muslimun Lhoksukon

Dokumen ini adalah rencana kerja untuk membangun web dengan PHP native dan MySQL. Tampilan mengikuti referensi desain (lihat `design.md`). Aturan kerja untuk agen AI ada di `agents.md`.

---

## 1. Tujuan

1. Menyediakan web publik yang informatif tentang sistem, metode ID3, dan statistik data siswa.
2. Menyediakan panel admin untuk mengelola data siswa, menjalankan training ID3, melihat pohon keputusan, dan menguji akurasi.
3. Menyediakan fitur **Cek Rekomendasi**. Siswa atau guru BK mengisi nilai, lalu sistem memberi rekomendasi rumpun jurusan beserta jalur keputusannya.
4. Semua halaman responsif dan nyaman dipakai di ponsel.

## 2. Ruang Lingkup

| Masuk ruang lingkup | Di luar ruang lingkup |
|---|---|
| Web publik dan panel admin | Aplikasi mobile native |
| Import data dari `Dataset_Bersih_ID3.xlsx` (via CSV) | Integrasi langsung ke EMIS atau Dapodik |
| Training ID3, pohon keputusan, aturan IF-THEN | Algoritma lain (C4.5, Naive Bayes) |
| Pengujian k-fold, confusion matrix, akurasi | Pembayaran atau pendaftaran online |
| Prediksi tunggal dan prediksi massal | Multi bahasa |
| Cetak laporan (HTML print dan CSV) | |

## 3. Teknologi

| Lapisan | Pilihan | Alasan |
|---|---|---|
| Server | PHP 8.1+ native (tanpa framework) | Sesuai permintaan dan proposal |
| Database | MySQL 8 / MariaDB 10.6 (XAMPP) | Sesuai 3.3.2 proposal |
| Akses DB | PDO + prepared statement | Aman dari SQL injection |
| CSS | CSS murni dengan CSS variables, tanpa build | Mudah dipelihara, tanpa Node |
| JavaScript | Vanilla JS (ES6), tanpa framework | Ringan |
| Ikon | Lucide (CDN, versi terkunci) | Sama dengan gaya referensi |
| Grafik | Chart.js 4.4.1 (CDN) | Grafik statistik dan evaluasi |
| Font | Plus Jakarta Sans dan Inter (Google Fonts) | Sesuai referensi |

Catatan: proposal Bab I menulis "PHP dan MySql". Folder kerja saat ini bernama `flask-id3`. Sebaiknya Batasan Masalah ditulis "PHP native dengan basis data MySQL" agar konsisten.

## 4. Data Sumber

Sumber utama adalah `Dataset_Bersih_ID3.xlsx` (sheet `Data_ID3`, `Data_Numerik`, `Data_Tanpa_Label`).

| Item | Jumlah |
|---|---|
| Total siswa di legger | 247 (MAN 4: 51, MAS: 196) |
| Data berlabel (data latih dan uji) | 147 (MAN 4: 43, MAS: 104) |
| Data tanpa label (untuk prediksi) | 100 |

**Atribut ID3 (9):** Sekolah_Asal, Peminatan, Kat_Agama, Kat_BArab, Kat_Bahasa, Kat_MTK, Kat_MIPA, Kat_IPS, Kecenderungan.

**Label:** Minat_Jurusan = Saintek, Soshum, Campuran.

**Aturan diskretisasi nilai:** Tinggi >= 95, Sedang 92 sampai < 95, Rendah < 92, Tidak Ada bila mapel tidak diambil. Batas ini disimpan di tabel `aturan_diskretisasi` agar bisa diubah admin tanpa mengubah kode.

## 5. Struktur Folder

```
siminat/
├── index.php                 # router publik
├── config/
│   ├── config.php            # konstanta app, base URL
│   └── database.php          # koneksi PDO (singleton)
├── app/
│   ├── core/                 # Router, View, Session, Csrf, Auth, Flash, Validator
│   ├── models/               # Siswa, Atribut, Model, Prediksi, Evaluasi, Konten
│   ├── services/
│   │   ├── Id3.php           # entropy, gain, build tree, predict
│   │   ├── Discretizer.php   # nilai angka ke kategori
│   │   ├── Evaluator.php     # k-fold, confusion matrix, metrik
│   │   └── Importer.php      # import CSV
│   └── controllers/
│       ├── public/           # Home, Metode, Statistik, Rekomendasi, Kontak
│       └── admin/            # Dashboard, Siswa, Atribut, Training, Pohon, Pengujian, Prediksi, Laporan, Konten, User
├── views/
│   ├── layouts/              # public.php, admin.php, print.php
│   ├── partials/             # navbar, footer, sidebar, flash, pagination
│   ├── public/
│   └── admin/
├── admin/index.php           # router admin
├── assets/
│   ├── css/  (tokens.css, base.css, components.css, public.css, admin.css)
│   ├── js/   (app.js, slider.js, counter.js, tree.js, charts.js, table.js)
│   └── img/  (hero/, galeri/, logo/)
├── storage/
│   ├── uploads/              # file CSV import
│   └── logs/
├── database/
│   ├── schema.sql
│   ├── seed.sql              # admin default, atribut, aturan
│   └── seed_dataset.sql      # hasil konversi 247 siswa
├── tests/
│   ├── id3_manual_test.php   # bandingkan dengan hitungan manual
│   └── fixtures/sample_14.csv
└── .htaccess
```

## 6. Skema Database

```sql
users(id, nama, username UNIQUE, password_hash, role ENUM('admin','guru_bk'), last_login, created_at)

sekolah(id, kode ENUM('MAN','MAS') UNIQUE, nama, alamat)

siswa(id, kode_siswa UNIQUE, sekolah_id FK, kelas, no_absen, nama, nis, nisn, jk,
      peminatan, sumber_label, detail_label, jurusan_diterima, bukti_file,
      label ENUM('Saintek','Soshum','Campuran') NULL, created_at, updated_at)

nilai_mapel(id, siswa_id FK, kode_mapel, nilai TINYINT)            -- QH, AA, MTK, BIO, ...
nilai_rata(siswa_id PK FK, rata_agama, nilai_barab, rata_bahasa, nilai_mtk,
           rata_mipa, rata_ips, rata_umum, kecenderungan)

atribut(id, kode UNIQUE, nama, deskripsi, aktif TINYINT, urutan)
atribut_nilai(id, atribut_id FK, nilai)                               -- domain tiap atribut
siswa_atribut(siswa_id FK, atribut_id FK, nilai, PRIMARY KEY(siswa_id, atribut_id))

aturan_diskretisasi(id, batas_tinggi DECIMAL(5,2), batas_sedang DECIMAL(5,2), aktif)

model_id3(id, nama, jumlah_data, atribut_dipakai JSON, tree_json LONGTEXT,
          jumlah_node, kedalaman, dibuat_oleh FK, aktif TINYINT, created_at)
model_langkah(id, model_id FK, node_path, entropy_parent, detail_gain JSON, atribut_terpilih)
model_aturan(id, model_id FK, no_aturan, kondisi TEXT, kelas, support INT)

evaluasi(id, model_id FK, metode ENUM('kfold','holdout'), k_atau_rasio, akurasi,
         precision_macro, recall_macro, f1_macro, confusion_json, detail_fold_json, created_at)

prediksi(id, model_id FK, sumber ENUM('form','siswa','massal'), siswa_id NULL,
         input_json, hasil, jalur_json, created_at)

konten_pengumuman(id, judul, slug, isi, gambar, terbit TINYINT, created_at)
konten_galeri(id, judul, gambar, urutan)
konten_slider(id, judul, subjudul, gambar, cta_label, cta_url, urutan, aktif)
pengaturan(kunci PK, nilai)                                            -- nama situs, kontak, statistik hero
pesan_kontak(id, nama, email, pesan, dibaca, created_at)
```

Index penting: `siswa(sekolah_id, label)`, `siswa_atribut(atribut_id, nilai)`, `prediksi(created_at)`.

## 7. Desain Algoritma ID3 (app/services/Id3.php)

```
entropy(rows) = - Σ p(c) * log2 p(c)
gain(rows, A) = entropy(rows) - Σ (|Sv|/|S|) * entropy(Sv)

build(rows, attrs, depth):
  jika semua label sama          -> leaf(label)
  jika attrs kosong / rows < min -> leaf(mayoritas)
  pilih A dengan gain tertinggi (seri: urutan atribut)
  simpan langkah (entropy, semua gain) ke log
  untuk setiap nilai v di domain A:
     Sv kosong -> leaf(mayoritas rows)
     selain itu -> child = build(Sv, attrs - A, depth+1)
predict(tree, x): telusuri cabang; nilai tak dikenal -> mayoritas node
```

Keputusan teknis:
1. Tree disimpan sebagai JSON: `{"atribut":"Peminatan","distribusi":{...},"cabang":{"MIPA":{...}}}`.
2. Setiap node menyimpan distribusi kelas. Distribusi dipakai untuk fallback dan tooltip di UI.
3. Langkah perhitungan (entropy dan gain per node) disimpan di `model_langkah`. Halaman Training menampilkan tabel ini agar mudah dicocokkan dengan perhitungan manual Bab IV.
4. Opsi pre-pruning: `min_sampel` (default 2) dan `max_depth` (default tanpa batas). Opsi ini sesuai pembahasan overfitting di Bab II.
5. Aturan IF-THEN dibentuk dari semua jalur akar ke daun.

## 8. Evaluasi

1. **Stratified k-fold** (default k = 5, bisa 3 sampai 10). Data latih hanya 147, jadi k-fold lebih stabil daripada satu kali split.
2. **Metrik:** akurasi, precision, recall, dan F1 per kelas dan macro, plus confusion matrix 3 x 3.
3. **Pilihan skenario:** semua data, MAN saja, MAS saja, atau tanpa kelas Campuran. Ini menjawab ketidakseimbangan kelas di data MAS.
4. **Seed acak tetap (42)** agar hasil bisa diulang dan dilaporkan di skripsi.

## 9. Daftar Halaman

### Publik (`index.php?page=...` atau URL rapi via .htaccess)
| Halaman | Isi | Data dinamis |
|---|---|---|
| Beranda | Hero slider, statistik, keunggulan sistem, alur kerja, ringkasan hasil, CTA | slider, pengaturan, hitung siswa, akurasi model aktif |
| Tentang | Latar belakang, tujuan, profil 2 sekolah | pengaturan, sekolah |
| Metode ID3 | Penjelasan entropy, gain, contoh hitung interaktif | atribut, model_langkah |
| Statistik | Grafik distribusi label, per sekolah, per peminatan | siswa, siswa_atribut |
| Cek Rekomendasi | Form nilai, hasil rekomendasi, jalur keputusan | model aktif |
| Informasi | Daftar pengumuman, detail | konten_pengumuman |
| Galeri | Grid foto dan lightbox | konten_galeri |
| Kontak | Form pesan, alamat, peta embed | pesan_kontak |

### Admin (`/admin`)
| Menu | Fungsi |
|---|---|
| Dashboard | Kartu ringkasan, grafik label, model aktif, prediksi terbaru |
| Data Siswa | CRUD, filter sekolah/kelas/label, cari, pagination, detail nilai |
| Import Data | Upload CSV, pratinjau 10 baris, validasi, laporan baris gagal |
| Atribut | Aktif/nonaktif atribut, urutan, domain nilai, batas diskretisasi |
| Training ID3 | Pilih atribut dan skenario data, jalankan, tabel langkah entropy/gain |
| Pohon Keputusan | Visual pohon interaktif, zoom, lipat cabang, daftar aturan IF-THEN |
| Pengujian | k-fold, confusion matrix, metrik, grafik per fold, riwayat evaluasi |
| Prediksi | Prediksi siswa tanpa label (massal) dan riwayat |
| Laporan | Cetak data, pohon, aturan, evaluasi; export CSV |
| Konten | Slider, pengumuman, galeri, pengaturan situs |
| Pengguna | Kelola admin dan guru BK |

## 10. Tahapan Kerja

Setiap tahap punya kriteria selesai. Tahap berikutnya dimulai setelah kriteria terpenuhi.

### Tahap 0. Persiapan (0,5 hari)
- Buat struktur folder, `.htaccess`, `config.php`, koneksi PDO.
- Buat `schema.sql` dan `seed.sql` (admin default, 2 sekolah, 9 atribut, aturan diskretisasi).
- **Selesai bila:** `http://localhost/siminat` tampil halaman kosong tanpa error, dan DB terbuat dari `schema.sql`.

### Tahap 1. Core dan keamanan (1 hari)
- Router sederhana, View renderer dengan layout, helper `e()` untuk escape.
- Session aman, login/logout, `password_hash`, middleware Auth dan role.
- CSRF token di semua form POST, flash message, validator.
- **Selesai bila:** login admin berhasil, halaman admin tertutup tanpa login, form tanpa token ditolak.

### Tahap 2. Design system dan layout (1,5 hari)
- Terapkan `tokens.css`, `base.css`, `components.css` sesuai `design.md`.
- Layout publik (navbar, footer, drawer mobile) dan layout admin (sidebar, topbar).
- Komponen: tombol, badge, kartu fitur, stat band, tabel, form, modal, toast, skeleton.
- **Selesai bila:** halaman contoh komponen (`/admin/ui-kit`) tampil benar di 360px, 768px, 1280px.

### Tahap 3. Data siswa dan import (1,5 hari)
- Export sheet `Data_Numerik` dan `Data_Tanpa_Label` ke CSV (UTF-8).
- `Importer.php`: baca CSV, validasi kolom, simpan `siswa`, `nilai_mapel`, `nilai_rata`, lalu hitung `siswa_atribut` lewat `Discretizer`.
- CRUD siswa dengan filter dan pencarian.
- **Selesai bila:** import menghasilkan tepat 247 siswa (147 berlabel, 100 tanpa label), dan distribusi label sama dengan sheet Ringkasan (MAN: Saintek 27, Campuran 12, Soshum 4; MAS: Saintek 7, Soshum 97).

### Tahap 4. Mesin ID3 (2 hari)
- Implementasi `Id3.php` (entropy, gain, build, predict, rules).
- Simpan model, langkah, aturan. Hanya satu model aktif.
- Halaman Training dengan tabel langkah per node.
- `tests/id3_manual_test.php` memakai dataset kecil 14 baris (contoh klasik play-tennis) dan subset 10 siswa yang dihitung manual di Bab IV.
- **Selesai bila:** entropy dan gain sistem sama dengan hitungan manual sampai 4 desimal, dan pohon play-tennis sesuai literatur (akar = Outlook).

### Tahap 5. Visual pohon dan aturan (1,5 hari)
- `tree.js`: render pohon dari JSON jadi diagram bersarang, lipat/buka cabang, zoom, warna daun per kelas.
- Mobile: tampilan pohon berubah jadi daftar bertingkat (accordion).
- Daftar aturan IF-THEN dengan support.
- **Selesai bila:** pohon dapat dibaca di layar 360px tanpa scroll horizontal halaman.

### Tahap 6. Pengujian model (1,5 hari)
- `Evaluator.php`: stratified k-fold dengan seed, confusion matrix, metrik.
- Halaman Pengujian dengan heatmap confusion matrix dan grafik akurasi per fold.
- **Selesai bila:** jumlah prediksi di confusion matrix = jumlah data uji, dan hasil sama saat dijalankan ulang dengan seed yang sama.

### Tahap 7. Prediksi dan Cek Rekomendasi (1,5 hari)
- Form publik: pilih sekolah, peminatan, isi nilai mapel. Sistem mengubah nilai jadi kategori, lalu memprediksi.
- Hasil: rumpun rekomendasi, tingkat keyakinan (distribusi daun), jalur keputusan berupa langkah bernomor, dan contoh prodi per rumpun.
- Prediksi massal untuk 100 siswa tanpa label di admin.
- **Selesai bila:** hasil form publik sama dengan prediksi admin untuk input yang sama.

### Tahap 8. Halaman publik lengkap (2 hari)
- Beranda sesuai referensi: hero slider, stat band dengan count-up, Program Unggulan (4 kartu keunggulan sistem), alur kerja 4 langkah, grafik ringkas, CTA.
- Metode ID3 dengan kalkulator entropy interaktif.
- Statistik, Informasi, Galeri, Kontak.
- **Selesai bila:** Lighthouse mobile Performance >= 80 dan Accessibility >= 90 di Beranda.

### Tahap 9. Laporan, konten, dan pengaturan (1 hari)
- Cetak laporan (layout print), export CSV.
- CRUD slider, pengumuman, galeri, pengaturan situs.
- **Selesai bila:** laporan evaluasi tercetak rapi di A4.

### Tahap 10. Pengujian akhir dan deploy (1 hari)
- Uji black box semua fitur (tabel uji untuk Bab IV).
- Uji responsif di 360, 390, 768, 1024, 1440 px.
- Uji keamanan dasar: SQL injection, XSS, CSRF, akses tanpa login.
- **Selesai bila:** semua kasus uji lulus dan tercatat.

Perkiraan total: 15 hari kerja.

## 11. Keamanan

1. Semua query lewat PDO prepared statement. Tidak ada string concatenation untuk input pengguna.
2. Semua output HTML lewat `e()` (`htmlspecialchars`, ENT_QUOTES, UTF-8).
3. CSRF token per sesi di setiap form POST.
4. `session_regenerate_id(true)` setelah login. Cookie `HttpOnly`, `SameSite=Lax`.
5. Upload: cek ekstensi dan MIME, batas ukuran 2 MB, nama file acak, simpan di `storage/` yang tidak bisa diakses langsung.
6. Batasi percobaan login (5 kali per 15 menit per IP).
7. Data pribadi (NISN, nama orang tua) tidak tampil di halaman publik. Statistik publik hanya agregat.

## 12. Rencana Pengujian (untuk Bab IV)

| Jenis | Cara | Bukti |
|---|---|---|
| Validasi hitungan | Bandingkan entropy dan gain sistem dengan Excel manual | Tangkapan layar tabel langkah |
| Black box | Tabel skenario per fitur (input, harapan, hasil) | Tabel uji |
| Akurasi model | k-fold 5, per skenario data | Confusion matrix dan metrik |
| Responsif | Chrome DevTools di 5 ukuran layar | Tangkapan layar |
| Usability (opsional) | Kuesioner singkat ke guru BK | Rekap skor |

## 13. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Kelas tidak seimbang (MAS 97 Soshum) | Akurasi tinggi tapi semu | Laporkan macro F1 dan skenario per sekolah |
| Nilai MAS seragam (97 sampai 99) | Atribut nilai tidak informatif untuk MAS | Peminatan jadi pembeda, jelaskan di Bab IV |
| Kelas Campuran ditolak pembimbing | Ubah label | Skenario "tanpa Campuran" sudah tersedia |
| Atribut tidak muncul di pohon | Pertanyaan penguji | Tampilkan tabel gain semua atribut di halaman Training |
| Server sekolah tanpa internet | CDN gagal | Simpan salinan lokal font, Lucide, Chart.js di `assets/vendor/` |
