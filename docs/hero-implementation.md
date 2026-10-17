# Hero SIMINAT

## Arah visual

Hero untuk siswa kelas XII dan guru BK, bergaya editorial institusi pendidikan.
Dial: ENERGY 2 / RHYTHM 2 / MOTION 1.

Referensi pengguna menjadi acuan foto layar penuh, hierarki judul di kiri, navigasi transparan, dan pita tiga panel informasi di bawah. Identitas SIMINAT tetap navy, ivory, dan kuning. Konsep terpilih: `docs/visuals/hero-concept.png`.

- Overlay gradasi melindungi kontras teks di atas foto (R-01).
- Georgia pada judul mengikuti karakter serif referensi; Inter pada navigasi dan isi mendukung keterbacaan. Ini penyesuaian tipografi khusus hero dari design.md (R-06).
- Pita tiga panel memakai ukuran sejajar karena ketiganya menjelaskan dasar pendekatan yang setara, sesuai referensi. Panel berupa informasi, bukan tautan (R-14).
- Ikon buku, pohon, dan topi wisuda mewakili nilai rapor, metode, dan pendampingan pendidikan (R-04).
- Aksen kuning berfungsi pada judul, aksi utama, dan identitas. Panah hanya pada CTA utama untuk menandai pembukaan penjelasan rumpun (R-08).
- Latar gelap diperlukan agar foto tetap terlihat dengan teks terang seperti referensi, bukan tema gelap aplikasi secara umum (R-21).
- Motif buku pada identitas menghubungkan simbol sekolah dan proses belajar; tidak menggunakan logo resmi sekolah.
- Animasi hanya transisi hover singkat. Preferensi reduced motion dihormati.

## Perilaku

Satu section hero. Semua menu selain Beranda membuka dialog penjelasan. Tombol utama membuka pengenalan Saintek, Soshum, dan Campuran, bukan prediksi. Dialog menjelaskan fitur klasifikasi masih dalam pengembangan.

Menu mobile menggunakan dialog native, tertutup dengan Escape, tombol tutup, atau backdrop. Dialog informasi mengembalikan fokus ke pemicu; bila berasal dari menu mobile, fokus kembali ke tombol menu. Tidak ada pemuatan API sehingga state loading/error data tidak diperlukan. Tanpa JavaScript, tampil pemberitahuan.

Implementasi: PHP native, CSS variables, vanilla JavaScript, ikon SVG lokal, dan font lokal. Tidak memerlukan database, Composer, npm, atau koneksi internet saat dibuka.

## Aset GPT Image

- Alat: built-in GPT Image (`image_gen.imagegen`).
- Konsep: `docs/visuals/hero-concept.png`.
- Foto asli hasil generasi: `assets/img/hero/kelas-madrasah-ai.png`.
- Aset web: `assets/img/hero/kelas-madrasah-ai.webp`, 1672 × 941, sekitar 78 KB. Hanya konversi format, tanpa perubahan adegan.
- Foto merupakan ilustrasi AI, bukan dokumentasi siswa sekolah. Label terlihat di bawah hero.

Prompt final foto:

```text
Use case: photorealistic-natural. Asset type: full-bleed landing-page background photograph, landscape wide 16:9, high resolution. Use the last generated SIMINAT website concept as VISUAL REFERENCE ONLY. Generate a NEW STANDALONE classroom photograph matching its photographic scene, lighting, teal uniforms, natural human expressions and composition. This must be ONLY the photograph with absolutely NO text, NO typography, NO logos or badges, NO navigation, NO UI, NO panels, NO watermark, NO dark gradient overlay (the website will add it using CSS). Scene: warm wood-paneled Indonesian madrasah classroom with senior students aged 17-18 in teal blue shirts, boys in black peci, girls in modest ivory hijab. Two students raising their hands naturally on the right half, engaged in classroom lesson, wooden desks and open notebooks. Main boy face around x=68%, y=48%, girl around x=88%, y=48%. Keep all raised hands inside frame with believable fingers. Left third must be relatively quiet softly out-of-focus wood paneling and one blurred student low in frame, leaving room for headline overlay; avoid major faces on left. Golden late afternoon natural window light from the right, cinematic documentary educational photography, warm realistic skin, authentic Indonesian features, rich but natural teal, deep wood browns, shallow depth of field. Camera at seated eye level, wide horizontal composition; keep faces clear and not enormous, show torso and desks, realism and welcoming mood. No school insignias, no lettering of any kind.
```

## Menjalankan

Dengan Laragon/Apache, buka `http://localhost/web-id3/` jika direktori www mengarah ke induk folder proyek.

Alternatif CLI, dari root proyek:

```powershell
php -S 127.0.0.1:8085 -t .
```

Server bawaan PHP hanya untuk pratinjau lokal dan tidak menerapkan .htaccess. Untuk hosting Apache, .htaccess melarang akses langsung ke data sumber, dokumentasi, script, log, dan file dataset.
