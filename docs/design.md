# Design System
> Rancangan awal proyek. Implementasi landing terbaru tercatat di [landing-redesign.md](landing-redesign.md); nilai warna dan tipografi yang digunakan aplikasi berada di `assets/css/tokens.css`.
## SIMINAT: Sistem Klasifikasi Minat Jurusan Kuliah

Gaya visual mengikuti referensi "Sekolah Nusantara". Ciri utamanya: hero foto siswa dengan overlay biru gelap, aksen kuning, pita statistik biru, dan kartu putih yang lembut. Nama, logo, dan foto memakai milik MAN 4 Matangkuli dan MAS Al-Muslimun. Jangan memakai foto atau nama dari referensi.

---

## 1. Prinsip

1. **Informatif dulu.** Setiap angka di layar berasal dari database, bukan teks statis.
2. **Satu aksi utama per layar.** Tombol kuning hanya untuk aksi utama (Cek Rekomendasi, Jalankan Training).
3. **Mobile first.** Desain mulai dari lebar 360px, lalu melebar.
4. **Tenang dan bersih.** Banyak ruang kosong, bayangan halus, sudut membulat.
5. **Gerak secukupnya.** Animasi membantu memahami, bukan hiasan. Hormati `prefers-reduced-motion`.

## 2. Warna (assets/css/tokens.css)

```css
:root{
  /* Primer: biru navy */
  --primary-950:#0B1B4D; --primary-900:#1E3A8A; --primary-800:#1E40AF;
  --primary-700:#1D4ED8; --primary-600:#2563EB; --primary-100:#DBEAFE; --primary-50:#EFF6FF;
  /* Aksen: kuning CTA */
  --accent-500:#FBBF24; --accent-600:#F59E0B; --accent-100:#FEF3C7;
  /* Netral */
  --ink-900:#0F172A; --ink-700:#334155; --ink-500:#64748B; --ink-300:#CBD5E1;
  --line:#E2E8F0; --surface:#FFFFFF; --bg:#F8FAFC;
  /* Chip ikon pastel (kartu Program Unggulan) */
  --chip-blue-bg:#DBEAFE;   --chip-blue-fg:#2563EB;
  --chip-amber-bg:#FEF3C7;  --chip-amber-fg:#D97706;
  --chip-green-bg:#DCFCE7;  --chip-green-fg:#16A34A;
  --chip-purple-bg:#F3E8FF; --chip-purple-fg:#9333EA;
  /* Warna kelas hasil ID3 (konsisten di pohon, grafik, badge) */
  --kls-saintek:#2563EB; --kls-soshum:#F59E0B; --kls-campuran:#9333EA;
  /* Status */
  --success:#16A34A; --warning:#D97706; --danger:#DC2626; --info:#0284C7;
  /* Overlay hero */
  --hero-overlay:linear-gradient(90deg,rgba(11,27,77,.92) 0%,rgba(30,58,138,.75) 45%,rgba(30,58,138,.15) 100%);
  --band-gradient:linear-gradient(135deg,#1E40AF 0%,#1E3A8A 60%,#172554 100%);
}
```

Aturan pakai:
- Teks di atas hero dan pita statistik: putih. Angka aksen ("+", "Th") memakai `--accent-500`.
- Kontras teks minimal 4.5:1. Teks abu (`--ink-500`) hanya di latar putih.
- Kelas Saintek selalu biru, Soshum selalu kuning-oranye, Campuran selalu ungu. Warna kelas juga diberi label teks, tidak mengandalkan warna saja.

## 3. Tipografi

| Peran | Font | Ukuran (mobile / desktop) | Berat | Line height |
|---|---|---|---|---|
| Display hero | Plus Jakarta Sans | 34px / 56px | 800 | 1.1, letter-spacing -0.02em |
| H2 judul section | Plus Jakarta Sans | 28px / 40px | 800 | 1.2 |
| H3 judul kartu | Plus Jakarta Sans | 18px / 20px | 700 | 1.3 |
| Eyebrow (KEUNGGULAN KAMI) | Inter | 12px | 700, uppercase, letter-spacing .12em | 1.4 |
| Body | Inter | 15px / 16px | 400 | 1.6 |
| Body kecil kartu | Inter | 13px / 14px | 400 | 1.6 |
| Angka statistik | Plus Jakarta Sans | 36px / 44px | 800, tabular-nums | 1 |
| Label statistik | Inter | 11px / 12px | 600, uppercase, letter-spacing .08em | 1.4 |

Gunakan `clamp()` untuk ukuran yang berubah, contoh `font-size:clamp(2.125rem,4vw + 1rem,3.5rem)`.

## 4. Spasi, Radius, Bayangan

- Skala spasi 4px: 4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 80, 96.
- Container: `max-width:1200px`, padding samping 16px (mobile), 24px (tablet), 32px (desktop).
- Jarak antar section: 64px mobile, 96px desktop.
- Radius: tombol 10px, kartu 16px, chip ikon 12px, badge pill 999px, input 10px.
- Bayangan:
  - `--shadow-sm: 0 1px 2px rgba(15,23,42,.06)`
  - `--shadow-md: 0 8px 24px rgba(15,23,42,.08)` (kartu)
  - `--shadow-lg: 0 16px 40px rgba(15,23,42,.12)` (kartu saat hover, modal)
  - `--shadow-cta: 0 8px 20px rgba(245,158,11,.35)` (tombol kuning)

## 5. Breakpoint

| Nama | Lebar | Perubahan utama |
|---|---|---|
| xs | < 480px | 1 kolom, navbar jadi tombol menu, hero teks rata kiri dan foto di belakang |
| sm | >= 480px | Stat band 2 x 2 |
| md | >= 768px | Kartu fitur 2 kolom, sidebar admin jadi drawer |
| lg | >= 1024px | Menu navbar penuh, kartu fitur 4 kolom, stat band 4 kolom, sidebar admin tetap |
| xl | >= 1280px | Container maksimal |

## 6. Komponen

### 6.1 Navbar publik
- Tinggi 72px. Transparan di atas hero. Setelah scroll 24px berubah jadi `rgba(11,27,77,.9)` dengan `backdrop-filter:blur(12px)`.
- Kiri: logo kotak biru 36px dengan huruf putih, lalu nama "SIMINAT" (bold, putih).
- Tengah-kanan: Beranda, Tentang, Metode ID3, Statistik, Informasi (dropdown: Pengumuman, Galeri), Kontak.
- Menu aktif: pill latar `rgba(255,255,255,.12)`, teks `--accent-500`.
- Ujung kanan: tombol kuning kecil **Cek Rekomendasi**.
- Mobile (< 1024px): ikon menu membuka drawer dari kanan (lebar 84vw, maks 360px), latar navy, item menu 48px tinggi, tombol CTA penuh di bawah. Fokus terkunci di drawer, tutup dengan Esc atau ketuk overlay.

### 6.2 Hero slider
- Tinggi: `min-height:620px` desktop, `100svh` maks 640px di mobile.
- Foto siswa kedua sekolah sebagai latar (`object-fit:cover`, fokus ke wajah di kanan), ditimpa `--hero-overlay`.
- Dekorasi: lengkung biru besar transparan di kiri bawah dan kartu ikon kaca melayang (`backdrop-filter:blur(8px)`, border putih 20%). Ikon: graduation-cap dan users.
- Konten kiri (maks 620px):
  1. Badge pill: titik kuning + "Tahun Ajaran 2025/2026". Border `--accent-500` 40%, latar gelap transparan.
  2. Judul: "Temukan Jurusan Kuliah yang Tepat" (slide 1). Slide lain: "Keputusan Berbasis Data, Bukan Tebakan", "Dua Madrasah, Satu Sistem".
  3. Subjudul putih 85% opasitas.
  4. Tombol: **Cek Rekomendasi →** (kuning) dan **Pelajari Metode** (outline putih).
- Indikator slide kanan bawah: titik 8px, aktif melebar 28px kuning.
- Teks "SCROLL" dan panah di tengah bawah, animasi naik turun pelan.
- Autoplay 6 detik, berhenti saat hover atau fokus. Bisa geser (swipe) di mobile. Gambar slide berikut dimuat lazy.

### 6.3 Pita statistik
- Latar `--band-gradient`, padding 56px atas bawah.
- 4 item: ikon dalam kotak 48px (latar `rgba(255,255,255,.1)`, radius 12px, ikon kuning), angka besar putih, label kecil uppercase.
- Isi dinamis dari DB:
  | Ikon | Angka | Label |
  |---|---|---|
  | users | 247 | TOTAL SISWA |
  | database | 147 | DATA LATIH |
  | git-branch | 9 | ATRIBUT ID3 |
  | target | 8x% | AKURASI MODEL |
- Angka naik dari 0 (count-up 1,2 detik) saat masuk viewport. Angka aksen ("+" atau "%") berwarna kuning.

### 6.4 Section header
- Eyebrow biru kecil uppercase, judul H2 hitam, deskripsi abu maks 640px, semua rata tengah.

### 6.5 Kartu fitur (Program Unggulan)
- Latar putih, radius 16px, padding 24px, `--shadow-md`.
- Chip ikon 48px pastel di kiri atas, judul H3, deskripsi 3 sampai 4 baris.
- Hover: naik 4px, `--shadow-lg`, chip ikon sedikit membesar (scale 1.05). Transisi 200ms.
- Isi untuk SIMINAT:
  | Chip | Judul | Isi |
  |---|---|---|
  | biru, graduation-cap | Rekomendasi Rumpun | Saran Saintek atau Soshum dari nilai rapor dan peminatan. |
  | kuning, git-branch | Pohon Keputusan | Alasan rekomendasi terlihat jelas dalam bentuk jalur keputusan. |
  | hijau, book-open | Data Dua Madrasah | Dilatih dari data MAN 4 Matangkuli dan MAS Al-Muslimun. |
  | ungu, bar-chart-3 | Akurasi Teruji | Diuji dengan k-fold cross validation dan confusion matrix. |

### 6.6 Alur kerja (Beranda)
- 4 langkah horizontal di desktop, vertikal di mobile, dihubungkan garis putus-putus.
- Isi Data → Diskretisasi → Pohon ID3 → Rekomendasi. Nomor dalam lingkaran biru.

### 6.7 Tombol
| Varian | Gaya |
|---|---|
| Primary (CTA) | Latar `--accent-500`, teks `--ink-900` bold, `--shadow-cta`, hover `--accent-600` |
| Secondary | Latar `--primary-700`, teks putih |
| Outline terang | Border putih 40%, teks putih, hover latar putih 10% |
| Outline | Border `--line`, teks `--ink-700` |
| Ghost | Tanpa border, teks `--primary-700` |
| Danger | Latar `--danger`, teks putih |
Tinggi 44px (min target sentuh), padding 20px, ikon 18px. Status loading: spinner dan teks "Memproses...". Fokus: outline 3px `--primary-100`.

### 6.8 Badge
- Pill 24px, teks 12px bold.
- Kelas: Saintek (biru muda, teks biru), Soshum (kuning muda, teks oranye), Campuran (ungu muda, teks ungu).
- Kategori nilai: Tinggi hijau, Sedang biru, Rendah merah muda, Tidak Ada abu.

### 6.9 Form
- Label di atas input, 14px semi bold. Input 44px, border `--line`, fokus border `--primary-600` dan ring 3px `--primary-100`.
- Pesan error merah di bawah input, ikon alert, juga `aria-describedby`.
- Form Cek Rekomendasi dibagi 3 langkah (stepper): 1 Sekolah dan peminatan, 2 Nilai mapel, 3 Hasil. Mobile: satu langkah per layar dengan tombol Lanjut tetap di bawah.
- Input nilai memakai `type="number" inputmode="numeric" min="0" max="100"` dan menampilkan kategori langsung (badge) saat diketik.

### 6.10 Kartu hasil rekomendasi
- Header berwarna sesuai kelas, ikon besar, teks "Rekomendasi: Saintek".
- Bar keyakinan dari distribusi daun (contoh Saintek 86%, Campuran 14%).
- Jalur keputusan: daftar bernomor "Peminatan = MIPA → Kat_MTK = Tinggi → Saintek", tiap langkah berupa chip yang tersambung panah.
- Contoh prodi per rumpun (chip abu).
- Tombol: Cetak hasil, Ulangi.
- Catatan kecil: "Hasil ini adalah rekomendasi, bukan keputusan akhir. Diskusikan dengan guru BK."

### 6.11 Visual pohon keputusan
- Node atribut: kartu putih border biru, nama atribut bold, jumlah sampel dan entropy kecil.
- Cabang: garis abu dengan label nilai atribut dalam pill.
- Daun: kartu berwarna kelas, nama kelas dan distribusi.
- Interaksi: klik node untuk lipat/buka, tombol Perbesar/Perkecil/Reset, geser dengan drag. Tooltip berisi distribusi.
- Mobile < 768px: berubah jadi accordion bertingkat dengan indentasi 16px per level.

### 6.12 Tabel data (admin)
- Header lengket, baris belang halus, hover baris `--primary-50`.
- Toolbar atas: cari, filter (sekolah, kelas, label), tombol tambah dan import.
- Mobile: tiap baris jadi kartu (nama di atas, data lain sebagai pasangan label dan nilai).
- Pagination 20 baris, info "Menampilkan 1 sampai 20 dari 247".

### 6.13 Confusion matrix
- Grid 3 x 3, warna sel heatmap biru (makin banyak makin gelap), angka di tengah.
- Diagonal (prediksi benar) diberi border hijau.
- Di bawahnya kartu metrik: Akurasi, Precision, Recall, F1 (macro).

### 6.14 Grafik (Chart.js)
- Font Inter 12px, grid `--line`, tanpa border chart.
- Warna kelas sesuai token. Donut untuk distribusi label, bar bertumpuk untuk per sekolah, line untuk akurasi per fold.
- Selalu ada judul dan teks ringkasan di bawah grafik untuk pembaca layar.

### 6.15 Layout admin
- Sidebar 260px navy (`--primary-950`), item 44px, ikon Lucide, item aktif latar putih 10% dan garis kuning 3px di kiri.
- Topbar putih 64px: judul halaman, breadcrumb, nama pengguna.
- Konten di `--bg` dengan kartu putih.
- Mobile: sidebar jadi drawer, topbar punya tombol menu.

### 6.16 Lainnya
- Toast di kanan atas (mobile: bawah), hilang sendiri 4 detik.
- Modal konfirmasi untuk hapus dan training ulang.
- Skeleton loading abu berkilau untuk tabel dan grafik.
- Empty state: ilustrasi ikon besar abu, judul, satu tombol aksi.
- Footer publik: latar `--primary-950`, 4 kolom (tentang, menu, sekolah, kontak), baris hak cipta.

## 7. Halaman dan Susunan

### Beranda
1. Navbar
2. Hero slider (6.2)
3. Pita statistik (6.3)
4. Program Unggulan: eyebrow "KEUNGGULAN SISTEM", judul "Fitur Unggulan", 4 kartu (6.5)
5. Alur kerja (6.6)
6. Ringkasan hasil: donut distribusi label dan kartu akurasi model aktif
7. Pengumuman terbaru (3 kartu dengan gambar)
8. CTA band kuning: "Siap menemukan rumpun jurusanmu?" dan tombol Cek Rekomendasi
9. Footer

### Metode ID3
- Penjelasan singkat entropy dan gain dengan rumus.
- Kalkulator interaktif: isi jumlah Saintek, Soshum, Campuran, nilai entropy dihitung langsung.
- Tabel gain akar dari model aktif dengan bar horizontal per atribut.

### Statistik
- Filter sekolah. Grafik distribusi label, peminatan, kategori nilai. Semua data agregat.

### Cek Rekomendasi
- Stepper 3 langkah (6.9), hasil (6.10).

## 8. Interaksi dan Animasi

| Elemen | Efek | Durasi |
|---|---|---|
| Section saat masuk layar | Fade dan naik 16px (IntersectionObserver) | 400ms |
| Angka statistik | Count-up | 1200ms |
| Kartu | Hover naik 4px | 200ms |
| Navbar | Berubah latar saat scroll | 200ms |
| Slider | Crossfade | 700ms |
| Tombol | Tekan scale .98 | 100ms |
Bila `prefers-reduced-motion: reduce`, semua animasi mati dan angka langsung tampil.

## 9. Aksesibilitas

1. Semua gambar punya `alt`. Gambar dekoratif `alt=""`.
2. Navigasi keyboard penuh, urutan fokus logis, link "Lewati ke konten".
3. Slider punya tombol jeda dan `aria-live="polite"`.
4. Target sentuh minimal 44 x 44px.
5. Struktur heading berurutan (satu H1 per halaman).
6. Bahasa halaman `lang="id"`.

## 10. Aset

- Foto hero: foto asli siswa MAN 4 Matangkuli dan MAS Al-Muslimun (izin sekolah), lebar 1920px, format WebP < 250 KB, fallback JPG.
- Logo: logo madrasah resmi atau monogram "S" untuk SIMINAT.
- Ikon: Lucide, stroke 2, ukuran 20 sampai 24px.
- Simpan font, Lucide, dan Chart.js di `assets/vendor/` sebagai cadangan bila tanpa internet.
