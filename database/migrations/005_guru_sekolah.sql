-- Akun guru BK lama tetap ada, tetapi harus diberi sekolah sebelum dapat masuk.
ALTER TABLE users ADD COLUMN sekolah_id TINYINT UNSIGNED NULL AFTER role;
ALTER TABLE users ADD CONSTRAINT fk_users_sekolah FOREIGN KEY (sekolah_id) REFERENCES sekolah(id);
