# Verifikasi video SIMINAT 9:16

Komposisi: 1080 × 1920, 30 fps, 41,748 detik. Tujuh adegan, musik pilihan pengguna tanpa narasi, demonstrasi interaksi pada screenshot asli.

## Pemeriksaan komposisi

Perintah: `npx hyperframes check --json --snapshots --samples 18`.

- PASS: lint 0 error dan 0 warning, sembilan file komposisi diperiksa.
- PASS: runtime 0 error; GSAP dibundel lokal pada assets/gsap.min.js.
- PASS: layout 0 error dan 0 warning. Overflow gambar demo adalah crop kamera yang disengaja; garis penutup memang berlanjut melewati tepi.
- PASS: 52 dari 52 pemeriksaan kontras lulus.
- PASS: snapshot 0,3; 3,5; 10,7; 15,5; 18; 20,4; 22,1; 28,4; 30; 32,5; 34,5; 41,7 detik diperiksa. Urutan label, posisi cursor, nilai, jalur keputusan, program studi dan akhir terbaca. Bukti: boards/verified/.
- PASS: timeline CLI mengenali grafik dan empat audio. Tujuh scene host dan progres memiliki baris terpisah.
- PASS: preview persisten mengembalikan HTTP 200 di http://localhost:3027/#project/siminat-dinamis.

## Delivery Gate antislop

- Hard Gate PASS: format portrait, pesan kunci dalam safe margin; tidak ada angka akurasi rekaan, testimoni rekaan atau identitas siswa asli; konten nasional menyertakan sumber resmi. CTA dalam MP4 adalah grafik demonstrasi; kontrol pemutaran dan penyuntingan berada di Studio.
- Purpose Gate PASS: warna berasal dari situs, aksen emas menandai pilihan dan jalur; screenshot digunakan sebagai bukti produk; potongan kamera menjaga ukuran tulisan; alasan visual dicatat pada frame.md.
- Liveliness PASS: ENERGY 3 / RHYTHM 3 / MOTION 3; variasi pertanyaan kinetik, panduan terang, foto kelas, demo form, diagram ID3, hasil dan penutup; marker keputusan menjadi motif bersama.
- Craftsmanship PASS: komposisi berjalan di Chrome; gerak dapat di-seek; screenshot menunjukkan fokus berbeda pada waktu berbeda; hasil contoh konsisten dengan capture/example.json; penutup menahan pesan hingga akhir.

## Batas verifikasi

Keyframes CLI tidak dapat menyelesaikan target dinamis helper `el()` pada frame masukan, sehingga bukti gerak menggunakan snapshot runtime berurutan. Temuan info pada sweep hanya terjadi selama teks pembuka sedang masuk melalui mask, bukan pada posisi baca akhir. Pemeriksaan statis tidak menggantikan pemeriksaan gambar hasil render.

## Ekspor

Output yang dituju: renders/siminat-dinamis-9x16.mp4. Perintah: `npx hyperframes render --output renders/siminat-dinamis-9x16.mp4 --fps 30 --quality delivery --workers 2`.

Ekspor selesai dan diverifikasi:

- PASS: file renders/siminat-dinamis-9x16.mp4, 5.611.995 byte, H.264, 1080 × 1920, DAR 9:16, 30 fps, 1.253 frame.
- PASS: durasi container 41,766667 detik. Selisih +0,018667 detik dari komposisi adalah pembulatan ke frame 30 fps.
- PASS: audio AAC stereo 48 kHz. FFmpeg volumedetect mendeteksi mean -21,8 dB dan peak -8,2 dB; audio terisi dan tidak terpotong pada batas digital.
- PASS: tujuh frame hasil decode MP4 diperiksa bersama pada boards/render-proof.jpg. Frame nasional 10,7 detik, demo 20,4 detik dan penutup 41,7 detik diperiksa pada resolusi penuh di boards/national-render.png, boards/demo-render.png dan boards/end-render.png. Hasil konsisten dengan snapshot runtime, tanpa reset atau akhir hitam.
- Jalur render: screenshot capture, hardware GPU Intel UHD, Chrome Headless 152; compile 1,0 detik, capture 64,9 detik, encode 13,7 detik, total 85,4 detik.

CLI tetap pada 0.8.167; pemeriksaan versi terbaru menyatakan pin sudah sesuai. Status kuota tidak diketahui (`usage_request_failed`).
