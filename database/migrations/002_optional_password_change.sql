-- Perubahan kebijakan atas permintaan pengguna. Tidak mengubah hash kata sandi atau sesi.
ALTER TABLE users ALTER COLUMN must_change_password SET DEFAULT 0;
UPDATE users SET must_change_password = 0 WHERE must_change_password <> 0;
