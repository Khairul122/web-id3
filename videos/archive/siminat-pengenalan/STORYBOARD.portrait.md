---
format: 1080x1920
duration: 45s
message: "SIMINAT mengubah nilai rapor dan peminatan menjadi rekomendasi rumpun jurusan kuliah yang jelas dan bisa ditelusuri — bahan diskusi bersama guru BK."
arc: PAS + Demo Loop — hook → pain → solution → cara kerja → bukti → tur situs → CTA
audience: siswa kelas XI–XII dan guru BK MAN 4 Matangkuli & MAS Al-Muslimun Lhoksukon; guru pembimbing/penguji projek
mode: collaborative
music: none
captions: skipped — no narration; on-screen type is the content
variants: "1080x1920 (9:16) — komposisi portrait terpisah, dibangun setelah pass 16:9 disetujui (konten ditata ulang, bukan crop buta)"
audio: host — assets/bgm-45.mp3 (potongan 0–45 dtk dari assets/bgm.mp3, 115 BPM, fade-in 0,4 dtk + fade-out 2,6 dtk)

---

Tanpa narasi (tidak ada `SCRIPT.md`): teks di layar + musik host. Semua batas frame jatuh di downbeat **6.165 · 10.335 · 16.595 · 24.94 · 31.21 · 37.47 dtk** (bar = 2,09 dtk @115 BPM; frame 1 mulai 0,0; total 45,0 dtk). Perancangan visual mengikuti `frame.md` (biennale-yellow di-remix ke brand: ground navy, ink ivory, aksen gold; display Georgia, body/data Inter).

## Video direction

- **Palette (dari `frame.md`, verbatim):** ground `colors.paper` navy #071D31 di setiap frame; SEMUA teks dan hairline `colors.ink` ivory #FFFDF6 (satu warna teks, tanpa warna kedua); `colors.sun` gold #F8C454 hanya sebagai sun-bloom / yellow-panel / block-tile / satu frasa italik yang ditekankan; ember-bloom #1F5F9E subordinat 15–22% di sudut berlawanan. Navy-on-gold untuk fill panel; ivory-on-navy untuk ground. Satu sun-bloom per frame (frame tanpa bloom terbaca sebagai template CMS). 0 radius, tanpa shadow, border hanya 1px hairline. Sparse 55–60%; ledger (F4) satu-satunya frame padat — densitas lewat repetisi hairline, bukan warna.
- **Motion grammar:** tidak ada voiceover — reveal dipaced ke grid downbeat musik (bar = 2,09 dtk; sub-beat ≈0,52 dtk), dan setiap Scene line di bawah membawa jendela detik lokalnya: itu cue-nya. Ease default long-tail `power3` (smooth, tanpa bounce/overshoot); entrance selalu `fromTo`; semua gerak deterministik (tanpa random, tanpa loop abadi). Tidak boleh front-load: t=0 hanya membawa apa yang beat pertama butuhkan; muatan besar dipindah ke paruh belakang. Holds: cukup diam + paling banyak jitter mikro.
- **Rhythm / held allocation:** F1 medium (3 bar reveal) · F2 staccato swap lalu slam ditahan · F3 pan tenang (3 stasiun) · F4 metronomik 1 bar/baris lalu ekor ditahan · F5 build formula/pohon → count-up → ditahan · F6 perjalanan kontinu dengan berhenti sejenak di tiap stasiun · F7 lockup ditahan panjang (≈1,3 dtk terakhir nyaris diam + fade penutup).
- **Negative list:** no slideshow (front-load lalu beku); no screensaver (banyak elemen mengambang independen); no lazy breathing, no back-half pan/push; no bouncy/overshoot; tanpa CRT/glitch/confetti/emoji; tanpa browser chrome atau kursor palsu; tanpa warna teks kedua; tanpa gradien ungu-biru "AI"; tanpa border >1px. Transisi antar-frame di-inject harness — frame tidak menulis exit sendiri; HANYA F7 (frame terakhir) boleh fade-out penutup.
- **Chrome kaki:** footer-band + pagenum duduk di kaki sesuai `frame.md`; SEMUA konten penting (judul, stat, jendela, mark) tetap di atas y≈900 (top 83%) — band bawah dibiarkan lapang.
- **Catalog:** pencarian registry (kinetic type / count-up / logo lockup) tidak menemukan blok yang cocok dengan perlakuan editorial ini — bentuk instansiasi diambil dari blueprint lokal.

## Frame 1 — Cover: Kenali Minat

- scene: Cover tipografis — mark logo + eyebrow "DARI RUANG KELAS, MENUJU MASA DEPAN", lalu display dua baris "Kenali Minatmu." / italic gold "Temukan Arah Kuliahmu.", footer-band sekolah.
- duration: 6.165s
- transition_in: cut
- status: outline
- src: compositions/frames-portrait/01-cover.html
- type: hook
- persuasion: Future pacing — kalimat pembuka memakai bahasa hasil milik penonton (arah kuliah), bukan fitur.
- beat: curiosity + aspiration
- blueprint: kinetic-type-beats (Adapt)
- focal: assets/logo.svg — mark SIMINAT (hero brand beat)
- roles: logo.svg = cutout (mark di sayap kiri-atas komposisi; hero tipografi tetap baris display)
- sfx: none
- asset_candidates: assets/logo.svg — mark SIMINAT (buku terbuka + pin + bar chart, garis emas)

narrativeRole: Membuka dengan bahasa yang penonton (siswa) pedulikan: mengenal minat → merencanakan masa depan.
keyMessage: SIMINAT adalah langkah awal dari ruang kelas menuju masa depan.

Adapt: keep — beat tipografis per bar dengan finale yang ditahan; change — panggung cover editorial (display rata-kiri, bloom kiri-tengah, footer-band), bukan field centered polos.

Scene 1 (0.0–2.0s): ground navy + satu sun-bloom kiri-tengah + ember counter-bloom sudut kanan (subordinat). Mark masuk merekah halus di sayap kiri-atas, lalu eyebrow micro-label "DARI RUANG KELAS, MENUJU MASA DEPAN" muncul di bawahnya — satu rangkaian, long-tail settle. Tidak ada elemen lain di kanvas (sparse; bloom menahan ruang kanan).
Scene 2 (2.0–4.08s): display satu baris "Kenali Minatmu." masuk menukik dengan entri khas per-beat (kinetic-beat-slam), mendarat di sepertiga-kiri; pagenum muncul kanan-bawah. Baris ditahan terbaca sampai beat berikutnya.
Scene 3 (4.08–6.165s): baris kedua "Temukan Arah Kuliahmu." italic gold masuk di bawah display saat downbeat; footer-band 4 sel menyusun baris-demi-baris di kaki (hairline ink 1px di atas tiap sel): "MAN 4 Matangkuli" · "MAS Al-Muslimun Lhoksukon" · "Nilai rapor · Peminatan" · "Klasifikasi ID3". Semua diam dan terbaca; hold sampai cut.

## Frame 2 — Masalah: Banyak pilihan

- scene: Kinetic pain — "Saintek? Soshum? Campuran?" berganti cepat di satu slot, hard-cut ke "Bingung memilih jurusan?", lalu baris kecil "Banyak pilihan. Sedikit arahan."
- duration: 4.17s
- transition_in: crossfade
- status: outline
- src: compositions/frames-portrait/02-masalah.html
- type: pain_point
- persuasion: Pain agitation — menamai kebimbangan jurusan yang dirasakan siswa kelas XI–XII.
- beat: frustration + anxiety
- blueprint: kinetic-type-beats (Adapt)
- focal: none — deliberately typography-only (kanvas polos, belum ada produk)
- roles: none — tipografi adalah visualnya
- sfx: impact-bass-1
- asset_candidates: none — deliberately typography-only (kanvas polos, belum ada produk)

narrativeRole: Menamai masalah sebelum menawarkan apa pun — penonton merasa "itu aku".
keyMessage: Pilihannya banyak, arahnya belum jelas.

Adapt: keep — satu slot tengah + swap instan; change — tempo swap mengikuti sub-beat ≈0,52 dtk dan finale ditahan sampai frame habis.

Scene 1 (0.0–2.09s): satu sun-bloom redup kanan-atas + ember counter-bloom kiri-bawah; satu kalimat memegang slot di tengah-atas dan HANYA kata-nya yang berganti: "Saintek?" → "Soshum?" → "Campuran?" — hard-cut instan tiap ≈0,52 dtk, tanpa fade/geser (in-place token cycle, → discrete-text-sequence). Ukuran kalimat identik antar state.
Scene 2 (2.09–3.13s): hard-cut ke "Bingung memilih jurusan?" — baris besar masuk slam skala dari besar dengan long-tail, duduk di tengah-atas ±40% kanvas; ember counter-bloom menyala tipis di belakang saat mendarat; frasa ini ditahan.
Scene 3 (3.13–4.17s): baris kecil "Banyak pilihan. Sedikit arahan." reveal di bawah baris besar (mask-reveal kiri→kanan, lembut); seluruhnya diam dan terbaca sampai cut.

## Frame 3 — Solusi: Dari rapor ke rekomendasi

- scene: Spatial pan stations — "Nilai rapor · Peminatan" → "Dibaca dengan metode ID3" → "Rekomendasi rumpun jurusanmu", pan berakhir saat jendela situs (hero asli) mendarat.
- duration: 6.26s
- transition_in: zoom-through
- status: outline
- src: compositions/frames-portrait/03-solusi.html
- type: product_intro
- persuasion: Show-don't-tell proof — konsep didekode stasiun demi stasiun lalu mendarat di permukaan situs yang benar-benar ada.
- beat: clarity + relief
- blueprint: spatial-pan-stations (Adapt)
- focal: assets/01-hero.png — hero situs live (jendela bukti yang mendarat di stasiun 3)
- roles: 01-hero.png = supporting → hero di stasiun 3 (jendela rect 0-radius + hairline, tanpa browser chrome); kelas-madrasah-ai.png = cadangan (hanya bila staging ulang ilustrasi hero di jendela diperlukan)
- sfx: whoosh
- asset_candidates: assets/01-hero.png — hero situs live (1920×1080): headline, CTA emas, ilustrasi kelas madrasah; assets/kelas-madrasah-ai.png — ilustrasi AI kelas madrasah (bila hero perlu staging ulang)

narrativeRole: Menjawab masalah dengan satu alur sederhana yang bisa ditelusuri — titik balik video.
keyMessage: Nilai rapormu menjadi rekomendasi — asal dan alurnya jelas.

Adapt: keep — satu kanvas dunia lebih lebar + pan lateral yang menyambung antar stasiun; change — tiga stasiun editorial "MASUKAN → PROSES → HASIL" dengan garis penghubung yang menggambar antar-stasiun; tanpa callout box.

Scene 1 (0.0–2.09s): kamera dunia (viewport-change) membuka di stasiun 1 kiri: micro-label "MASUKAN" + dua baris ter-label "Nilai rapor" / "Peminatan" (hairline ivory 1px antar baris). Pan mulai meluncur ke kanan saat downbeat 2 (multi-phase-camera).
Scene 2 (2.09–4.18s): kamera mendarat di stasiun 2 (coordinate-target-zoom men-center target): "Dibaca dengan metode ID3"; garis penghubung menggambar dirinya kiri→kanan antar stasiun (svg-path-draw). Pan berlanjut tenang — stasiun 1 keluar frame.
Scene 3 (4.18–6.26s): kamera mendarat di stasiun 3: "Rekomendasi rumpun jurusanmu"; jendela situs assets/01-hero.png meluncur masuk dari kanan (tetap di dalam area konten, margin aman) dan BERHENTI di posisi hero saat downbeat; kamera berhenti; semua diam; hold terbaca sampai cut.

## Frame 4 — Cara kerja: Empat langkah

- scene: Ledger rows — 01 Data siswa · 02 Kategori nilai · 03 Pohon ID3 · 04 Diskusi hasil; satu baris menyala per bar musik.
- duration: 8.345s
- transition_in: crossfade
- status: outline
- src: compositions/frames-portrait/04-cara-kerja.html
- type: feature_showcase
- persuasion: Friction reduction — prosesnya transparan dan tenang, bukan kotak hitam.
- beat: ease + control
- blueprint: grid-card-assemble (Adapt)
- focal: none — deliberately typography-only (baris ledger adalah visualnya)
- roles: none — ledger 4 baris adalah visualnya
- sfx: none
- asset_candidates: none — deliberately typography-only (baris ledger adalah visualnya)

narrativeRole: Membuktikan prosesnya masuk akal dan rapi — kepercayaan dibangun dari keteraturan.
keyMessage: Empat langkah sederhana dari data siswa ke diskusi hasil.

Adapt: keep — daftar vertikal yang menyusun dirinya baris demi baris (varians "vertical benefit list" accumulate); change — gaya ledger tabular `frame.md` (numeral Georgia + judul Georgia + body Inter + hairline-soft), satu baris per bar musik; ekor ditahan.

Scene 1 (0.0–2.09s): topbar ledger — micro-label "CARA KERJA" + headline-sm "Dari data siswa ke diskusi hasil." (hairline ink 1px di bawah topbar). Baris 01 "Data siswa" + body "Nilai rapor dan peminatan sebagai masukan." masuk langsung-ke-slot (jarak pendek; numeral Georgia 01 + judul + body, → center-outward-expansion); aksen gold hanya pada numeral baris yang baru masuk.
Scene 2 (2.09–4.18s): baris 02 "Kategori nilai" — "Nilai diubah menjadi kategori (diskretisasi)." menyala saat downbeat (→ spring-pop-entrance pada numeral); baris 01 meredup halus by-position (→ gsap-effects). Hairline-soft 1px memisah baris.
Scene 3 (4.18–6.27s): baris 03 "Pohon ID3" — "Telusuri cabang keputusan per atribut." menyala; baris 02 meredup.
Scene 4 (6.27–8.345s): baris 04 "Diskusi hasil" — "Baca jalur keputusan bersama guru BK." menyala (baris 04 = baris terang focal); seluruh ledger terpasang dan DITAHAN terbaca — ekor tenang, tanpa gerak tambahan (hanya jitter mikro bila perlu).

## Frame 5 — Metode: Keputusan yang bisa ditelusuri

- scene: Formula Entropy(S) dan Gain(S,A) (Georgia italic, seperti situs), pohon keputusan kecil menggambar (node Peminatan → cabang → daun Soshum/Saintek/Campuran) + count-up "88,9%" dan "9 atribut".
- duration: 6.27s
- transition_in: crossfade
- status: outline
- src: compositions/frames-portrait/05-metode.html
- type: social_proof
- persuasion: Statistical proof — angka akurasi terverifikasi projek + metode yang terbuka untuk diperiksa.
- beat: trust + awe
- blueprint: dataviz-countup (Adapt)
- focal: assets/04-metode.png — section Metode ID3 situs live (jendela konteks kecil)
- roles: 04-metode.png = supporting (jendela konteks kecil di sudut kanan-bawah area konten; boleh dikecilkan/diredupkan agar tidak mengganggu)
- sfx: none
- asset_candidates: assets/04-metode.png — section Metode ID3 situs live (formula + ilustrasi pohon) — sumber tampilan, boleh muncul sebagai jendela konteks kecil

narrativeRole: Membangun kepercayaan lewat angka nyata dan keterbukaan metode — "keputusan yang bisa ditelusuri".
keyMessage: Metodenya terbuka dan terukur — 88,9% akurasi 5-fold pada 135 data skenario Saintek–Soshum.

Adapt: keep — satu statistik naik sambil struktur menggambar; change — instrumennya tipografis/editorial (formula Georgia italic + pohon hairline), bukan ring/chart; satu angka, bukan tiga.

Scene 1 (0.0–2.09s): headline-sm "Keputusan yang bisa ditelusuri." + baris formula "Entropy(S) = − Σ p(c) log₂ p(c)" (Georgia italic); micro-label "METODE". Jendela konteks kecil assets/04-metode.png mendarat lembut di sudut kanan-bawah area konten (hairline, 0-radius).
Scene 2 (2.09–4.18s): formula "Gain(S, A) = Entropy(S) − Σ (|Sᵥ| / |S|) Entropy(Sᵥ)" reveal di bawahnya; pohon keputusan menggambar dirinya (svg-path-draw): akar "Peminatan" (catatan mikro "gain 0,4827") → tiga cabang → daun "Saintek" / "Soshum" / "Campuran" — label ink, tiap daun ditandai marker kecil berwarna kelas (biru #1F5F9E / cokelat #8A4B08 / ungu #6B3C8F, marker ≤12px — warna HANYA pada marker, teks tetap ink); node pop-in di ujung draw (spring-pop-entrance).
Scene 3 (4.18–6.27s): count-up numeral-jumbo "88,9%" naik 0→88,9 dengan skala tumbuh bersama nilai (counting-dynamic-scale) di tengah blok; micro-label "AKURASI 5-FOLD · 135 DATA" dan "9 ATRIBUT" reveal setelahnya; catatan mikro "Ilustrasi struktur, bukan hasil data siswa" di baris kaki area konten. Semua diam dan terbaca sampai cut (hold).

## Frame 6 — Tur situs: Jelajahi SIMINAT

- scene: Kamera terbang menuruni plat penuh situs (1920×5908) — berhenti singkat di Rumpun Jurusan → Metode ID3 → Panduan; label mikro muncul per stasiun di rel kiri.
- duration: 6.26s
- transition_in: zoom-through
- status: outline
- src: compositions/frames-portrait/06-tur.html
- type: benefit_highlight
- persuasion: Future pacing — penonton melihat dirinya sendiri menjelajahi situs; buktinya ada di depan mata.
- beat: confidence + belonging
- blueprint: camera-journey (Adapt)
- focal: assets/full-page.png — plat penuh situs 1920×5908 (dunia perjalanan)
- roles: full-page.png = background/dunia (penerbangan tanpa kursor); 02-rumpun.png, 04-metode.png, 05-panduan.png = supporting (shot section tajam saat kamera berhenti di stasiun — swap-in elemen di dalam jendela, bukan mengganti dunia)
- sfx: whoosh-short
- asset_candidates: assets/full-page.png — plat penuh situs 1920×5908 (semua section, tangkapan asli); assets/02-rumpun.png — section Rumpun Jurusan; assets/04-metode.png — section Metode ID3; assets/05-panduan.png — section Panduan

narrativeRole: Membuktikan janji — situsnya nyata, lengkap, dan ditata untuk ditelusuri siswa.
keyMessage: Rumpun jurusan, cara kerja, metode, dan panduan — semua ada di satu tempat.

Adapt: keep — sub-shape B (penerbangan tanpa kursor) + landing yang ditahan; change — "dunia" adalah plat 5908px yang nyata; setiap kaki dimotivasi oleh stasiun yang dijawabnya; jendela viewport = rect 0-radius + hairline ivory, tanpa browser chrome.

Scene 1 (0.0–2.09s): jendela viewport (0-radius, hairline ivory) membuka di puncak plat (section hero); kamera dunia (viewport-change) mulai meluncur TURUN dengan easing lembut; dunia = wrapper ber-data-layout-allow-overflow, overflow clip di root. Tidak ada kursor di mana pun.
Scene 2 (2.09–4.18s): kamera mendarat sesaat di stasiun "RUMPUN JURUSAN" (coordinate-target-zoom menahan target di tengah viewport) — label mikro rel-kiri muncul; saat berhenti, elemen 02-rumpun.png memakai shot section yang tajam; lalu meluncur turun lagi (multi-phase-camera).
Scene 3 (4.18–6.26s): dua berhenti singkat berurutan — "METODE ID3" (±4.18–5.29, memakai 04-metode.png) lalu "PANDUAN" (±5.29–5.81, memakai 05-panduan.png) — label mikro per stasiun; setelah PANDUAN, kamera pull-back lembut memperlihatkan seluruh plat (skala dunia mengecil; teks stasiun tetap terbaca); berhenti dan tahan sampai cut.

## Frame 7 — CTA: Mulai dari percakapan

- scene: Display "Pilihan yang matang" / italic gold "dimulai dari percakapan." → baris CTA + catatan "Rekomendasi adalah bahan pertimbangan, bukan keputusan akhir." → lockup mark + SIMINAT + tagline, footer "Dua madrasah, satu tujuan."
- duration: 7.53s
- transition_in: blur-crossfade
- status: outline
- src: compositions/frames-portrait/07-cta.html
- type: cta
- persuasion: Risk reversal — rekomendasi bukan keputusan akhir; langkah amannya adalah berdiskusi dengan guru BK.
- beat: motivation + peace of mind
- blueprint: logo-assemble-lockup (Adapt)
- focal: assets/logo.svg — mark SIMINAT untuk lockup penutup
- roles: logo.svg = cutout (mark di tengah lockup); rak jendela memakai tangkapan situs yang sudah di-stage projek (01-hero.png, 02-rumpun.png, 04-metode.png, 05-panduan.png) sebagai elemen panggung yang menyebar
- sfx: impact-bass-1
- asset_candidates: assets/logo.svg — mark SIMINAT untuk lockup penutup

narrativeRole: Menutup dengan tindakan manusiawi (diskusi bersama guru BK) + identitas brand yang tenang.
keyMessage: Pilihan yang matang dimulai dari percakapan bersama guru BK.

Adapt: keep — teks membersihkan panggung lalu mark menggambar dirinya dan lockup lengkap (text-clear bloom + stroke draw); change — "cara bersihnya" adalah rak empat jendela situs kecil yang menyebar keluar ke empat sisi; wordmark "SIMINAT" + tagline + footer-band mengikuti lockup.

Scene 1 (0.0–2.09s): display dua baris rata-tengah: "Pilihan yang matang" (ink) + "dimulai dari percakapan." (italic gold); reveal per frasa word-by-word long-tail saat downbeat; bloom emas di belakang blok teks.
Scene 2 (2.09–4.18s): lede "Mulai dari nilai rapor. Diskusikan bersama guru BK." + catatan mikro "Rekomendasi adalah bahan pertimbangan, bukan keputusan akhir." reveal di bawah blok; sebuah rak empat jendela situs kecil (echo tur F6: 01-hero/02-rumpun/04-metode/05-panduan — hairline, 0-radius, diredupkan) menyusun dirinya di kaki area konten saat downbeat 2.
Scene 3 (4.18–6.27s): rak jendela menyebar KELUAR ke empat sisi panggung (center-outward-expansion arah-outward) membersihkan layar; saat panggung bersih, mark assets/logo.svg menggambar dirinya (svg-path-draw, stagger multi-segmen), wordmark "SIMINAT" reveal di kanannya, tagline "KENALI MINAT. RENCANAKAN MASA DEPAN." (micro-label) melengkapi lockup; footer-band "Dua madrasah, satu tujuan." + nama dua madrasah menyusun di kaki.
Scene 4 (6.27–7.53s): lockup + footer DITAHAN nyaris diam (paling banyak jitter mikro) — pembacaan tenang; frame terakhir boleh fade-out halus di ±0,4 dtk terakhir ke ground navy.
