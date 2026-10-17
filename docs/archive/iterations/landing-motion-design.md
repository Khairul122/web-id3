# Arah visual dan gerak landing

Design Read: landing informasi publik bagi siswa XII, orang tua, dan guru BK; editorial akademik yang lebih ekspresif. Permintaan pengguna untuk tampilan dinamis menggantikan arah gerak minimal sebelumnya. ENERGY 3 / RHYTHM 3 / MOTION 3; variasi 8/10, gerak 6/10, kepadatan 4/10.

## Ekstraksi referensi GPT Image

| Section | Temuan visual | Terjemahan ke source |
|---|---|---|
| Hero | Foto dominan kanan, judul Georgia tiga baris, garis bawah gold, CTA kontras, tiga manfaat di dasar | Foto produksi lama dipertahankan; judul masuk per baris, foto bergeser pelan saat scroll, panah CTA merespons hover. Nama sekolah serta menu mengikuti data SIMINAT. |
| Rumpun | Judul serif dengan italic, nomor besar di kanan, bingkai foto bergeser, caption navy menimpa foto | Dua kolom dengan bobot 1.05:1, angka 44–68px, bingkai 12px, caption terpisah dari label AI; baris definisi masuk bergiliran. |
| Cara kerja | Empat tahap turun bertingkat, garis penghubung, pita percakapan navy | Empat kolom offset 0/32/64/96px di desktop, dua kolom di tablet, urutan vertikal di ponsel; nomor tetap urutan proses. |
| Metode | Kontras navy, judul italic gold, diagram terangkat dan fokus | Panggung bersudut garis gold, tanpa ornamen batu/daun; SVG source menggambar jalur dan menampilkan node bertahap. Rumus dan kriteria mengikuti source yang benar, bukan teks generasi. |
| Panduan | Daftar editorial kiri, foto pendampingan kanan, panel guru BK menimpa bagian bawah | Foto asli GPT dari aset produksi tetap; panel navy bertumpuk di tablet/desktop, tersusun pada ponsel; CTA gold. |
| Footer | Ajakan besar sebelum tiga kelompok informasi | Judul percakapan 32–66px, tombol kembali ke atas desktop, navigasi footer tetap tersedia di semua ukuran. |

Referensi adalah panduan komposisi. Nama sekolah, fitur, rumus, label ilustrasi, logo, dan kontak pada hasil generasi tidak menjadi sumber fakta. Tidak memasukkan school branding, menu tes minat/informasi kampus, klaim, atau rumus yang tidak sesuai aplikasi. Semua gambar referensi hanya disimpan di docs dan tidak dimuat halaman.

## Perilaku gerak

- Web Animations API untuk pembuka judul (1.1 detik, stagger 140ms), reveal isi (800ms), foto (1.1 detik), dan diagram (1.7 detik total).
- IntersectionObserver menjalankan animasi masuk satu kali saat elemen terlihat. Teks tetap terlihat secara default bila JavaScript gagal/dimatikan.
- Scroll pasif dengan satu requestAnimationFrame menghitung header, navigasi aktif, progres baca, dan drift foto; tidak mengubah kendali scroll pengguna.
- Parallax terbatas 64px pada hero dan 12px pada foto isi. Tidak ada animasi berulang tanpa henti.
- Tombol Putar ulang ilustrasi menggambar diagram kembali; disabled selama pemutaran. Diagram tetap berlabel ilustrasi.
- prefers-reduced-motion menonaktifkan seluruh gerak dan parallax, termasuk bila preferensi berubah saat animasi berjalan. Konten SVG kembali terlihat.
- Header menetap setelah scroll 72px; scroll-margin memberi ruang agar tujuan navigasi tidak tertutup header.

Kedalaman berasal dari bingkai dan caption foto, tumpukan panel pendampingan, panggung diagram, dan header yang terangkat. Bayangan tidak diterapkan pada seluruh elemen. Tipografi Georgia, Inter lokal, navy, ivory, dan gold meneruskan identitas SIMINAT. Tidak ada dependensi baru.

## Prompt dan aset konsep

Dibuat dengan built-in GPT Image, mode ui-mockup; tidak menggunakan fallback CLI.

### hero

Aset: docs/archive/visuals/landing/motion/hero-concept.png

```text
Generate a single horizontal high fidelity website section design reference, 1536x1024 landscape composition. A cinematic full viewport opening for SIMINAT, an Indonesian school university-interest guidance site. Navy overlay over classroom photo Indonesian students teal uniforms and black peci raising hands. Top small school bar, SIMINAT book emblem left, 5 links and gold-outline Masuk right. Huge left aligned Georgia headline 3 lines 'Kenali Minatmu. Temukan Arah Kuliahmu.' last italic gold with hand-drawn underline. Right photography dominant. Gold rectangular CTA Kenali Rumpun Jurusan with circular arrow inset. Bottom 3 slim editorial benefit columns, with gold numbers and icons. Subtle editorial gold line and visible scroll cue. Meaningful depth and luminous photo, no floating decorative nonsense. Shared design language: ivory #fffdf6, navy #071d31, gold #f8c454, Georgia serif editorial display and Inter body. Professional Indonesian school brand, energetic refined academic art direction. One section only, readable typography, implementation-ready screenshot, no browser chrome. This is a design reference rather than a production photo.
```

### rumpun

Aset: docs/archive/visuals/landing/motion/rumpun-concept.png

```text
Generate a single horizontal high fidelity website section design reference, 1536x1024 landscape composition. ONE section Rumpun Jurusan on warm ivory. Left editorial header Kenali bidangnya. Temukan ketertarikanmu. with gold italic second phrase, short intro. Under it large landscape library photograph Indonesian students teal uniforms, offset thin gold frame at right/bottom, navy floating caption 'Mulai dari rasa ingin tahu.' flush bottom right. Right 3 handsome stacked full-width editorial bands Saintek, Soshum, Campuran, each has oversized fine gold 01/02/03, descriptive subtitle, body. Not rounded cards. Thin rules, hover-like cream highlight of one row with gold left edge. Asymmetric layout, generous breathing room. Shared design language: ivory #fffdf6, navy #071d31, gold #f8c454, Georgia serif editorial display and Inter body. Professional Indonesian school brand, energetic refined academic art direction. One section only, readable typography, implementation-ready screenshot, no browser chrome. This is a design reference rather than a production photo.
```

### cara-kerja

Aset: docs/archive/visuals/landing/motion/cara-kerja-concept.png

```text
Generate a single horizontal high fidelity website section design reference, 1536x1024 landscape composition. ONE Cara Kerja section on light warm paper. Editorial header 'Dari nilai rapor ke bahan diskusi.' dark navy huge Georgia, gold italic 'bahan diskusi.' Intro on right. Below 4 stepped editorial columns: 01 Data siswa; 02 Kategori nilai; 03 Pohon ID3; 04 Diskusi hasil. Each white square top border gold, huge gold number with small semantic line icon, heading and short body. Columns stagger downward 24px each, connected by delicate gold progression line. Full-width navy bottom band 'Hasil menjadi bahan percakapan bersama guru BK.' No rounded bento, no gradients. Shared design language: ivory #fffdf6, navy #071d31, gold #f8c454, Georgia serif editorial display and Inter body. Professional Indonesian school brand, energetic refined academic art direction. One section only, readable typography, implementation-ready screenshot, no browser chrome. This is a design reference rather than a production photo.
```

### metode

Aset: docs/archive/visuals/landing/motion/metode-concept.png

```text
Generate a single horizontal high fidelity website section design reference, 1536x1024 landscape composition. ONE Metode ID3 section deep navy. Left huge Georgia heading 'Keputusan yang bisa ditelusuri.' gold italic second line, readable copy, two handsome formula rows Entropy(S) and Gain(S,A), three stopping criteria below. Right raised navy-dark open illustration stage with delicate corner strokes, gold caption 'Dari atribut menuju rekomendasi'. Gold line tree diagram: root Atribut terpilih, branch Atribut berikutnya and Soshum, leaves Saintek and Campuran. Gold connectors, ivory labels. Illustration disclaimer below. Strong depth, fine rules, no fake dashboard. Shared design language: ivory #fffdf6, navy #071d31, gold #f8c454, Georgia serif editorial display and Inter body. Professional Indonesian school brand, energetic refined academic art direction. One section only, readable typography, implementation-ready screenshot, no browser chrome. This is a design reference rather than a production photo.
```

### panduan

Aset: docs/archive/visuals/landing/motion/panduan-concept.png

```text
Generate a single horizontal high fidelity website section design reference, 1536x1024 landscape composition. ONE Panduan section warm ivory. Left eyebrow PANDUAN, huge editorial heading 'Siapkan pilihanmu, bicarakan bersama.' with gold italic second phrase. 4 compact numbered student instructions with thin lines. Right portrait-oriented editorial photo female navy hijab teacher counseling student ivory hijab and teal blazer, with offset gold frame. Overlapping navy caption panel lower part with title 'Untuk Guru BK', short text and gold 'Masuk ke Panel' CTA. Good readable contrast. Magazine-style asymmetry, thoughtful depth, no excessively rounded cards. Shared design language: ivory #fffdf6, navy #071d31, gold #f8c454, Georgia serif editorial display and Inter body. Professional Indonesian school brand, energetic refined academic art direction. One section only, readable typography, implementation-ready screenshot, no browser chrome. This is a design reference rather than a production photo.
```

### footer

Aset: docs/archive/visuals/landing/motion/footer-concept.png

```text
Generate a single horizontal high fidelity website section design reference, 1536x1024 landscape composition. ONE footer section deep navy for SIMINAT school guidance. Across top very large editorial Georgia callout 'Pilihan yang matang dimulai dari percakapan.' 'percakapan' italic gold. Right circular outlined arrow up to return home. Below three columns book logo SIMINAT plus short mission; Sekolah with MAN 4 Matangkuli and MAS Al-Muslimun Lhoksukon; Informasi & bantuan 'Hubungi guru BK di sekolahmu'. Thin gold horizontal rule. Bottom tiny menu links and AI image disclosure. Beautiful typographic rhythm, understated tactile navy background, no fake contacts. Shared design language: ivory #fffdf6, navy #071d31, gold #f8c454, Georgia serif editorial display and Inter body. Professional Indonesian school brand, energetic refined academic art direction. One section only, readable typography, implementation-ready screenshot, no browser chrome. This is a design reference rather than a production photo.
```
