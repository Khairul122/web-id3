# Pembersihan struktur proyek

Tanggal: 17 Oktober 2026.

## Hasil

- Video sebelumnya dipindahkan dari `videos/siminat-pengenalan/` ke `videos/archive/siminat-pengenalan/`, termasuk source, aset, state editor, dan MP4.
- Delapan catatan iterasi lama dipindahkan ke `docs/archive/iterations/`. Rencana utama, panduan pengembangan, verifikasi metode, dan rancangan landing terbaru tetap di `docs/`.
- Konsep desain dipindahkan dari `docs/visuals/` ke `docs/archive/visuals/`.
- Empat PNG sumber dari `assets/img/` dipindahkan ke `docs/archive/visuals/source/`. Situs tetap menggunakan WebP yang sama pada jalur sebelumnya.
- Dua screenshot pendukung video yang tidak dipakai komposisi dipindahkan dari `assets/` video ke `capture/screenshots/`; script capture diperbarui agar keluaran berikutnya tetap teratur.
- Cache browser Playwright, pytest, bytecode Python, thumbnail, waveform, dan snapshot awal video yang sudah digantikan dihapus. Bukti final video pada `boards/verified/` dan frame hasil decode tetap disimpan.
- MP3 di root dihapus karena hash SHA-256 sama persis dengan musik yang tetap disimpan di kedua proyek video.
- `.gitignore` diperluas agar cache dan state sementara tidak menambah isi repositori pada pekerjaan berikutnya.
- Jalur dataset yang sebelumnya masih mengarah ke root diseragamkan menjadi `data/Dataset_Bersih_ID3.xlsx` pada controller impor, CLI impor, script pembentuk dataset, dan uji verifikasi.

## File yang tetap diperlukan

Controller dimuat secara dinamis oleh router; model dan service melalui autoloader. View laporan menggunakan nama dinamis `cetak-{jenis}`. File tersebut tidak dinyatakan mati hanya karena tidak mempunyai import statis.

Script `extract.py` dan `raw_labels.py` dipakai oleh `build_dataset.py`. `match_check.py` tetap berguna untuk audit pencocokan data penelitian. Dataset, dokumen sumber, upload, konfigurasi lokal, migrasi, pengujian, dan dependensi HyperFrames dipertahankan.

State dan history `.hyperframes/` juga dipertahankan. Folder ini bukan sekadar cache; menghapus semuanya akan menghilangkan konteks penyuntingan video.

## Verifikasi

Uji dataset sebelum perbaikan gagal dengan `FileNotFoundError` pada lokasi root. Setelah jalur diperbaiki, empat skenario ID3 lulus: semua data, MAN, MAS, dan tanpa Campuran.

- PASS: lint 98 file PHP dan sintaks script capture JavaScript.
- PASS: `php tests/id3_manual_test.php` dan `python -B tests/verify_id3_dataset.py`.
- PASS: `python -B tests/admin_http_test.py`, termasuk pembacaan dataset dari server, pratinjau impor, pembatalan, CRUD, laporan, autentikasi, dan hak akses.
- PASS: `python -B tests/landing_sections_test.py`, tujuh ukuran layar 360–1920 px, tanpa aset gagal maupun error console.
- PASS: `python -B tests/landing_motion_test.py`, termasuk navigasi, animasi dan reduced motion.
- PASS: 108 referensi aset lokal pada situs dan kedua proyek video ditemukan.
- PASS: 55 media arsip cocok dengan hash Git sebelum pemindahan; tidak ada perubahan isi media.
- PASS: MP4 baru 41,766667 detik dan MP4 lama 45 detik terbaca oleh ffprobe pada lokasi masing-masing.

Sebanyak 146 file dipindahkan dengan struktur internal tetap, 59 file sementara/duplikat terhitung dihapus (3.571.038 byte), dan folder cache pytest yang terkunci dibersihkan terpisah. Angka pemindahan termasuk isi folder proyek video yang kemudian cache-nya dibersihkan.

Manifest perpindahan dan penghapusan tersedia secara lokal di `output/cleanup-manifest.json`. Path lama dan tujuan disimpan agar arsip mudah ditemukan.
