# Hero SIMINAT

## Arah visual

Hero untuk siswa kelas XII dan guru BK, bergaya editorial institusi pendidikan.
Dial: ENERGY 2 / RHYTHM 2 / MOTION 1.

Referensi pengguna menjadi acuan foto layar penuh, hierarki judul di kiri, navigasi transparan, dan pita tiga panel informasi di bawah. Identitas SIMINAT tetap navy, ivory, dan kuning. Konsep terpilih: `docs/archive/visuals/hero-concept.png`.

- Overlay gradasi melindungi kontras teks di atas foto (R-01).
- Georgia pada judul mengikuti karakter serif referensi; Inter pada navigasi dan isi mendukung keterbacaan. Ini penyesuaian tipografi khusus hero dari design.md (R-06).
- Pita tiga panel memakai ukuran sejajar karena ketiganya menjelaskan dasar pendekatan yang setara, sesuai referensi. Panel berupa informasi, bukan tautan (R-14).
- Ikon buku, pohon, dan topi wisuda mewakili nilai rapor, metode, dan pendampingan pendidikan (R-04).
- Aksen kuning berfungsi pada judul, aksi utama, dan identitas. Panah hanya pada CTA utama untuk menandai pembukaan penjelasan rumpun (R-08).
- Latar gelap diperlukan agar foto tetap terlihat dengan teks terang seperti referensi, bukan tema gelap aplikasi secara umum (R-21).
- Motif buku pada identitas menghubungkan simbol sekolah dan proses belajar; tidak menggunakan logo resmi sekolah.
- Animasi hanya transisi hover singkat. Preferensi reduced motion dihormati.

## Perilaku

Halaman publik kini terdiri dari hero, Rumpun Jurusan, Cara Kerja, Metode ID3, Panduan, dan footer sesuai docs/archive/iterations/plan-landing-sections.md. Tombol hero menuju #rumpun dan #cara-kerja. Navigasi desktop serta ponsel memakai tautan ke section yang tersedia; Masuk/Panel Saya tetap menuju halaman akun.

Menu mobile menggunakan dialog native, tertutup dengan Escape, tombol tutup, backdrop, atau pemilihan tautan. Fokus dikunci selama menu terbuka; pemilihan tautan mengarahkan fokus ke section tujuan. Dialog informasi dan template lama dihapus. Penjelasan umum dapat dibaca tanpa JavaScript, dengan navigasi alternatif pada noscript.

Implementasi: PHP native, CSS variables, vanilla JavaScript, ikon SVG lokal, dan font lokal. Section publik berisi penjelasan umum tanpa meminta data siswa, jumlah data, atau hasil model. Login dan panel tetap menggunakan autentikasi yang sudah tersedia. Tidak ada Composer, npm, atau CDN yang dibutuhkan saat halaman dibuka.

## Section tambahan

- Rumpun Jurusan: dua kolom, pengantar dan foto di kiri, tiga definisi bergaris di kanan. Baris definisi digunakan karena pembaca perlu membandingkan rumpun, bukan membuka kartu fitur.
- Cara Kerja: empat tahap sesuai alur sistem. Nomor Georgia besar mempertahankan motif editorial; penghubung horizontal menjadi vertikal pada ponsel.
- Metode ID3: latar navy menandai bagian teknis. Rumus Entropy dan Gain berupa HTML dengan minus U+2212, dilengkapi tiga kriteria berhenti sesuai docs/verifikasi-metode.md. Diagram SVG statis diberi label ilustrasi, bukan hasil data siswa.
- Panduan: daftar tindakan siswa dan blok guru BK dengan CTA ke akun. Foto pendampingan memperlihatkan konteks diskusi. Ukuran judul dan label pohon disesuaikan untuk ponsel agar tetap terbaca.
- Footer: identitas, madrasah, dan ajakan menghubungi guru BK memakai tiga kelompok berbeda. Tautan kembali menuju section; tidak menambahkan email atau telepon yang belum diberikan.
- Latar ivory/paper menjaga keterbacaan teks panjang; warna gold-ink dipakai untuk teks aksen pada latar terang. Seluruh warna memakai token yang sudah ada. Radius foto 3px dan tanpa shadow tambahan.

Konsep setiap section dibuat terpisah dengan GPT Image sebelum implementasi, lalu diterjemahkan menjadi HTML/CSS. Konsep disimpan di docs/archive/visuals/landing/. Foto yang dipakai pada halaman dibuat sebagai aset standalone, bukan potongan gambar UI. Daftar aset dan prompt: docs/archive/iterations/landing-image-prompts.md. Hasil pengujian: docs/archive/iterations/landing-validation.md.

## Aset GPT Image

- Alat: built-in GPT Image (`image_gen.imagegen`).
- Konsep: `docs/archive/visuals/hero-concept.png`.
- Foto asli hasil generasi: `docs/archive/visuals/source/hero/kelas-madrasah-ai.png`.
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
