# SIMINAT

Web klasifikasi minat jurusan kuliah berbasis ID3 untuk siswa SMA/MA dan pendampingan guru BK. Aplikasi memakai PHP native, MySQL/MariaDB, CSS, dan JavaScript tanpa framework.

## Struktur proyek

| Folder | Isi |
|---|---|
| `app/` | Controller, model, service ID3, router, autentikasi, dan helper. |
| `admin/` | Pintu masuk panel admin. |
| `views/` | Layout dan tampilan aplikasi. |
| `assets/` | CSS, JavaScript, logo, gambar WebP yang dipakai situs, dan font lokal. |
| `config/` | Contoh konfigurasi dan konfigurasi database lokal yang diabaikan Git. |
| `database/` | Skema, seed, dan migrasi. |
| `data/` | Dataset bersih dan data penelitian sumber. Akses HTTP diblokir. |
| `scripts/` | Persiapan autentikasi, ekstraksi data, pembentukan dataset, impor, dan proses penelitian. |
| `tests/` | Pengujian ID3, verifikasi dataset, HTTP, dan browser. |
| `docs/` | Rencana proyek, panduan pengembangan, verifikasi metode, desain, dan struktur. |
| `docs/archive/` | Catatan iterasi, konsep visual, dan gambar PNG sumber. |
| `videos/siminat-dinamis/` | Video terbaru 9:16, komposisi HyperFrames, aset, dan hasil MP4. |
| `videos/archive/` | Proyek video pengenalan sebelumnya beserta sumber dan hasilnya. |
| `storage/` | Upload dan log aplikasi. |
| `output/` | Hasil pengujian dan laporan lokal yang diabaikan Git. |

`node_modules/` merupakan dependensi perkakas video, bukan dependensi runtime situs PHP. Folder ini dapat dipasang ulang melalui `npm ci` menggunakan package-lock.json.

## Menjalankan situs

1. Gunakan PHP 8.1+ dengan PDO MySQL, MySQL/MariaDB, dan Apache dengan mod_rewrite.
2. Siapkan database dari `database/schema.sql` dan `database/seed.sql` untuk instalasi baru. Migrasi untuk database lama ada di `database/migrations/`.
3. Salin `config/config.example.php` ke `config/config.php`, lalu sesuaikan koneksi database.
4. Buka alamat proyek di Laragon, misalnya `http://localhost/web-id3/` atau `http://web-id3.test/`. Panel berada di `/admin/`.

Dataset utama: `data/Dataset_Bersih_ID3.xlsx`. Script pembentuk dataset, impor CLI, halaman impor admin, dan verifikasi independen menggunakan lokasi yang sama. `scripts/import_dataset.php` mengganti data siswa; jalankan hanya saat memang ingin melakukan impor.

## Pengujian

```powershell
php tests/id3_manual_test.php
python tests/verify_id3_dataset.py
python tests/landing_sections_test.py
python tests/landing_motion_test.py
```

Verifikasi dataset memerlukan database terisi. Uji browser memerlukan Playwright dan situs lokal aktif; hasilnya masuk ke `output/qa/`. Pengujian autentikasi dan panel tersedia di `tests/auth_http_test.py`, `tests/auth_browser_test.py`, dan `tests/admin_http_test.py` dengan fixture akun sementara.

## Video terbaru

- Hasil: [siminat-dinamis-9x16.mp4](videos/siminat-dinamis/renders/siminat-dinamis-9x16.mp4).
- Brief dan sumber: [BRIEF.md](videos/siminat-dinamis/BRIEF.md).
- Jalankan preview dari root: `npx hyperframes preview videos/siminat-dinamis --background`.
- Video sebelumnya: [arsip pengenalan](videos/archive/siminat-pengenalan/).

Peta pemindahan dan hasil pembersihan: [docs/struktur-proyek.md](docs/struktur-proyek.md).
