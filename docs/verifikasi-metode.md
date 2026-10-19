# Verifikasi Hasil terhadap Metode Penelitian

Tanggal: 17 Oktober 2026. Acuan metode: proposal `data/referensi/New Nadia.pdf` (BAB I 1.3, BAB II 2.5, BAB III 3.4). Data: `data/Dataset_Bersih_ID3.xlsx`.

## 1. Kesimpulan singkat

1. **Perhitungan sistem sesuai metode ID3 di proposal.** Entropy, gain, pemilihan atribut, kriteria berhenti, dan prediksi dihitung ulang secara independen (Python, langsung dari Excel, tanpa kode PHP dan database). Hasilnya sama persis untuk keempat skenario: struktur pohon, entropy setiap node, gain setiap atribut di setiap langkah, akurasi latih, dan confusion matrix k-fold (toleransi 0,000001).
2. **Satu aturan sistem diperbaiki agar sesuai proposal.** Sebelumnya sistem berhenti memecah node bila gain terbaik bernilai 0. Aturan itu tidak tercantum di proposal sehingga dihapus. Sekarang node hanya berhenti bila (a) kelas sama, (b) atribut habis, atau (c) data habis.
3. **Data belum sepenuhnya sesuai Batasan Masalah proposal** (bagian 4). Bagian ini perlu direvisi di proposal atau datanya dilengkapi.
4. **Hasil terbaik dan paling sesuai proposal: skenario Saintek dan Soshum** (tanpa Campuran). Akurasi 5-fold 88,9%, F1 macro 84,1%, di atas baseline kelas mayoritas 74,8%. Model ini dijadikan model aktif.

## 2. Kesesuaian implementasi dengan metode

| Metode di proposal | Implementasi | Status | Bukti |
|---|---|---|---|
| Entropy(S) = −Σ pᵢ log₂ pᵢ (pers. 1) | `Id3::entropy`, log basis 2, 0·log₂0 = 0 | Sesuai | Uji play-tennis: entropy 0,9403 |
| Gain(S,A) = Entropy(S) − Σ \|Sv\|/\|S\| Entropy(Sv) (pers. 2 sampai 4) | `Id3::gain` | Sesuai | Gain Outlook 0,2467, Humidity 0,1518, Wind 0,0481, Temperature 0,0292 |
| Atribut dengan gain tertinggi menjadi node | Gain tertinggi dipilih; bila seri, urutan atribut terkecil | Sesuai | Akar play-tennis = Outlook |
| Berhenti bila kelas sama, atribut habis, data habis | Tiga kriteria itu saja (aturan gain 0 dihapus) | Sesuai setelah diperbaiki | Uji XOR dan atribut identik di `tests/id3_manual_test.php` |
| Daun memprediksi argmax P(c\|L) (pers. 7) | Kelas mayoritas di daun; cabang tanpa data memakai mayoritas induk | Sesuai | Verifikasi independen |
| ID3 hanya untuk atribut kategorikal, nilai numerik perlu diskretisasi | Nilai rapor dikategorikan: Tinggi ≥ 95, Sedang 92 sampai < 95, Rendah < 92 | Sesuai | 247 siswa: kategori sistem sama dengan sheet Data_ID3 (0 selisih) |
| Nilai hilang (pendekatan treat-as-value) | Mapel yang tidak diambil = kategori "Tidak Ada" | Sesuai pendekatan (a) proposal | Kamus_Data dataset |
| Hasil klasifikasi ditampilkan beserta jalur keputusan (BAB III 3.4 no. 7) | Halaman hasil Cek Rekomendasi menampilkan jalur dan keyakinan | Sesuai | Panel Prediksi |
| Pruning (BAB II no. 7) | Tersedia sebagai opsi (minimal sampel, kedalaman). Model penelitian memakai ID3 murni tanpa pruning | Sesuai (opsional) | Konfigurasi model |

Catatan: minimal sampel 2 tidak mengubah pohon, karena node berisi 1 data pasti satu kelas dan sudah berhenti oleh kriteria (a).

## 3. Hasil pengolahan data

Model dilatih dengan ID3 murni memakai 9 atribut: Sekolah_Asal, Peminatan, Kat_Agama, Kat_BArab, Kat_Bahasa, Kat_MTK, Kat_MIPA, Kat_IPS, dan Kecenderungan. Pengujian memakai stratified 5-fold cross validation dengan seed 42. Baseline adalah akurasi bila semua siswa ditebak kelas terbanyak.

| Skenario | Data | Akar | Akurasi latih | Akurasi 5-fold | Baseline | Precision | Recall | F1 macro |
|---|---|---|---|---|---|---|---|---|
| Semua data | 147 | Kat_MIPA | 90,5% | 82,3% | 68,7% | 65,5% | 64,5% | 64,8% |
| MAN saja | 43 | Kecenderungan | 83,7% | 55,8% | 62,8% | 35,4% | 37,3% | 36,3% |
| MAS saja | 104 | Peminatan | 93,3% | 93,3% | 93,3% | 46,6% | 50,0% | 48,3% |
| **Saintek dan Soshum** | 135 | Peminatan | 93,3% | **88,9%** | 74,8% | 87,5% | 81,8% | **84,1%** |

Confusion matrix skenario Saintek dan Soshum (baris = label asli, kolom = prediksi):

| | Saintek | Soshum |
|---|---|---|
| Saintek (34) | 23 | 11 |
| Soshum (101) | 4 | 97 |

Gain di akar skenario Saintek dan Soshum (entropy akar 0,8142): Peminatan 0,4827; Kat_MIPA 0,4814; Sekolah_Asal 0,4127; Kecenderungan 0,4053; Kat_Agama 0,3896; Kat_IPS 0,3467; Kat_BArab 0,3408; Kat_Bahasa 0,2747; Kat_MTK 0,2160.

Prediksi massal 100 siswa tanpa label dengan model aktif: Saintek 5 (semua dari MAN) dan Soshum 95 (MAN 3, MAS 92).

## 4. Ketidaksesuaian data dengan proposal

| Bagian proposal | Isi proposal | Kondisi data | Saran |
|---|---|---|---|
| Batasan no. 2 | Dua kelas: sains dan teknologi, sosial dan humaniora | Ada kelas ketiga, Campuran (12 siswa MAN: 1 mapel TKA Saintek + 1 Soshum) | Pakai skenario Saintek dan Soshum sebagai hasil utama, atau tambahkan Campuran ke Batasan Masalah |
| Batasan no. 3, Tujuan no. 2 | Nilai rapor, prestasi akademik dan non-akademik, akreditasi, indeks sekolah, minat, tes minat bakat, ekstrakurikuler, kuesioner | Hanya nilai rapor, peminatan, dan sekolah asal | Revisi Batasan dan Tujuan, atau kumpulkan data prestasi dan kuesioner |
| Batasan no. 5 dan 6 | SNMPTN/SBMPTN, 30 siswa per sekolah | Label dari mapel pilihan TKA 2025 (MAN) dan prodi SPAN-PTKIN 2026 (MAS); 43 dan 104 siswa berlabel | Sesuaikan nama seleksi dan jumlah sampel |
| Tujuan no. 1 | Hanya MAN 4 Matangkuli | Data dua madrasah | Samakan dengan judul (dua madrasah) |
| BAB III 3.2 no. 6 | "klasifikasi penentuan status gizi balita" | Salah ketik dari naskah lain | Ganti dengan "klasifikasi minat jurusan kuliah" |
| BAB III | Teknik pengujian akurasi belum disebut | Sistem memakai stratified k-fold (k = 5, seed 42), confusion matrix, precision, recall, F1 | Tuliskan teknik ini di BAB III |

## 5. Catatan untuk pembahasan BAB IV

1. **Akurasi perlu dibandingkan dengan baseline.** Data MAS sangat tidak seimbang (97 Soshum, 7 Saintek). Model MAS saja memprediksi semua siswa Soshum sehingga akurasi 93,3% sama dengan baseline dan recall Saintek 0%. Model MAN saja (55,8%) di bawah baseline (62,8%) karena datanya sedikit (43 siswa) dan kelasnya tiga.
2. **Nilai MAS seragam (97 sampai 99)** sehingga semua kategori nilai MAS menjadi Tinggi. Pembeda utama di MAS adalah Peminatan. Ini terlihat dari akar pohon skenario MAS dan skenario Saintek dan Soshum.
3. **Pohon ID3 murni besar** (92 node untuk skenario Saintek dan Soshum). Sebagian besar node itu adalah cabang tanpa data, dan ada pemecahan bergain 0 yang tetap dilakukan sesuai kriteria berhenti proposal. Aturan IF-THEN hanya dibentuk dari daun yang memiliki data (16 aturan).
4. **Indikasi overfitting.** Pada skenario Saintek dan Soshum, pohon dengan kedalaman maksimal 1 mendapat akurasi 5-fold 91,9% (F1 88,9%), lebih baik daripada pohon penuh. Angka ini dipilih dengan data uji yang sama sehingga cenderung optimis. Bila ingin memakai pre-pruning (BAB II no. 7a), tetapkan aturannya di BAB III sebelum pengujian.

## 6. Cara mengulang

```powershell
php scripts/import_dataset.php                      # muat ulang 247 siswa dari Excel
php scripts/proses_penelitian.php tanpa_campuran 5  # latih 4 skenario, uji 5-fold, aktifkan, prediksi massal
php tests/id3_manual_test.php                       # uji rumus ID3 (play-tennis)
python tests/verify_id3_dataset.py                  # verifikasi independen sistem terhadap Excel
```

Hasil verifikasi rinci tersimpan di `output/verifikasi_id3.json`. Model, langkah perhitungan entropy/gain per node, aturan IF-THEN, dan confusion matrix dapat dilihat dan dicetak dari panel admin (menu Training, Pengujian, Laporan).
