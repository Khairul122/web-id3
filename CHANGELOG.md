# Changelog

## 2026-10-22 (sidebar per peran)

- Sidebar punya menu terpisah per peran. Admin: Kelola Data, Proses ID3, Administrasi (Pengguna, Laporan). Guru BK: Bimbingan (Cek Rekomendasi, Riwayat Prediksi, Data Siswa, Laporan) dan Referensi (hanya lihat) untuk model, pengujian, atribut, diskretisasi, dan sekolah. Siswa tetap tiga menu pribadi.
- Subjudul sidebar mengikuti peran (Panel Administrator, Panel Guru BK, Panel Siswa). Cek Rekomendasi menjadi menu aktif untuk guru BK dan siswa.
- Uji HTTP memeriksa isi sidebar admin dan guru BK serta menu aktif Cek Rekomendasi.

## 2026-10-22 (alamat panel /panel/)

- Prefiks URL panel diganti dari `/admin/` menjadi `/panel/` karena panel dipakai semua peran. Folder `admin/` menjadi `panel/`; `admin_url()`, redirect login, dan Router mengikuti. Folder `views/admin/` dan `admin.css` tidak berubah.
- `admin/index.php` tetap ada sebagai pengalih 303 dari `/admin/...` ke `/panel/...` (parameter query ikut) agar tautan lama berfungsi.
- Uji HTTP dan uji browser memakai alamat baru.

## 2026-10-22 (dashboard siswa dan prediksi massal semua siswa)

- Form Simulasi Rekomendasi siswa terisi otomatis dengan nilai rapor dan kategori dari data siswa; nilai yang belum ada tetap kosong untuk diisi atau dipilih manual. Draf yang sedang diedit tidak ditimpa.
- Dashboard siswa menampilkan kartu rekomendasi model aktif, profil nilai per rumpun, nilai mapel dengan nama lengkap, jalur keputusan, contoh prodi, dan riwayat prediksi.
- Prediksi massal punya pilihan cakupan: siswa tanpa label (bawaan, sesuai metode) atau semua siswa. Cakupan semua mengisi riwayat siswa berlabel dan melaporkan kecocokan dengan label asli. Ringkasan dashboard guru BK tetap menghitung siswa tanpa label saja.
- `tests/audit_data.php` mengaudit database: jumlah 247/147/100, domain atribut, kategori diskretisasi, prediksi tersimpan terhadap model aktif, dan tautan akun siswa.
- Database lokal: prediksi massal semua siswa dengan model aktif. Siswa tanpa label tetap Saintek 5 dan Soshum 95; 126 dari 147 siswa berlabel sesuai label (126 dari 135 tanpa Campuran = akurasi latih 93,3%).

## 2026-10-22 (akun login siswa dan guru BK)

- Database lokal `siminat` diisi 247 akun siswa dari data master dan satu akun Guru BK. Akun siswa ditautkan lewat `kode_siswa`; admin yang sudah ada tidak diubah.
- Batas kata sandi baru menjadi 8–72 byte pada form pengguna dan ganti kata sandi. Uji HTTP mencakup sandi 8 byte diterima dan 7 byte ditolak.

## 2026-10-22 (remodel halaman masuk)

- Halaman masuk memakai panel foto kelas dan form portal sekolah. Tata letak menyesuaikan ponsel, tablet, dan desktop.
- Jam WIB dan panduan fitur siswa, guru BK, serta administrator memberi interaksi tanpa mengubah alur login. Bantuan, validasi, dan navigasi keyboard tetap tersedia.
- Uji browser mencakup panduan peran, jam, alur autentikasi, serta judul dashboard guru BK.

## 2026-10-22 (panel multi-peran)

- Tambah peran siswa, tautan akun melalui kode siswa, dashboard dan navigasi per peran, serta pembatasan riwayat dan detail prediksi siswa.
- Admin dapat membuat akun siswa massal; admin dan guru BK dapat mereset sandi siswa. Sandi awal hanya tampil sekali di kartu cetak dan perubahan sandi mengakhiri sesi lama.
- Form simulasi siswa mengunci nama, sekolah, dan peminatan dari data server. Uji HTTP mencakup isolasi data, CSRF, akun massal, dan reset sandi.

## 2026-10-19 (perapian struktur sumber dan skrip penelitian)

- 66 dokumen penelitian dipisahkan ke `data/sumber/` berdasarkan sekolah dan jenis sumber, serta `data/referensi/` untuk proposal. Dataset siap impor tetap di `data/Dataset_Bersih_ID3.xlsx`.
- Empat skrip Python pengolahan sumber dikumpulkan di `scripts/penelitian/`; jalur input/output dan referensi perintah diperbarui.
- Peta struktur, alur E2E, README dua bahasa, panduan agen, dan peta sumber data disesuaikan. Tidak ada perubahan skema database atau URL aplikasi.

## 2026-10-17 (pembersihan struktur proyek)

- Video lama dipindahkan ke `videos/archive/`, catatan iterasi dan konsep visual ke `docs/archive/`, serta PNG sumber dipisahkan dari gambar WebP yang digunakan situs.
- Cache browser, bytecode, thumbnail, waveform dan snapshot video awal dibersihkan. MP3 duplikat di root dihapus setelah hash dicocokkan; aset sumber dan hasil video tetap tersedia.
- Lokasi dataset diseragamkan menjadi `data/Dataset_Bersih_ID3.xlsx` untuk impor admin, impor CLI, pembentukan dataset dan pengujian independen.
- README dan peta struktur ditambahkan; pola `.gitignore` diperluas untuk berkas sementara.

## 2026-10-17 (redesign section landing)

- Rumpun jurusan menjadi eksplorasi bertab dengan contoh bidang dan pertanyaan refleksi; cara kerja menjadi empat tahap interaktif dengan ilustrasi masing-masing.
- Metode ID3 memakai penjelasan bertahap, diagram berwarna sesuai kelas, replay, dan detail rumus yang dapat dibuka. Panduan memiliki checklist persiapan konsultasi dan progres sementara.
- Footer diperbarui dengan ajakan konsultasi dan tipografi besar. Warna dan tipografi mengikuti hero; markup, stylesheet, dan JavaScript hero tetap dipertahankan.
- Verifikasi browser tujuh viewport, tabs lewat keyboard, pemilih tahap, checklist, rumus, navigasi, reduced motion, tanpa JavaScript, dan akses panel guru BK. Perbandingan piksel hero desktop identik.

## 2026-10-17 (perkakas video HyperFrames)

- HyperFrames CLI dipasang sebagai devDependency (`package.json`, `package-lock.json`); `node_modules/` diabaikan Git. Aplikasi PHP tidak berubah dan tetap tanpa Node saat dibuka.
- Sepuluh core skills HyperFrames terpasang lewat `npx hyperframes skills update` untuk OpenCode dan agent lain (`~/.config/opencode/skills`, `~/.claude/skills`, `~/.agents/skills`); workflow pembuatan video lain dipasang sesuai kebutuhan.
- Prasyarat terverifikasi dengan `npx hyperframes doctor`: Node 24, FFmpeg 9, Chrome. Preview komposisi: `npx hyperframes preview`; render MP4: `npx hyperframes render`.

## 2026-10-17 (section landing)

- Implementasi docs/archive/iterations/plan-landing-sections.md: Rumpun Jurusan, Cara Kerja, Metode ID3, Panduan, dan footer.
- Navigasi dan CTA hero menuju section; menu ponsel menutup saat tautan dipilih dan memindahkan fokus ke bagian tujuan. Dialog informasi lama dihapus.
- Halaman umum hanya memuat penjelasan; ringkasan model aktif dan hasil penelitian dihapus dari landing.
- Dua foto baru dibuat dengan GPT Image, tersedia sebagai WebP responsif 768/1536 px. Skema pohon ilustratif berupa SVG dan rumus berupa teks HTML.
- Pengujian landing mencakup tujuh viewport, native anchors, keyboard, reduced motion, gambar, dan akses tanpa JavaScript. Tes autentikasi diperbarui untuk memeriksa tautan section.

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
