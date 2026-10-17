# Asset descriptions — SIMINAT (web-id3)

Canonical inventory of captured material. All screenshots are REAL captures of the live site
`http://web-id3.test/` (Playwright, 2026-10-17, viewport 1920×1080). Only these filenames may
be named as `asset_candidates`.

## Screenshots — `capture/screenshots/`

- `01-hero.png` — Landing hero (1920×1080). Deep navy ground; serif headline "Kenali Minatmu. Temukan Arah Kuliahmu." (2nd line gold italic); subtitle "Langkah awal mengenali rumpun jurusan kuliah melalui nilai rapor dan peminatanmu."; CTAs "Kenali Rumpun Jurusan" (gold button) + "Bagaimana cara kerjanya?"; right side: `kelas-madrasah-ai` illustration in an arch-cropped stage; value strip: "Berangkat dari Nilai Rapor" / "Metode ID3 yang Terbuka" / "Diskusi Bersama Guru BK".
- `02-rumpun.png` — Section "RUMPUN JURUSAN — Kenali bidangnya. Temukan minatmu." (1920×1080). Three numbered rows: 01 Saintek (Sains dan teknologi), 02 Soshum (Sosial dan humaniora), 03 Campuran (Pilihan lintas rumpun) beside the `jelajah-rumpun-ai` illustration.
- `03-cara-kerja.png` — Section "CARA KERJA — Dari nilai rapor ke bahan diskusi." (1920×1080). Four numbered steps: 01 Data siswa; 02 Kategori nilai; 03 Pohon ID3; 04 Diskusi hasil. Light paper ground.
- `04-metode.png` — Section "METODE ID3 — Keputusan yang bisa ditelusuri." (1920×1080). Formulas Entropy(S) = −∑ p(c) log2 p(c) and Gain(S,A) = Entropy(S) − ∑ (|Sv|/|S|) Entropy(Sv) with explanations + stop-condition list; right: decision-tree illustration stage (nodes "Atribut terpilih" → "Nilai A" / "Nilai B" → "Atribut berikutnya"; leaves Soshum / Saintek / Campuran) on navy with gold/ivory nodes. Caption: "Ilustrasi, bukan pohon hasil data siswa".
- `05-panduan.png` — Section "PANDUAN SISWA — Siapkan pilihanmu, bicarakan bersama." (1920×1080). Four checklist steps (siapkan nilai rapor → minta pendampingan guru BK → baca jalur keputusan → diskusikan bersama guru BK & orang tua), note "Untuk Guru BK", button "Masuk ke Panel".
- `06-login.png` — Panel login page "Masuk" (1920×1080). Navy split layout with `bimbingan-karier-ai` illustration; login form card.
- `full-page.png` — The whole landing as one plate: **1920×5908 px** (hero → rumpun → cara kerja → metode → panduan → footer). Pixel-exact source for scroll/flight animation over the real site.

## Images — `capture/assets/`

- `kelas-madrasah-ai.png` — AI illustration (1672×941). Bright madrasah classroom: students in teal/white uniforms raising hands, teacher at the whiteboard, warm window light.
- `jelajah-rumpun-ai.png` — AI illustration (1536×1024). Student exploring study-field paths/cards ("Jelajahi pilihanmu").
- `pendampingan-guru-ai.png` — AI illustration (1536×1024). Teacher/counselor guiding a student ("pendampingan guru").
- `bimbingan-karier-ai.png` — AI illustration (1122×1402, portrait). Career-guidance scene used on the login page.
- `logo.svg` — SIMINAT mark: gold line-art — open book + location pin + ascending bar chart.

## Brand facts (from the live site)

- Ground navy `#071D31` (deeper `#041625`), text ivory `#FFFDF6`, accent gold `#F8C454`; light paper sections `#F8F6F0`.
- Class colors: Saintek `#1F5F9E`, Soshum `#8A4B08`, Campuran `#6B3C8F`; success green `#1D6B3A`.
- Type: Georgia (serif display; gold italic second lines) + Inter (body/UI, uppercase tracked micro-labels).
- Taglines: "KENALI MINAT. RENCANAKAN MASA DEPAN." · "SIMINAT · Klasifikasi Minat Jurusan Kuliah".
- Schools: MAN 4 Matangkuli / MAS Al-Muslimun Lhoksukon. Public pages are aggregate-only; no student photos or names are usable.

## Proof numbers (from `docs/verifikasi-metode.md` — verified against the project docs, not shown on the landing)

- 247 siswa (147 berlabel); 9 atribut ID3.
- Model aktif (skenario Saintek–Soshum, 135 data): akurasi 5-fold cross-validation **88,9%**, F1 macro 84,1%, baseline kelas-mayoritas 74,8%.
- Atribut akar pohon: Peminatan (gain 0,4827).

## Music (project asset, not a frame candidate)

- `assets/bgm.mp3` — user-supplied track (source: `ssstik.io_1792218982847.mp3`), 115 BPM, 60,9 s; video uses the 0–45 s cut with cuts on the downbeat grid (≈2,087 s).
