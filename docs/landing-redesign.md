# Redesign section landing

## Arah visual

Hero yang disetujui menjadi referensi: navy, krem, emas, Georgia untuk judul editorial dan Inter lokal untuk penjelasan. Hero, header, menu ponsel, dan animasi hero dipertahankan. Pemeriksaan antislop dilakukan selama pengerjaan sesuai pilihan pengguna.

ENERGY 3 / RHYTHM 3 / MOTION 2. Variasi komposisi berasal dari fungsi setiap bagian: eksplorasi rumpun bertab, alur bertahap, diagram keputusan, dan persiapan konsultasi. Gerak hanya untuk perpindahan konteks, masuknya konten, dan penelusuran diagram; reduced motion tetap didukung.

## Kontrak implementasi

- Rumpun: tiga pilihan yang dapat ditelusuri dengan mouse atau keyboard, contoh bidang, ketertarikan, dan pertanyaan refleksi. Semua isi dapat dibaca tanpa JavaScript.
- Cara kerja: empat tahapan dengan ilustrasi input, kategorisasi, percabangan, dan pembacaan hasil. Ilustrasi tidak menampilkan hasil atau data siswa nyata.
- Metode: diagram ID3 yang jelas, penjelasan entropy dan gain, rumus, tiga kondisi berhenti. Label ilustrasi tetap eksplisit.
- Panduan: daftar persiapan yang dapat dicentang, progres lokal sementara, foto pendampingan dan akses panel guru BK.
- Footer: ajakan konsultasi, identitas dua sekolah, navigasi ringkas dan kembali ke atas.
- Warna kelas hanya digunakan untuk membedakan rumpun; emas menandai pilihan aktif dan aksi utama. Foto yang sudah tersedia dipakai kembali dan tetap diberi alt sebagai ilustrasi.
- Tidak menambah library, prediksi publik, klaim akurasi, data pribadi, maupun perubahan autentikasi.

## Verifikasi

Uji browser pada 360, 390, 768, 1024, 1280, 1440, dan 1920 px. Periksa navigasi, tabs dengan panah/Home/End, tahapan, checklist, rumus, replay, reduced motion, tanpa JavaScript, console, dan aset. Bandingkan screenshot hero sebelum/sesudah secara piksel.

## Hasil verifikasi

- `php -l index.php` dan `node --check assets/js/landing-sections.js`: lulus.
- `python tests/landing_sections_test.py`: tujuh viewport lulus, tanpa overflow horizontal, semua tujuan menu tercapai, tanpa error console atau aset gagal. Rumus juga diperiksa dalam keadaan terbuka di setiap viewport.
- `python tests/landing_motion_test.py`: lulus untuk entrance hero, reveal, parallax, scroll spy, header, reading progress, replay pohon, perubahan reduced motion, dan menu ponsel.
- Tiga tab: klik mengganti satu panel; panah kiri/kanan berputar, Home/End memilih awal/akhir.
- Empat tahap: setiap pilihan menampilkan penjelasan dan ilustrasi yang sesuai.
- Checklist: centang menambah progres, hapus centang mengurangi progres, selesai menampilkan ajakan diskusi. Space bekerja di ponsel; reload mengosongkan progres.
- Detail metode: klik membuka dan menutup rumus serta kondisi berhenti. Tetap bekerja tanpa JavaScript.
- Navigasi footer dan hero: menuju anchor yang ada. Menu ponsel: fokus terkunci, Escape/tombol/backdrop menutup dan mengembalikan fokus.
- Akses guru BK: membuka `login.php` untuk pengunjung yang belum masuk.
- Tanpa JavaScript: ketiga rumpun dan keempat tahap tetap terbaca; navigasi alternatif tersedia.
- Hero: markup dibandingkan dengan versi awal dan identik. Screenshot 1440 px sebelum/sesudah identik pada seluruh piksel. `hero.css`, `hero.js`, `landing.css`, dan `tokens.css` tidak diubah.

Bukti visual dan hasil otomatis tersedia di `output/qa/` dan `output/qa/redesign/`. Screenshot elemen panjang dapat menangkap header fixed di tengah gambar karena cara browser mengambil screenshot; posisi header di viewport tetap diperiksa oleh tes navigasi.
