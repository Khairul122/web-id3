-- Data acuan: sekolah, 9 atribut ID3 beserta domainnya, dan aturan diskretisasi default.
-- Aman dijalankan ulang; data yang sudah ada tidak ditimpa.
INSERT IGNORE INTO sekolah (id, kode, nama, alamat) VALUES
(1, 'MAN', 'MAN 4 Aceh Utara', 'Matangkuli, Aceh Utara'),
(2, 'MAS', 'MAS Al-Muslimun Lhoksukon', 'Lhoksukon, Aceh Utara');

INSERT IGNORE INTO atribut (id, kode, nama, deskripsi, aktif, urutan) VALUES
(1, 'Sekolah_Asal', 'Sekolah Asal', 'Kode madrasah asal siswa.', 1, 1),
(2, 'Peminatan', 'Peminatan', 'Kelompok mapel pilihan yang diambil siswa.', 1, 2),
(3, 'Kat_Agama', 'Kategori Agama', 'Rata-rata Quran Hadis, Akidah Akhlak, Fikih, dan SKI.', 1, 3),
(4, 'Kat_BArab', 'Kategori Bahasa Arab', 'Nilai Bahasa Arab.', 1, 4),
(5, 'Kat_Bahasa', 'Kategori Bahasa', 'Rata-rata Bahasa Indonesia dan Bahasa Inggris.', 1, 5),
(6, 'Kat_MTK', 'Kategori Matematika', 'Nilai Matematika umum.', 1, 6),
(7, 'Kat_MIPA', 'Kategori MIPA', 'Rata-rata Biologi, Kimia, Fisika, dan Matematika Tingkat Lanjut.', 1, 7),
(8, 'Kat_IPS', 'Kategori IPS', 'Rata-rata Ekonomi, Sosiologi, dan Geografi.', 1, 8),
(9, 'Kecenderungan', 'Kecenderungan', 'Selisih rata-rata MIPA dan IPS. Selisih 1 poin atau lebih menentukan arah.', 1, 9);

INSERT IGNORE INTO atribut_nilai (atribut_id, nilai, urutan) VALUES
(1, 'MAN', 1), (1, 'MAS', 2),
(2, 'Umum', 1), (2, 'MIPA', 2), (2, 'IPS', 3), (2, 'Keagamaan', 4), (2, 'Lainnya', 5),
(3, 'Tinggi', 1), (3, 'Sedang', 2), (3, 'Rendah', 3), (3, 'Tidak Ada', 4),
(4, 'Tinggi', 1), (4, 'Sedang', 2), (4, 'Rendah', 3), (4, 'Tidak Ada', 4),
(5, 'Tinggi', 1), (5, 'Sedang', 2), (5, 'Rendah', 3), (5, 'Tidak Ada', 4),
(6, 'Tinggi', 1), (6, 'Sedang', 2), (6, 'Rendah', 3), (6, 'Tidak Ada', 4),
(7, 'Tinggi', 1), (7, 'Sedang', 2), (7, 'Rendah', 3), (7, 'Tidak Ada', 4),
(8, 'Tinggi', 1), (8, 'Sedang', 2), (8, 'Rendah', 3), (8, 'Tidak Ada', 4),
(9, 'MIPA', 1), (9, 'IPS', 2), (9, 'Seimbang', 3), (9, 'Tidak Terukur', 4);

INSERT IGNORE INTO aturan_diskretisasi (id, batas_tinggi, batas_sedang) VALUES (1, 95.00, 92.00);
