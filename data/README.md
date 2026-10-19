# Data penelitian

`Dataset_Bersih_ID3.xlsx` adalah workbook siap impor yang dibaca aplikasi, CLI impor, dan tes verifikasi ID3. Lokasinya tetap di folder ini.

| Lokasi | Isi |
|---|---|
| `sumber/man/legger/` | Tiga PDF nilai kelas XII MAN 4 Aceh Utara. |
| `sumber/man/formulir-tka/` | 45 foto formulir pilihan mata pelajaran TKA. |
| `sumber/man/` | Workbook siswa yang lolos PTKIN/SPAN-PTKIN; pembacaan skrip memakai sheet `2026`. |
| `sumber/mas/legger/` | Delapan workbook nilai kelas XII MAS Al-Muslimun. |
| `sumber/mas/poster-kelulusan/` | Delapan foto poster kelulusan perguruan tinggi. |
| `referensi/` | Proposal penelitian `New Nadia.pdf`. |

Nama file sumber dipertahankan agar kolom `Bukti_Label` dan `File_Nilai` di dataset tetap bisa ditelusuri. Kedua kolom menyimpan nama file, bukan jalur absolut. Catatan di workbook lama dapat menyebut lokasi skrip sebelum pemindahan; gunakan perintah terbaru di bawah.

## Membangun ulang

Dari root proyek, dengan `openpyxl` dan Poppler `pdftotext` tersedia:

```powershell
python scripts/penelitian/build_dataset.py
```

Perintah ini mengganti workbook siap impor. Skrip `extract.py` membaca legger dan daftar PTKIN, sedangkan `raw_labels.py` menyimpan hasil pembacaan manual foto. `match_check.py` membantu audit pencocokan nama. Keempat skrip berada di `scripts/penelitian/`.

Hasil penelitian berisi 247 siswa: 147 berlabel dan 100 tanpa label. Memindahkan sumber tidak memerlukan impor ulang atau perubahan database.

Dokumen sumber memuat identitas siswa. Akses HTTP ke seluruh `data/` diblokir oleh `.htaccess` root; folder ini dibaca melalui filesystem oleh proses yang berwenang.
