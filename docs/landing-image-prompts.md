# Aset gambar landing SIMINAT

Alat: built-in GPT Image (`image_gen.imagegen`). Foto adalah ilustrasi AI, bukan dokumentasi siswa atau guru sekolah. Label terlihat pada setiap foto dan pada footer. Hasil asli dipertahankan; WebP hanya merupakan konversi format dan ukuran, tanpa pengubahan adegan.

## Foto yang digunakan

| Section | Aset | Dimensi | Ukuran |
|---|---|---|---|
| Rumpun Jurusan | assets/img/landing/jelajah-rumpun-ai.webp | 1536×1024 | 115.782 byte |
| Rumpun Jurusan, layar kecil | assets/img/landing/jelajah-rumpun-ai-768.webp | 768×512 | 48.840 byte |
| Panduan | assets/img/landing/pendampingan-guru-ai.webp | 1536×1024 | 134.168 byte |
| Panduan, layar kecil | assets/img/landing/pendampingan-guru-ai-768.webp | 768×512 | 58.506 byte |

File PNG asli tersedia dengan nama dasar yang sama di assets/img/landing/. Elemen img memakai srcset, sizes, width/height, decoding async, dan loading lazy.

## Konsep section

Setiap konsep berupa satu gambar horizontal terpisah, lalu diimplementasikan menjadi kode. Konsep bukan latar raster yang memuat teks UI.

- docs/visuals/landing/rumpun-concept.png: judul dan foto di kiri, tiga definisi bergaris di kanan.
- docs/visuals/landing/cara-kerja-concept.png: empat tahap bernomor dengan penghubung horizontal.
- docs/visuals/landing/metode-concept.png: penjelasan, rumus, dan diagram pohon pada navy.
- docs/visuals/landing/panduan-concept.png: langkah siswa dan blok pendampingan guru BK.
- docs/visuals/landing/footer-concept.png: tiga kelompok identitas, madrasah, dan kontak melalui guru BK.

Skema pohon ditulis sebagai SVG dalam index.php agar tetap tajam dan memiliki deskripsi aksesibel. Tidak menggunakan gambar model penelitian.

## Prompt final foto rumpun

```text
Create a NEW STANDALONE editorial photograph for the SIMINAT section Rumpun Jurusan, using attached section design as scene and palette reference only. Output the photograph only, not website layout. Horizontal landscape 3:2. Two Indonesian madrasah senior students aged17-18, a girl in modest ivory hijab and teal blazer and a boy in black peci and teal blazer with ivory shirt, browsing an open unlabeled course guide together in a sunlit school library. Natural friendly attentive expressions, standing or seated side by side, look at the guide instead of camera. Waist-up medium composition with faces on the upper half, natural hands holding book, warm wood shelves and late afternoon window light, soft green foliage in distant window, navy and teal schoolwear, warm lifelike skin and authentic Indonesian features. Quiet, candid educational editorial photography, realistic anatomy and documentary texture. NO logos or badges on clothing; NO recognizable official school insignia; NO text, NO letters, NO readable book title, NO UI, NO graphic overlays, NO watermark. Do not crop the website reference; generate a fresh complete photograph.
```

## Prompt final foto panduan

```text
Create a NEW STANDALONE editorial photograph for the SIMINAT section Panduan Guru BK, using attached website section as visual scene reference only. Output photograph only, no website UI. Horizontal landscape 3:2. One Indonesian female guidance teacher aged about35 in modest navy hijab and understated navy batik blouse with warm muted gold details, and a senior female student aged17-18 in ivory hijab and teal school uniform, sitting together at wooden desk by a softly sunlit leafy classroom window. Teacher gently points to an open notebook while student listens and looks down thoughtfully, supportive authentic career guidance mood, no posing or exaggerated smiles. Compose two people comfortably from torso up, faces in upper half, hands and notebook fully in frame, natural believable fingers. Warm wood, deep navy, teal, ivory and soft afternoon gold sunlight; shallow depth of field, realistic Indonesian facial features and skin texture, premium candid documentary education photography. No official school badges, no logos, no readable writing, no typography, no text, no UI panels, no watermark. Do not crop or extract the reference; generate a fresh complete photograph.
```
