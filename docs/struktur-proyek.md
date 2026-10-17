# Pembersihan struktur proyek

Tanggal: 17 Oktober 2026.

## Hasil

- Video sebelumnya dipindahkan dari `videos/siminat-pengenalan/` ke `videos/archive/siminat-pengenalan/`, termasuk source, aset, state editor, dan MP4.
- Delapan catatan iterasi lama dipindahkan ke `docs/archive/iterations/`. Rencana utama, panduan pengembangan, verifikasi metode, dan rancangan landing terbaru tetap di `docs/`.
- Konsep desain dipindahkan dari `docs/visuals/` ke `docs/archive/visuals/`.
- Empat PNG sumber dari `assets/img/` dipindahkan ke `docs/archive/visuals/source/`. Situs tetap menggunakan WebP yang sama pada jalur sebelumnya.
- Cache browser Playwright, pytest, bytecode Python, thumbnail, waveform, dan snapshot awal video yang sudah digantikan dihapus. Bukti final video pada `boards/verified/` dan frame hasil decode tetap disimpan.
- MP3 di root dihapus karena hash SHA-256 sama persis dengan musik yang tetap disimpan di kedua proyek video.
- `.gitignore` diperluas agar cache dan state sementara tidak menambah isi repositori pada pekerjaan berikutnya.
- Jalur dataset yang sebelumnya masih mengarah ke root diseragamkan menjadi `data/Dataset_Bersih_ID3.xlsx` pada controller impor, CLI impor, script pembentuk dataset, dan uji verifikasi.

## File yang tetap diperlukan

Controller dimuat secara dinamis oleh router; model dan service melalui autoloader. View laporan menggunakan nama dinamis `cetak-{jenis}`. File tersebut tidak dinyatakan mati hanya karena tidak mempunyai import statis.

Script `extract.py` dan `raw_labels.py` dipakai oleh `build_dataset.py`. `match_check.py` tetap berguna untuk audit pencocokan data penelitian. Dataset, dokumen sumber, upload, konfigurasi lokal, migrasi, pengujian, dan dependensi HyperFrames dipertahankan.

State dan history `.hyperframes/` juga dipertahankan. Folder ini bukan sekadar cache; menghapus semuanya akan menghilangkan konteks penyuntingan video.

## Verifikasi

Uji dataset sebelum perbaikan gagal dengan `FileNotFoundError` pada lokasi root. Setelah jalur diperbaiki, empat skenario ID3 lulus: semua data, MAN, MAS, dan tanpa Campuran. Hasil pengujian tambahan dicatat setelah pembersihan selesai.

Manifest perpindahan dan penghapusan tersedia secara lokal di `output/cleanup-manifest.json`. Path lama dan tujuan disimpan agar arsip mudah ditemukan.
