# Changelog

## 2026-10-17 (gambar pohon)

- Pohon keputusan dibuat otomatis sebagai gambar SVG (`app/services/TreeImage.php`, rute `admin/training/pohon?id=`), tampil di detail model dan laporan cetak, dengan unduhan SVG dan PNG.

## 2026-10-17 (persiapan dibagikan)

- Teks landing diperbarui: tidak lagi menyebut fitur klasifikasi "dalam pengembangan"; dialog Metode ID3 menampilkan ringkasan model aktif dari database.
- Pohon keputusan: cabang tanpa data digabung satu baris per node; jumlah daun berisi data ditampilkan.
- Nilai mapel mengikuti urutan legger; pohon pada halaman cetak dapat digulir di ponsel.

## 2026-10-17 (verifikasi metode)

- Mesin ID3 disesuaikan dengan kriteria berhenti proposal: aturan berhenti saat gain 0 dihapus.
- Verifikasi independen `tests/verify_id3_dataset.py` (Python, langsung dari Excel) lulus untuk 4 skenario.
- Data diolah dengan `scripts/proses_penelitian.php`: 4 model, 4 pengujian 5-fold, model aktif skenario Saintek dan Soshum, prediksi massal 100 siswa.
- Laporan kesesuaian metode dan data: `docs/verifikasi-metode.md`.

## 2026-10-17 (panel admin)

- Panel admin berstruktur MVC + service: front controller `admin/index.php` (URL `/admin/{modul}/{aksi}`), controller di `app/controllers`, logika di `app/services`, query di `app/models`, tampilan di `views/admin/{modul}/index.php, form.php, review.php, detail.php`.
- Modul: Dashboard, Data Siswa (CRUD, form -> tinjau -> simpan, import Excel dengan validasi 247/147/100), Data Sekolah, Atribut ID3, Diskretisasi, Training (pohon, langkah entropy/gain, aturan IF-THEN), Pengujian (stratified k-fold seed 42, confusion matrix), Prediksi (Cek Rekomendasi dan massal), Laporan (cetak A4 dan CSV), Pengguna, Akun Saya.
- Mesin ID3 (`app/services/Id3.php`) dan evaluator, diuji dengan play-tennis di `tests/id3_manual_test.php`.
- Dialog pesan di tengah layar untuk berhasil, gagal, dan konfirmasi, termasuk saat masuk dan keluar.
- Database: migrasi `003_master_id3.sql` dan `seed_master.sql`. Import CLI: `php scripts/import_dataset.php`.
- Tes: `tests/admin_http_test.py`, tes autentikasi diperbarui untuk rute dan dialog baru.

## 2026-10-17

- Sesuai permintaan pengguna, login langsung membuka ruang kerja tanpa kewajiban mengganti kata sandi. Kata sandi saat ini tetap berlaku; menu Ganti kata sandi tersedia secara sukarela.
- Halaman login, navigasi Masuk/Panel Saya, sesi akun admin dan guru BK, penggantian kata sandi awal, dan logout POST.
- Database MySQL `siminat` dengan tabel `users` dan `login_attempts`; seed akun admin tidak menimpa akun yang sudah ada.
- CSRF, prepared statements, bcrypt, batas 5 percobaan login per IP selama 15 menit, dan validasi server.
- Foto khusus login dibuat ulang menggunakan GPT Image: konsultasi pilihan kuliah bersama guru BK; hero landing tetap memakai foto kelas.

- Hero awal SIMINAT berdasarkan referensi pengguna: foto kelas memenuhi layar, navigasi, judul editorial, CTA, dan tiga panel informasi.
- Konsep dan foto latar dibuat menggunakan GPT Image. Foto ditandai sebagai ilustrasi AI.
- Menu responsif dan dialog penjelasan tersedia dengan keyboard, Escape, penguncian fokus native, dan pengembalian fokus.
- Fitur klasifikasi belum diimplementasikan; CTA mengenalkan kategori rumpun jurusan.
