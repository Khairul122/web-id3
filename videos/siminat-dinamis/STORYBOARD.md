---
format: 1080x1920
duration: 41.748
fps: 30
music: none
audio: host / assets/bgm.mp3 / 115 BPM
captions: skipped / no voiceover
status: animated
---

# SIMINAT: dari masalah menuju arah studi

Arahan pengguna 17 Oktober 2026: video harus memaparkan masalah, solusi nasional dan solusi sistem. Pilihan pengguna untuk solusi nasional: panduan nasional dan peran guru BK. Durasi diperpanjang dari rencana awal 33,4 detik menjadi 41,748 detik untuk keterbacaan tujuh adegan.

| Adegan | Waktu | Visual dan pesan | Sumber |
|---|---|---|---|
| 01 Masalah | 0–4.174 | Pertanyaan besar “Bingung memilih jurusan?”; nilai sudah ada tetapi arah kuliah belum jelas; pilihan rumpun bercabang. | Masalah yang dijawab situs SIMINAT; tanpa statistik rekaan. |
| 02 Upaya nasional | 4.174–12.522 | Kanvas ivory; tiga langkah: kenali minat, bakat, kemampuan; pilih mata pelajaran sesuai rencana studi; susun rencana bersama guru BK. Sorotan mengikuti garis vertikal. | Panduan Pemilihan Mapel SMA/MA, revisi 2024; sumber tampil pada layar. |
| 03 Solusi SIMINAT | 12.522–16.696 | Wordmark, ilustrasi kelas milik situs, lalu nilai rapor dan peminatan masuk berurutan. | Aset logo dan ilustrasi proyek; deskripsi sistem. |
| 04 Demo masukan | 16.696–22.957 | Formulir asli diperbesar; fokus peminatan, sekolah, nilai; cursor menekan tombol tinjauan dengan kompresi dan dua ring. | assets/form.png, view PHP asli dengan contoh anonim. |
| 05 Proses ID3 | 22.957–29.218 | Lima simpul keputusan terhubung, marker mengikuti Peminatan Umum → Kecenderungan MIPA → Agama Sedang → Bahasa Sedang → Matematika Tinggi. | capture/example.json, hasil read-only model aktif. |
| 06 Hasil | 29.218–35.479 | Kamera berpindah dari hasil Soshum ke jalur keputusan, lalu contoh program studi. | assets/detail.png, tampilan PHP hasil asli. |
| 07 Tindak lanjut | 35.479–41.748 | Kenali minatmu, rencanakan arahmu; diskusikan hasil bersama guru BK; ajakan cek rekomendasi. Garis emas mengarah ke logo. | Tindak lanjut yang dijelaskan pada situs. |

Musik pilihan pengguna dari video sebelumnya dipakai dengan fade-in 0,35 detik dan fade-out 2,148 detik. SFX lama memberi aksen pada pergantian solusi, demo dan hasil. Semua elemen media ditempatkan pada root, tiap adegan di subkomposisi terpisah. Frame akhir tetap terlihat, tanpa fade ke hitam.

## Sumber nasional

- [Repositori resmi, Panduan SMA/MA edisi revisi 2024](https://repositori.kemendikdasmen.go.id/36485/)
- [Dokumen panduan resmi](https://kurikulum.kemendikdasmen.go.id/file/1732515135_manage_file.pdf), bagian Bimbingan dalam Memilih Mata Pelajaran Pilihan.

Tidak ada klaim pengesahan pemerintah terhadap SIMINAT. Panduan nasional memberi konteks pendampingan; SIMINAT menyajikan rekomendasi rumpun dari pola data pelatihan sebagai bahan diskusi. Keyakinan yang terlihat pada screenshot adalah distribusi daun untuk satu contoh, bukan akurasi umum sistem.

## Catatan produksi

Inter lokal dan GSAP lokal dibundel agar hasil tidak bergantung CDN. Screenshot diambil dari view PHP proyek dengan nilai contoh tanpa menyimpan prediksi. Data siswa asli tidak dipakai. Video MP4 mendemonstrasikan interaksi; proyek HyperFrames menyediakan timeline yang dapat dimainkan dan disunting.

Preview: http://localhost:3027/#project/siminat-dinamis

Verifikasi rinci dan status ekspor berada di VALIDATION.md.
