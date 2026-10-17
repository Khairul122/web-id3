# Login dan autentikasi SIMINAT

## Arah dan navigasi

Halaman login bagi administrator dan guru BK, dengan bahasa visual editorial yang mengikuti hero. Dial: ENERGY 2 / RHYTHM 2 / MOTION 1.

Komposisi desktop: panel foto 52%, form di kertas ivory 48%, judul Georgia, teks Inter lokal. Foto menghubungkan halaman dengan proses pendampingan siswa; overlay menjaga keterbacaan. Pada ponsel, foto menjadi header singkat dan form ditampilkan satu kolom. Foto login berbeda dari foto landing sesuai permintaan pengguna.

- Landing menampilkan tautan Masuk pada navigasi desktop dan mobile.
- Setelah login, tautan berubah menjadi Panel Saya.
- Kembali ke Beranda tersedia pada halaman login dan halaman akun.
- Password visibility toggle, bantuan masuk, validasi server, dan state submit tersedia. Login tetap bekerja tanpa JavaScript.
- Ruang kerja awal hanya menampilkan informasi akun dan tombol keluar/ganti kata sandi. Fitur siswa serta ID3 belum dibuat.

## Database dan akun awal

Database MySQL: `siminat`. Tabel pada tahap ini: `users` dan `login_attempts`. Database terlihat di phpMyAdmin lokal.

Koneksi lokal menggunakan `root` tanpa kata sandi, sesuai instruksi pengguna. Pengaturan berada di config/config.php, tidak disajikan lewat HTTP dan diabaikan git. config.example.php mendukung variabel lingkungan SIMINAT_DB_HOST, SIMINAT_DB_PORT, SIMINAT_DB_NAME, SIMINAT_DB_USER, dan SIMINAT_DB_PASSWORD.

Akun aplikasi awal: username `admin`, kata sandi `admin123`. Ini berbeda dari akun MySQL. Sesuai permintaan pengguna, login langsung membuka ruang kerja dan penggantian kata sandi bersifat sukarela melalui menu Ganti kata sandi. Pengujian menggunakan akun fixture terpisah dan tidak mengubah kata sandi admin awal.

Setup dapat diulang tanpa menimpa akun yang ada:

```powershell
php scripts/setup_auth.php
```

Skema: database/schema.sql. Migrasi awal: database/migrations/001_auth.sql. Perubahan kebijakan kata sandi: database/migrations/002_optional_password_change.sql. Seed: database/seed.sql. Kolom must_change_password dipertahankan untuk kompatibilitas data lama dengan default 0; nilainya tidak memaksa penggantian kata sandi pada controller. Tabel domain siswa/ID3 akan ditambahkan saat fiturnya dikerjakan.

## Kontrak autentikasi

- POST login dan perubahan akun wajib token CSRF sesi; token tidak valid menghasilkan HTTP 403.
- Password disimpan menggunakan bcrypt cost 12 dan diperiksa dengan password_verify. Password baru panjangnya 12 hingga 72 byte, berbeda dari password lama, dan konfirmasinya harus sama.
- Prepared statement PDO native pada seluruh query yang menerima input.
- Username dibatasi format dan panjang; input array atau bentuk tidak valid ditolak. Output menggunakan e(). Password tidak dikembalikan ke HTML.
- Login gagal memakai pesan generik. Setelah lima percobaan per IP dalam 15 menit, server mengembalikan HTTP 429 dan Retry-After. Penghitung tersimpan di MySQL dan diperbarui dengan row lock, bukan counter sesi.
- IP disimpan sebagai hash SHA-256. Jendela kedaluwarsa tidak berlaku lagi setelah 15 menit, dan record lama dibersihkan pada permintaan login berikutnya.
- Session ID dirotasi setelah login, perubahan password, dan logout. Logout hanya POST. Cookie HttpOnly dan SameSite=Lax; Secure serta HSTS aktif ketika permintaan dilayani HTTPS.
- Batas sesi: idle 30 menit, absolut 8 jam. Perubahan password menaikkan session_version sehingga sesi akun lain menjadi tidak valid.
- Pengguna dan peran diperiksa di server untuk setiap akses ruang kerja. Peran yang diterima: admin dan guru_bk.
- CSP membatasi aset ke origin sendiri. Respons autentikasi tidak boleh disimpan cache. Detail exception hanya ke storage/logs/app.log; pengguna melihat pesan umum.
- Direktori app, views, config, database, storage, data, scripts, tests, docs, dan output dilindungi .htaccess.

Dokumentasi PHP primer: [password_verify](https://www.php.net/manual/en/function.password-verify.php), [PDO prepared statements](https://www.php.net/manual/en/pdo.prepared-statements.php), [session security](https://www.php.net/manual/en/features.session.security.management.php).

## Foto khusus login

Alat: built-in GPT Image (image_gen.imagegen). Foto merupakan ilustrasi, bukan dokumentasi siswa atau guru sekolah. Label AI tetap ditampilkan di desktop dan mobile.

- Asli: assets/img/auth/bimbingan-karier-ai.png.
- Aset web: assets/img/auth/bimbingan-karier-ai.webp, 1122×1402 piksel. Konversi format saja tanpa perubahan adegan.
- Konsep komposisi awal: docs/visuals/login-concept.png. Foto akhirnya diganti dengan visual konsultasi atas permintaan pengguna.

Prompt foto final:

```text
Use case: photorealistic-natural. Asset type: standalone photographic background for the LEFT visual panel of SIMINAT login page, portrait 4:5 composition, high-resolution. Generate a BRAND NEW image; do not repeat the previous classroom with raised hands. Primary scene: a thoughtful, warm university-career guidance conversation in an Indonesian madrasah library or counseling room. Three people: one female Indonesian guidance teacher aged around 35 in modest muted navy hijab and cream blouse, one Indonesian senior male student aged 17-18 wearing teal blue school shirt and black peci, one Indonesian senior female student aged 17-18 wearing teal school uniform with ivory hijab. They sit together at an authentic wooden table, reviewing an open notebook and simple unlabeled university subject brochures, with an attentive friendly conversational expression, not looking at camera, no exaggerated smiles. Teacher gestures naturally to a notebook; believable hands and anatomy. Composition: subjects' faces primarily across upper-middle area around y=30-46%, centered around x=35-75%; table in middle-lower area. Keep the upper-left corner quiet, out-of-focus wood-and-bookshelves background for a website logo. Keep bottom 30% relatively dark and softly detailed wooden foreground for CSS text overlay. Camera at natural eye-level, medium-wide view shows people from torso up and part of table, not giant cropped faces. Warm late-afternoon window light from right, deep warm walnut wood, soft library shelves, natural Indonesian features and realistic textured skin, teal and navy uniforms harmonize with gold sunlight. Premium candid editorial education photograph, reassuring and grounded, shallow depth of field, subtle film character. Do NOT add an overall navy overlay; CSS will apply that. No text, no letters, no logos or official school badges, no branding, no UI, no decorative frames, no watermark. No raised hands, no AI fantasy details.
```

## Pengujian

```powershell
python tests/auth_http_test.py
python tests/auth_browser_test.py
python output/qa/verify_hero.py
```

Tes HTTP menggunakan akun fixture bernama auth_test_* dan IP loopback 127.0.0.2 untuk mengisolasi penghitung percobaan. Tes browser menggunakan fixture guru BK. Fixture dibersihkan di akhir tes; akun admin asli tidak diubah.
