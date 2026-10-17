-- Akun awal pengembangan: admin / admin123. Wajib ganti kata sandi saat masuk pertama.
-- Tidak menimpa akun yang sudah ada.
INSERT INTO users (nama, username, password_hash, role, must_change_password)
SELECT 'Administrator SIMINAT', 'admin', '$2y$12$jp51.VxPCE78fy1uK9CU7OKKTywqx0uGstjSyR.qPWOPg.7ms9JxW', 'admin', 1
WHERE NOT EXISTS (SELECT 1 FROM users WHERE username = 'admin');
