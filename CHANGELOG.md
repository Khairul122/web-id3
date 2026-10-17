# Changelog

## 2026-10-17

- Sesuai permintaan pengguna, login langsung membuka ruang kerja tanpa kewajiban mengganti kata sandi. Kata sandi saat ini tetap berlaku; menu Ganti kata sandi tersedia secara sukarela.
- Halaman login, navigasi Masuk/Panel Saya, sesi akun admin dan guru BK, penggantian kata sandi awal, dan logout POST.
- Database MySQL `siminat` dengan tabel `users` dan `login_attempts`; seed akun admin tidak menimpa akun yang sudah ada.
- CSRF, prepared statements, bcrypt, batas 5 percobaan login per IP selama 15 menit, dan validasi server.
- Foto khusus login dibuat ulang menggunakan GPT Image: konsultasi pilihan kuliah bersama guru BK; hero landing tetap memakai foto kelas.

- Hero awal SIMINAT berdasarkan referensi pengguna: foto kelas memenuhi layar, navigasi, judul editorial, CTA, dan tiga panel informasi.
- Konsep dan foto latar dibuat menggunakan GPT Image. Foto ditandai sebagai ilustrasi AI.
- Menu responsif dan dialog penjelasan tersedia dengan keyboard, Escape, penguncian fokus native, dan pengembalian fokus.
- Fitur klasifikasi belum diimplementasikan; CTA mengenalkan kategori rumpun jurusan.
