-- Peran siswa. Akun siswa ditautkan lewat kode_siswa (bukan siswa.id) karena impor membuat ulang semua baris siswa.
-- Dijalankan oleh scripts/setup_auth.php hanya bila kolom kode_siswa belum ada.
ALTER TABLE users MODIFY role ENUM('admin', 'guru_bk', 'siswa') NOT NULL DEFAULT 'guru_bk';
ALTER TABLE users ADD COLUMN kode_siswa VARCHAR(20) NULL UNIQUE AFTER role;
