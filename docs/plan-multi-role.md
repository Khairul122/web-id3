# Spesifikasi multi-role panel: Admin, Guru BK, dan Siswa

Disusun: 22 Oktober 2026. Status: Fase 1 sampai 4 diimplementasikan; Fase 5 tetap opsional.

## 0. Cara memakai dokumen ini

1. Baca dulu `docs/agents.md` (stack wajib, aturan kode), `docs/struktur-proyek.md`, dan dokumen ini sampai habis.
2. Kerjakan tugas **berurutan sesuai nomor di bagian 5**. Urutan ini penting untuk keamanan (lihat 4.1).
3. Satu tugas = satu commit. Setelah tiap fase, jalankan uji di bagian 6.
4. Jangan mengubah keputusan di bagian 4 dan 7. Bila ada yang tidak bisa dijalankan, berhenti dan laporkan.

Aturan proyek yang paling sering dilanggar:

- PHP 8.1 native tanpa framework dan tanpa Composer. CSS murni dengan token `assets/css/tokens.css`. JS vanilla.
- Query hanya lewat `Database::run()`/`Database::pdo()` dengan prepared statement. Output HTML selalu lewat `e()`.
- Form POST wajib `<?= Csrf::field() ?>`, controller memanggil `$this->verifyPost(...)`.
- Pesan hasil aksi: `flash('success'|'error', judul, pesan)` lalu `back_to(...)`.
- Teks antarmuka berbahasa Indonesia baku.

## 1. Kondisi proyek

### Arsitektur panel

- Semua URL panel lewat `panel/index.php` → `Router::dispatch()` (`app/core/Router.php`). Pola `/panel/{modul}/{aksi}` → `{Modul}Controller::{aksi}()`.
- Semua controller panel turunan `Controller` (`app/core/Controller.php`). Konstruktornya memanggil `Auth::requireUser()`; jadi konstruktor adalah satu titik yang dilewati setiap request panel.
- Layout: `views/layouts/admin_top.php` (sidebar + topbar) dan `admin_bottom.php`. View menerima `$user`, `$isAdmin`, dan (setelah perubahan working tree) `$isSiswa`.

### Peran yang sudah ada

`users.role` saat ini (di DB produksi) hanya `admin` dan `guru_bk`. Guru BK sudah berjalan sebagai peran baca + Cek Rekomendasi:

| Modul | Admin | Guru BK (sekarang) |
|---|---|---|
| Dashboard | penuh | lihat |
| Data Siswa, Sekolah, Atribut, Diskretisasi | CRUD, impor | lihat |
| Training, Pengujian | latih, aktifkan, uji, hapus | lihat |
| Prediksi | Cek Rekomendasi, massal, hapus | Cek Rekomendasi, lihat riwayat semua |
| Laporan | cetak, CSV | cetak, CSV |
| Pengguna | kelola | ditolak |
| Akun Saya | ganti sandi | ganti sandi |

Jadi peran guru tidak dibangun dari nol. Pekerjaan utama adalah peran **siswa**, ditambah perapian peran guru. Dokumen ini menganggap "guru" = Guru BK.

### Temuan yang menentukan desain

1. **Hak akses guru bersifat opt-in.** Aksi khusus admin memanggil `$this->requireAdmin()` satu per satu (17 pemanggilan di 8 controller). Aksi tanpa pemanggilan itu terbuka untuk semua peran yang login. Aman untuk guru, tidak aman untuk siswa. Siswa memerlukan **default-deny** di konstruktor `Controller`.
2. **Daftar peran tersebar** di `Auth::user()`, `AuthController::login()`, `PenggunaService::ROLES`, dan ternary label di `views/layouts/admin_top.php:81`. Semuanya dipusatkan ke `PenggunaService::ROLES`.
3. **Impor mengganti seluruh siswa.** `Importer::store()` memanggil `Siswa::deleteAll()` lalu insert ulang, sehingga `siswa.id` berubah tiap impor. Akun siswa ditautkan lewat `kode_siswa` (stabil, format `MAN-K1-001`), **tanpa FK**.
4. **Riwayat prediksi tidak punya filter pemilik.** `Prediksi::paginate()`, `find()`, `recent()`, dan `export()` mengembalikan semua baris. `prediksi.siswa_id` menjadi NULL setelah impor ulang (`ON DELETE SET NULL`). Kepemilikan siswa memakai `prediksi.dibuat_oleh` (id user) ATAU `prediksi.siswa_id`.
5. **Dashboard berorientasi admin.** `DashboardService::summary()` memuat `Prediksi::recent(6)` yang berisi **nama siswa lain**, dan kartu "Alur kerja penelitian" menautkan `siswa/import`, `training/form`, `pengujian/form`, `prediksi/massal` ke semua peran.
6. **`PrediksiService::predictForm()`** selalu menyimpan `siswa_id = null` (baris 69) dan mengambil `sekolah`/`peminatan`/`nama` dari POST lewat `fromPost()`.
7. **`views/admin/prediksi/detail.php`** menampilkan `label_asli` (baris 33) dan "Diproses oleh" (baris 49).
8. Bisa dipakai ulang tanpa perubahan: `Id3::predict()` (sudah dipakai `SiswaController::detail` untuk hasil langsung), `Prediksi::forSiswa()`, `PrediksiService::PRODI`, alur form → tinjau → hasil Cek Rekomendasi, `AkunController`, `Csrf`, `flash()`, dialog.

## 2. Fondasi awal saat rancangan ditulis

Bagian ini mencatat keadaan awal saat rancangan ditulis.

| File | Isi perubahan | Status |
|---|---|---|
| `database/migrations/004_role_siswa.sql` | ENUM + `siswa`, kolom `users.kode_siswa VARCHAR(20) NULL UNIQUE` | selesai |
| `database/schema.sql` | sama, untuk instalasi baru | selesai |
| `scripts/setup_auth.php` | jalankan 004 hanya bila kolom `kode_siswa` belum ada | selesai |
| `app/core/Auth.php`, `app/controllers/AuthController.php` | cek peran via `isset(PenggunaService::ROLES[...])` | selesai |
| `app/core/Controller.php` | `SISWA_MODULES` (Dashboard, Prediksi, Akun), `$isSiswa`, `$ownSiswa` (properti, dimuat di konstruktor), `requireStaff()`, `denySiswa()`, `isSiswa` diteruskan ke view | selesai |
| `app/models/Siswa.php` | `findByKode()` | selesai |
| `tests/auth_fixture.php` | fixture menerima `SIMINAT_TEST_ROLE=siswa` dan `SIMINAT_TEST_KODE` | selesai |
| `PenggunaService::ROLES` | **belum** berisi `siswa` | sengaja, lihat 4.1 |

Karena `ROLES` belum berisi `siswa`, akun siswa saat ini tetap ditolak saat login. Itu keadaan aman.

## 3. Matriks hak akses tujuan

| Modul / aksi | Admin | Guru BK | Siswa |
|---|---|---|---|
| Dashboard | ringkasan penelitian | ringkasan bimbingan (baru) | profil + hasil saya (baru) |
| Data Siswa: daftar, detail | ya | ya | ditolak |
| Data Siswa: CRUD, impor | ya | ditolak | ditolak |
| Sekolah, Atribut, Diskretisasi (lihat) | ya | ya | ditolak |
| Training, Pengujian (lihat) | ya | ya | ditolak |
| Training, Pengujian (ubah) | ya | ditolak | ditolak |
| Cek Rekomendasi | ya | ya | ya, sekolah, peminatan, nama dikunci ke datanya |
| Riwayat prediksi | semua | semua | milik sendiri saja |
| Detail prediksi | semua | semua | milik sendiri saja |
| Prediksi massal, hapus | ya | ditolak | ditolak |
| Laporan | ya | ya | ditolak |
| Pengguna, buat akun siswa | ya | ditolak | ditolak |
| Reset sandi akun siswa | ya | ya, hanya akun berperan `siswa` | ditolak |
| Akun Saya | ya | ya | ya |

## 4. Keputusan desain

| Keputusan | Pilihan | Alasan |
|---|---|---|
| Nilai peran | Tambah `siswa` ke ENUM; `guru_bk` tetap | Akun guru lama tidak perlu migrasi. |
| Tautan akun ke siswa | `users.kode_siswa`, tanpa FK | Bertahan saat impor ulang (temuan 3). Kode yang hilang menampilkan keadaan kosong, bukan error. |
| Penjaga akses siswa | `SISWA_MODULES` di `Controller::__construct()` | Satu titik, default-deny. Modul baru otomatis tertutup bagi siswa. |
| Penjaga per aksi | Di controller yang diizinkan (Dashboard, Prediksi, Akun), aksi yang bukan untuk siswa memanggil `requireStaff()` atau `requireAdmin()` | `massal` dan `hapus` sudah `requireAdmin()`. |
| Kepemilikan prediksi | `p.dibuat_oleh = :userId OR p.siswa_id = :siswaId` | Simulasi siswa bertahan saat impor ulang; hasil massal untuk dirinya tetap terlihat. |
| URL panel siswa | Tetap `/admin/...` | Router, layout, CSS, CSRF sudah ada. Label brand diganti "Panel Siswa". |
| Username siswa | `kode_siswa` huruf kecil | Unik, lolos regex username. |
| Sandi awal siswa | Acak 12 karakter, ditampilkan sekali di halaman cetak | NISN mudah ditebak; aturan minimal 12 byte tetap berlaku. |

### 4.1 Urutan keamanan (wajib)

Menambahkan `'siswa'` ke `PenggunaService::ROLES` **membuka login siswa**. Saat itu juga, tanpa penjaga tambahan, siswa bisa:

- melihat nama siswa lain di dashboard (`Prediksi::recent`),
- melihat seluruh riwayat di `prediksi/` dan detail siapa pun lewat `prediksi/detail?id=N` (IDOR).

Karena itu `ROLES` baru diubah di **tugas 1.6**, setelah dashboard dan prediksi dijaga (tugas 1.3–1.5). Jangan ubah urutannya.

## 5. Tugas

### Fase 1: Fondasi peran + penjagaan siswa

**1.1 Siapkan form Pengguna untuk kode siswa (tanpa membuka login dulu).**
- `User::create()` dan `User::update()` menerima parameter `?string $kodeSiswa`; `User::all()` ikut memilih `kode_siswa`.
- `PenggunaService::fromPost()` membaca `kode_siswa`. Bila `role === 'siswa'`: wajib, harus ada di tabel `siswa` (`Siswa::findByKode`), dan belum dipakai akun lain (query `users` dengan `id <> ?`). Peran lain: simpan NULL.
- `views/admin/pengguna/form.php`: isian `kode_siswa`, ditampilkan hanya bila pilihan peran Siswa (atribut `data-*` + JS kecil di `assets/js/admin.js`, isian tetap ada di HTML agar tanpa JS masih bisa diisi).
- Saat admin mengubah peran atau `kode_siswa` akun lain, naikkan `session_version` akun itu (cek apakah `User::update` sudah melakukannya saat sandi berubah; terapkan pola yang sama).

**1.2 Layout per peran.**
- `views/layouts/admin_top.php`: kolom ke-4 menu diganti dari `bool $adminOnly` menjadi daftar peran, contoh `['admin']`, `['admin','guru_bk']`, atau `null` (semua). Filter dengan `in_array($user['role'], ...)`.
- Menu siswa: Dashboard, Cek Rekomendasi (`prediksi/form`), Riwayat Saya (`prediksi/`), Akun Saya. Grup "Master Data" dan "Proses ID3" tidak tampil.
- Label topbar: `PenggunaService::ROLES[$user['role']]`. Teks brand: "Panel Siswa" untuk siswa, "Panel Pengelola" lainnya.

**1.3 Dashboard per peran.**
- `DashboardController::index()`: bila `$this->isSiswa`, render `dashboard/siswa` dengan data dari 3.1; **jangan** panggil `DashboardService::summary()` untuk siswa.
- Untuk tugas ini cukup view siswa minimal (nama + keadaan kosong). Isi lengkap di 3.1.

**1.4 Kepemilikan prediksi di model.**
- `Prediksi::paginate(array $filter, int $page, ?array $owner = null)`: bila `$owner = [userId, siswaId|null]`, tambahkan `(p.dibuat_oleh = ? OR p.siswa_id = ?)`. Bila `siswaId` null, pakai `p.dibuat_oleh = ?` saja. Filter ini **tidak pernah** dibaca dari URL.
- `Prediksi::ownedBy(array $prediction, int $userId, ?int $siswaId): bool` atau pemeriksaan setara di controller.

**1.5 Penjagaan `PrediksiController` untuk siswa.**
- `index()`: siswa → kirim `$owner`. Judul halaman "Riwayat Saya".
- `detail()`: siswa dan bukan pemilik → perlakukan sebagai tidak ditemukan (`notFound`), jangan bocorkan keberadaan id.
- `form()`/`review()`: siswa → timpa `nama`, `sekolah`, `peminatan` dari `$this->ownSiswa` di server (abaikan POST). Bila `$this->ownSiswa === null`, tolak dengan flash "Data Anda belum tersedia, hubungi guru BK" lalu kembali ke dashboard.
- `PrediksiService::predictForm()` mendapat parameter `?int $siswaId = null`; siswa mengirim `(int) $this->ownSiswa['id']`.
- `views/admin/prediksi/detail.php`: sembunyikan `label_asli` dan "Diproses oleh" untuk siswa. `views/admin/prediksi/form.php`: untuk siswa, sekolah/peminatan/nama ditampilkan sebagai teks, bukan select.
- `massal()` dan `hapus()` sudah `requireAdmin()`; tidak diubah.

**1.6 Buka login siswa.**
- `PenggunaService::ROLES` = `['admin' => 'Administrator', 'guru_bk' => 'Guru BK', 'siswa' => 'Siswa']`.
- Jalankan `php scripts/setup_auth.php` di DB lokal agar migrasi 004 terpasang.

Selesai bila: akun siswa uji bisa login, hanya melihat 4 menu siswa, URL modul lain kembali ke dashboard dengan dialog "Akses ditolak", dan seluruh uji bagian 6 untuk Fase 1 lulus.

### Fase 2: Akun siswa massal (admin)

**2.1** Aksi `PenggunaController::akunSiswa()` (URL `pengguna/akun-siswa`): GET menampilkan jumlah siswa tanpa akun dengan filter sekolah dan kelas. POST (dengan `data-confirm`) membuat akun dalam `Database::transaction()`; username = `strtolower(kode_siswa)`, sandi = `bin2hex(random_bytes(6))` (12 karakter), disimpan hanya sebagai hash.

**2.2** Hasil (nama, kelas, username, sandi awal) disimpan di `$_SESSION['kartu_akun']`, ditampilkan **sekali** di halaman kartu akun siap cetak, lalu dihapus dari sesi saat halaman dibuka. Buka ulang → keadaan kosong. Halaman ini berada di `SiswaController::kartuAkun()` (URL `siswa/kartu-akun`, gaya cetak mengikuti laporan yang ada), **bukan** di `PenggunaController`, karena `PenggunaController::__construct()` memanggil `requireAdmin()` sehingga guru BK tidak bisa membukanya, padahal 2.5 juga memakai halaman ini. Siswa sudah tertutup dari modul `siswa` lewat `SISWA_MODULES`.

**2.3** `views/admin/pengguna/index.php`: filter peran (jumlah akun akan naik ke sekitar 250). Kolom kode siswa untuk akun siswa.

**2.4** `views/admin/siswa/detail.php`: status akun (sudah/belum punya akun) untuk admin dan guru.

**2.5 Reset sandi akun siswa oleh admin dan guru BK.**
- Aksi `SiswaController::resetSandi()` (URL `siswa/reset-sandi`), hanya POST lewat `$this->verifyPost('siswa/detail?id=…')`. Tombol "Reset Sandi" di `siswa/detail` tampil bila siswa sudah punya akun, dengan `data-confirm` ("Sandi lama tidak bisa dipakai lagi").
- Akun target dicari di server: `SELECT ... FROM users WHERE kode_siswa = ? AND role = 'siswa'` memakai `kode_siswa` dari baris siswa yang dipilih. **Jangan** menerima `user_id` dari POST, agar guru tidak bisa mereset akun admin atau guru lain.
- Sandi baru `bin2hex(random_bytes(6))`, disimpan lewat `User::changePassword()` (sudah menaikkan `session_version`, jadi sesi siswa yang sedang aktif ikut keluar).
- Sandi baru ditampilkan sekali lewat halaman kartu akun 2.2 (isi `$_SESSION['kartu_akun']` dengan satu baris, lalu `back_to('siswa/kartu-akun')`).
- Guru BK tidak mendapat akses lain ke akun siswa (tidak bisa mengubah username, peran, atau menghapus akun).

### Fase 3: Panel siswa

**3.1 Dashboard siswa** (`views/admin/dashboard/siswa.php`):
- Profil: nama, sekolah, kelas, peminatan dari `$this->ownSiswa`.
- Nilai rata-rata dan kategori atribut ID3 (data yang sama dengan `siswa/detail`; salin bagian view yang relevan, jangan menambah query di view).
- Hasil rekomendasi model aktif: `Id3::predict($model['tree'], $ownSiswa['atribut'])`, tampilkan rumpun, keyakinan, jalur keputusan, dan contoh prodi dari `PrediksiService::PRODI`.
- Kalimat bahwa hasil adalah bahan diskusi dengan guru BK, bukan keputusan.
- Keadaan kosong: `kode_siswa` tidak ditemukan ("Data Anda belum tersedia, hubungi guru BK") atau belum ada model aktif.
- Tombol ke Cek Rekomendasi (simulasi nilai lain) dan Riwayat Saya.

**3.2** Rapikan teks halaman prediksi untuk siswa: "Simulasi Rekomendasi" di form, "Riwayat Saya" di daftar, tanpa filter sumber `massal` bila tidak relevan.

### Fase 4: Perapian panel guru

**4.1** Dashboard guru: kartu "Alur kerja penelitian" hanya untuk admin. Untuk guru, tampilkan ringkasan bimbingan: jumlah siswa per rumpun hasil, siswa tanpa label yang belum diprediksi, prediksi terbaru, tombol Cek Rekomendasi. Data tambahan dihitung di `DashboardService`, bukan di view.

**4.2** `views/admin/pengguna/detail.php:18`: teks hak akses per peran (tiga cabang, atau peta di `PenggunaService`).

### Fase 5 (opsional, kerjakan hanya bila diminta): Catatan konsultasi BK

- Tabel `catatan_bk(id, kode_siswa, isi TEXT, dibuat_oleh, created_at)` lewat migrasi `005_catatan_bk.sql` (alasan `kode_siswa` sama dengan temuan 3).
- Guru dan admin menulis catatan di `siswa/detail`; siswa membacanya di dashboard. Tanpa balasan, tanpa notifikasi.

## 6. Pengujian

Uji HTTP berjalan terhadap Apache Laragon di `http://localhost/web-id3/` (lihat kepala `tests/admin_http_test.py`). Data uji memakai awalan `UJI-` / `auth_test_` dan dibersihkan oleh fixture.

Perluas `tests/admin_http_test.py` (fixture: `tests/auth_fixture.php` dengan `SIMINAT_TEST_ROLE=siswa SIMINAT_TEST_KODE=UJI-...`, dan siswa uji `UJI-...` dibuat lewat `tests/admin_fixture.php`):

Fase 1:
- Siswa login, dashboard menampilkan namanya dan tidak memuat nama siswa lain.
- Setiap rute modul selain dashboard, prediksi, akun ditolak, termasuk `laporan/csv?jenis=siswa`, `siswa/detail?id=…`, `pengguna/`.
- `prediksi/detail?id=<milik akun lain>` ditolak (IDOR). `prediksi/` hanya berisi milik sendiri.
- POST Cek Rekomendasi dengan `sekolah`/`peminatan`/`nama` palsu tetap tersimpan dengan data siswa sendiri dan `siswa_id` miliknya.
- `prediksi/massal` dan POST `prediksi/hapus` ditolak.
- Akun siswa dengan `kode_siswa` yang tidak ada di tabel `siswa`: dashboard menampilkan keadaan kosong, Cek Rekomendasi ditolak dengan pesan.
- Seluruh uji admin dan guru BK yang ada tetap lulus.

Fase 2:
- Pembuatan akun massal: jumlah akun sesuai, `password_hash` bukan teks sandi, halaman kartu tidak bisa dibuka ulang.
- Validasi form: kode siswa kosong / tidak ada / sudah dipakai ditolak.
- Guru BK mereset sandi siswa: sandi lama gagal login, sandi baru berhasil, sesi siswa yang sedang aktif ikut keluar.
- Reset sandi tanpa token CSRF ditolak; siswa yang memanggil `siswa/reset-sandi` ditolak.
- Reset pada siswa tanpa akun menampilkan pesan gagal, dan tidak ada cara mereset akun admin atau guru lewat aksi ini.

Fase 3:
- Setelah impor ulang dataset, akun siswa tetap tertaut dan simulasi lamanya tetap terlihat di Riwayat Saya.

Selalu jalankan juga: `php tests/id3_manual_test.php` dan `python tests/auth_http_test.py`.

## 7. Keputusan default (sudah ditetapkan, jangan ditanyakan ulang)

1. Wajib ganti sandi untuk siswa: **tidak**, mengikuti migrasi 002.
2. Siswa melihat label asli / jurusan diterima: **tidak**, hanya hasil rekomendasi model.
3. Guru BK mereset sandi siswa: **ya**, khusus akun berperan `siswa` (tugas 2.5). Admin tetap bisa mereset semua akun lewat menu Pengguna.
4. "Guru" = Guru BK. Tidak ada peran wali kelas.

Bila pemilik proyek mengubah salah satunya, perbarui bagian ini sebelum mengubah kode.

## 8. Dokumen yang diperbarui di akhir

`README.md`, `README.id.md` (tabel fitur dan peran), `docs/plan.md` (skema `users`), `docs/struktur-proyek.md` (alur hak akses), `CHANGELOG.md`.
