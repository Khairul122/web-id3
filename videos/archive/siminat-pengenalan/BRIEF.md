---
workflow: product-launch-video
flow: automation
storyboard: yes
message: "SIMINAT mengubah nilai rapor dan peminatan menjadi rekomendasi rumpun jurusan kuliah yang jelas dan bisa ditelusuri — bahan diskusi bersama guru BK."
destination: desktop/embed + social
aspect: 1920x1080 (primary) + 1080x1920 (variant)
language: id
audience: siswa kelas XI–XII dan guru BK MAN 4 Matangkuli & MAS Al-Muslimun Lhoksukon; guru pembimbing/penguji projek
length: 45s
angle: pengenalan menyeluruh — brand → masalah (bingung memilih jurusan) → solusi (rekomendasi dari nilai rapor) → cara kerja & metode ID3 → tur tampilan situs → CTA
narration: no
---

## Intent

Video pengenalan projek SIMINAT — situs klasifikasi minat jurusan kuliah berbasis metode ID3 — untuk siswa dan guru BK dua madrasah. Alur sesuai permintaan pengguna: apa itu SIMINAT → masalah (bingung memilih jurusan) → solusi (rekomendasi dari nilai rapor + peminatan) → cara kerja & metode ID3 → tampilan situs. Tanpa narasi suara: teks di layar + musik. Dua luaran: 16:9 (1920×1080, utama — desktop/embed) dan varian 9:16 (1080×1920 — sosial vertikal).

## Assets

- `assets/bgm.mp3` — musik latar pilihan pengguna (115 BPM, 60,9 dtk). Video memakai potongan 0–45 dtk; setiap potongan scene jatuh di downbeat (≈2,087 dtk); fade-in pendek + fade-out ±2,5 dtk. Disalin ke `assets/bgm.mp3`.
- Ilustrasi AI milik situs (disalin ke `capture/assets/` lalu di-stage ke `assets/`): `kelas-madrasah-ai.png` (hero), `jelajah-rumpun-ai.png`, `pendampingan-guru-ai.png`, `bimbingan-karier-ai.png`; logo `logo.svg`.
- Tangkapan layar situs live `capture/screenshots/`: `01-hero.png`, `02-rumpun.png`, `03-cara-kerja.png`, `04-metode.png`, `05-panduan.png`, `06-login.png`, dan plat penuh `full-page.png` (1920×5908). Inventaris lengkap: `capture/extracted/asset-descriptions.md`.
- Font: Inter (woff2 milik situs, di-stage untuk video) + Georgia (serif display sistem).

## Customizations

- **Dua format luaran.** 16:9 (1920×1080) sebagai komposisi utama; 9:16 (1080×1920) dibangun sebagai komposisi terpisah dengan tata letak disesuaikan (bukan crop buta).
- **Sinkron beat.** Semua batas scene di grid downbeat musik (115 BPM).
- Palet brand: navy `#071D31` / ivory `#FFFDF6` / gold `#F8C454` (dibaca via `colorStats` sehingga preset terang di-remix ke ground gelap).

## Notes

- **Jalur capture CLI HyperFrames tidak tersedia untuk host lokal** (`web-id3.test`, `localhost`): guard SSRF internal menolak alamat privat/loopback dengan 403 by design (tanpa override). Materi situs tetap diambil sebagai **tangkapan layar asli** situs live via browser Playwright, lalu dibundel ke `capture/`. Tidak ada screenshot sintetis.
- Privasi: foto/dokumen pribadi siswa (`data/IMG-*.jpg`) dan nama siswa **tidak digunakan**; angka/metode bersumber dari `docs/verifikasi-metode.md` (dokumen projek, terverifikasi).
- Tidak ada narasi TTS; HeyGen tidak diperlukan dan tidak di-sign-in. Musik dari file pengguna (host audio).
