# SIMINAT

**Sistem Klasifikasi Minat Jurusan Kuliah dengan Algoritma ID3**

SIMINAT membantu guru Bimbingan dan Konseling (BK) membaca kecenderungan rumpun studi siswa berdasarkan nilai rapor, peminatan, dan sekolah asal. Sistem menampilkan hasil klasifikasi beserta jalur keputusan agar hasilnya dapat dibahas bersama siswa.

Proyek ini dikembangkan untuk penelitian skripsi Nadia Umaira, Teknik Informatika, Universitas Malikussaleh, dengan data siswa MAN 4 Aceh Utara (Matangkuli) dan MAS Al-Muslimun Lhoksukon.

Hasil sistem merupakan rekomendasi **rumpun studi**, yaitu Saintek, Soshum, atau Campuran sesuai skenario model. Hasil tersebut menjadi bahan pertimbangan bersama guru BK dan orang tua.

## Fitur

| Modul | Fungsi |
|---|---|
| Halaman publik | Pengenalan SIMINAT, eksplorasi rumpun, alur penggunaan, penjelasan ID3, dan panduan konsultasi. |
| Autentikasi | Login, logout, pengaturan akun, dan penggantian kata sandi. |
| Dashboard | Ringkasan data siswa, model aktif, pengujian, dan prediksi terbaru. |
| Data siswa | Pencarian, filter, tambah, ubah, hapus, detail nilai, dan impor Excel dengan pratinjau. |
| Master data | Pengelolaan sekolah, atribut ID3, domain atribut, dan batas diskretisasi nilai. |
| Training model | Pelatihan ID3, langkah entropy dan information gain, pohon keputusan, aturan IF-THEN, dan aktivasi model. |
| Pengujian model | Stratified k-fold, confusion matrix, akurasi, precision, recall, dan F1. |
| Prediksi | Rekomendasi dari input nilai, penelusuran keputusan, contoh program studi, serta prediksi massal siswa tanpa label. |
| Laporan | Cetak data siswa, model, hasil pengujian, dan hasil prediksi; ekspor CSV sesuai jenis laporan. |
| Pengguna | Pengelolaan akun admin dan guru BK oleh administrator. |

Administrator mengelola data, pelatihan, pengujian, dan akun pengguna. Guru BK dapat membaca data dan hasil, menjalankan rekomendasi melalui form, serta menggunakan laporan sesuai hak aksesnya.

## Teknologi dan kebutuhan

- **PHP 8.1 atau lebih baru**, dengan ekstensi `PDO`, `pdo_mysql`, `mbstring`, `zip`, `SimpleXML`, dan `fileinfo`.
- **MySQL 8 atau MariaDB** untuk penyimpanan data.
- **Apache** dengan `mod_rewrite` dan dukungan `.htaccess`.
- **CSS native dan JavaScript ES6** untuk antarmuka.
- **Python 3** untuk verifikasi independen dan pengolahan data penelitian, bila diperlukan.

Aplikasi menggunakan pola MVC dengan lapisan service. Instalasi situs tidak memerlukan Composer maupun proses build frontend.

## Instalasi lokal

Contoh berikut memakai Laragon di Windows. Jalankan perintah dari root proyek, tempat `index.php` berada.

### 1. Siapkan folder dan server

Tempatkan proyek pada document root Laragon, misalnya:

```text
C:\laragon\www\web-id3
```

Untuk XAMPP, gunakan folder `htdocs`. Jalankan Apache dan MySQL/MariaDB, lalu pastikan perintah `php` tersedia pada terminal.

```powershell
php --version
php -m
```

### 2. Atur koneksi database

Pada instalasi baru, salin contoh konfigurasi:

```powershell
Copy-Item config/config.example.php config/config.php
```

Jika `config/config.php` sudah tersedia, sesuaikan file tersebut tanpa menimpanya dengan contoh konfigurasi. Isinya mengembalikan array berikut:

```php
<?php
declare(strict_types=1);

return [
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_name' => 'siminat',
    'db_user' => 'root',
    'db_password' => '',
];
```

Nama database dan kredensial harus sesuai dengan server lokal. File konfigurasi lokal diabaikan Git. Jika tetap memakai konfigurasi contoh, nilainya juga dapat diisi melalui variabel lingkungan `SIMINAT_DB_HOST`, `SIMINAT_DB_PORT`, `SIMINAT_DB_NAME`, `SIMINAT_DB_USER`, dan `SIMINAT_DB_PASSWORD`.

### 3. Siapkan database dan akun awal

```powershell
php scripts/setup_auth.php
```

Script membuat database jika belum ada, menyiapkan tabel, menjalankan migrasi kebijakan penggantian kata sandi, dan mengisi akun serta master data awal. Akun dan master data yang sudah tersedia tidak ditimpa. Pengguna database memerlukan hak pembuatan database bila database belum tersedia.

File SQL untuk pemeriksaan atau instalasi manual tersedia di `database/schema.sql`, `database/seed.sql`, `database/seed_master.sql`, dan `database/migrations/`.

### 4. Buka aplikasi

| Halaman | Contoh URL |
|---|---|
| Beranda | `http://localhost/web-id3/` |
| Login | `http://localhost/web-id3/login.php` |
| Panel | `http://localhost/web-id3/admin/` |

Jika virtual host Laragon aktif, gunakan `http://web-id3.test/`. Sesuaikan URL dengan nama folder atau virtual host pada komputer Anda.

Akun awal dari seed:

| Username | Kata sandi | Peran |
|---|---|---|
| `admin` | `admin123` | Administrator |

Ganti kata sandi akun awal melalui menu akun sebelum aplikasi digunakan bersama pengguna lain.

## Menyiapkan data dan model

### Impor dataset

Dataset utama berada di [data/Dataset_Bersih_ID3.xlsx](data/Dataset_Bersih_ID3.xlsx). Importer membaca sheet `Data_Numerik` dan `Data_Tanpa_Label`. Dataset penelitian yang diterima berjumlah 247 siswa: 147 berlabel dan 100 tanpa label.

Melalui panel, buka **Data Siswa → Import**, pilih dataset server atau unggah workbook `.xlsx`, lalu tinjau data sebelum menyimpannya.

Impor juga tersedia melalui CLI:

```powershell
php scripts/import_dataset.php
```

Untuk memakai file lain dengan struktur yang sama:

```powershell
php scripts/import_dataset.php "D:/dataset/Dataset_Bersih_ID3.xlsx"
```

**Impor mengganti seluruh data siswa.** Gunakan langkah ini ketika memang ingin memuat ulang dataset dan cadangkan database yang sudah berisi pekerjaan penting.

### Latih dan aktifkan model

Setelah data tersedia:

1. Buka **Training Model**, pilih atribut dan skenario data, lalu jalankan pelatihan.
2. Periksa langkah perhitungan, pohon keputusan, dan aturan yang terbentuk.
3. Aktifkan model yang akan digunakan untuk rekomendasi.
4. Buka **Pengujian Model** untuk mengevaluasinya dengan k-fold.
5. Gunakan **Prediksi** untuk input siswa atau prediksi massal.

Skenario yang tersedia:

| Kode | Data pelatihan |
|---|---|
| `semua` | Seluruh siswa berlabel. |
| `man` | Siswa berlabel dari MAN 4 Aceh Utara. |
| `mas` | Siswa berlabel dari MAS Al-Muslimun Lhoksukon. |
| `tanpa_campuran` | Siswa dengan label Saintek atau Soshum. |

Untuk menjalankan rangkaian penelitian melalui CLI:

```powershell
php scripts/proses_penelitian.php tanpa_campuran 5
```

Perintah tersebut **menyimpan model dan pengujian untuk seluruh skenario**, mengaktifkan model `tanpa_campuran`, lalu menjalankan prediksi massal. Argumen pertama memilih skenario aktif; argumen kedua memilih jumlah fold dari 3 sampai 10. Nilai defaultnya adalah `tanpa_campuran` dan `5`.

## Metode ID3

Sistem menghitung entropy dan information gain untuk memilih atribut pemisah pada setiap node. Jika gain sama, urutan atribut menentukan pilihan agar hasil konsisten. Cabang tanpa data memakai kelas mayoritas induknya; input dengan nilai atribut yang tidak dikenal memakai kelas mayoritas node terakhir yang dicapai.

Sembilan atribut awal mencakup sekolah asal, peminatan, kategori Agama, Bahasa Arab, Bahasa, Matematika, MIPA, IPS, dan kecenderungan MIPA/IPS. Batas diskretisasi dibaca dari database dengan nilai awal:

| Kategori | Nilai |
|---|---|
| Tinggi | ≥ 95 |
| Sedang | ≥ 92 dan < 95 |
| Rendah | < 92 |
| Tidak Ada | Nilai mata pelajaran tidak tersedia. |

Pengujian k-fold menggunakan stratifikasi dan seed tetap 42. Angka keyakinan pada hasil prediksi berasal dari distribusi kelas pada daun pohon; angka itu berbeda dari akurasi model pada data uji. Perhitungan, hasil penelitian, dan batas interpretasinya dijelaskan pada [verifikasi metode](docs/verifikasi-metode.md).

## Struktur aplikasi

| Lokasi | Isi |
|---|---|
| `index.php` | Halaman publik. |
| `login.php`, `logout.php` | Pintu masuk autentikasi. |
| `admin/` | Front controller dan aturan URL panel. |
| `app/core/` | Bootstrap, router, koneksi PDO, autentikasi, CSRF, dan helper. |
| `app/controllers/` | Penanganan permintaan dan alur halaman. |
| `app/models/` | Akses data. |
| `app/services/` | Logika ID3, impor, pelatihan, evaluasi, dan prediksi. |
| `views/` | Layout, halaman, dan komponen tampilan. |
| `assets/` | Stylesheet, JavaScript, logo, gambar WebP, dan font lokal. |
| `config/` | Konfigurasi database. |
| `database/` | Skema, seed, dan migrasi SQL. |
| `data/` | Dataset dan dokumen sumber penelitian. |
| `scripts/` | Perkakas persiapan dan pengolahan data. |
| `tests/` | Pengujian algoritma, HTTP, dan browser. |
| `storage/` | Upload dan log aplikasi. |
| `output/` | Hasil pemeriksaan lokal yang diabaikan Git. |
| `docs/` | Dokumentasi dan arsip proses pengembangan. |

## Pengujian

Jalankan dari root proyek:

| Perintah | Cakupan dan kebutuhan |
|---|---|
| `php tests/id3_manual_test.php` | Entropy, gain, pohon, kasus batas, dan k-fold; tidak memerlukan database. |
| `python tests/verify_id3_dataset.py` | Menghitung ulang ID3 dari workbook dan membandingkannya dengan sistem; membutuhkan PHP, database terisi, dan dataset. |
| `python tests/auth_http_test.py` | Alur autentikasi melalui HTTP dengan akun fixture sementara. |
| `python tests/admin_http_test.py` | Halaman panel, CRUD, impor, training, pengujian, prediksi, laporan, dan hak akses. |
| `python tests/auth_browser_test.py` | Alur login dan akun melalui browser. |
| `python tests/landing_sections_test.py` | Navigasi, komponen interaktif, aset, dan responsivitas landing. |
| `python tests/landing_motion_test.py` | Animasi, replay diagram, menu ponsel, dan reduced motion. |

Tes HTTP dan browser memakai alamat `http://localhost/web-id3/`, sehingga Apache, database, serta nama folder tersebut perlu sesuai. Fixture pengujian membuat data sementara dan membersihkannya setelah pengujian. Hasil pemeriksaan browser disimpan di `output/qa/`.

Untuk menyiapkan pengujian browser:

```powershell
python -m pip install playwright
python -m playwright install chromium
```

Pengolahan ulang dataset dari sumber penelitian membutuhkan `openpyxl` dan perintah Poppler `pdftotext`. Script utamanya adalah `scripts/build_dataset.py`; `extract.py` dan `raw_labels.py` menjadi sumber pendukung. Tahap ini diperlukan bila ingin membangun ulang workbook dari data mentah.

## Kendala umum

| Kendala | Pemeriksaan |
|---|---|
| Koneksi database gagal | Periksa layanan MySQL/MariaDB dan nilai pada `config/config.php`. |
| Tabel belum tersedia | Jalankan persiapan database dan periksa migrasi yang sesuai dengan database lama. |
| URL panel menghasilkan 404 | Pastikan `mod_rewrite` aktif dan Apache mengizinkan `.htaccess`. |
| `ZipArchive` atau fungsi XML tidak ditemukan | Aktifkan ekstensi `zip` dan `SimpleXML` pada PHP yang digunakan server maupun CLI. |
| Dataset server tidak ditemukan | Pastikan file berada di `data/Dataset_Bersih_ID3.xlsx`. |
| Rekomendasi belum dapat dijalankan | Impor data, latih model, dan aktifkan satu model. |
| PHP CLI berbeda dari PHP Apache | Periksa `php --ini` dan versi PHP yang dipilih pada Laragon. |

Detail error aplikasi tersimpan di `storage/logs/app.log`. Folder `storage/logs/` dan `storage/uploads/` perlu dapat ditulis oleh proses PHP.

## Data dan dokumentasi

Data penelitian memuat identitas siswa. Pertahankan aturan `.htaccess` yang membatasi akses HTTP ke folder internal dan gunakan data tersebut hanya untuk keperluan yang diizinkan. Pada server selain Apache, terapkan pembatasan akses yang setara.

- [Verifikasi metode dan hasil penelitian](docs/verifikasi-metode.md).
- [Rencana awal proyek](docs/plan.md).
- [Rancangan landing terbaru](docs/landing-redesign.md).
- [Peta struktur dan pembersihan proyek](docs/struktur-proyek.md).
- [Panduan pengembangan](docs/agents.md).
- [Riwayat perubahan](CHANGELOG.md).
