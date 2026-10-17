<?php
declare(strict_types=1);

final class Sekolah
{
    public static function all(): array
    {
        return Database::run(
            'SELECT s.*, COUNT(sw.id) AS jumlah_siswa, SUM(sw.label IS NOT NULL) AS jumlah_berlabel
             FROM sekolah s LEFT JOIN siswa sw ON sw.sekolah_id = s.id GROUP BY s.id ORDER BY s.id'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        return Database::run('SELECT * FROM sekolah WHERE id = ?', [$id])->fetch() ?: null;
    }

    public static function findByKode(string $kode): ?array
    {
        return Database::run('SELECT * FROM sekolah WHERE kode = ?', [$kode])->fetch() ?: null;
    }

    public static function update(int $id, string $nama, string $alamat): void
    {
        Database::run('UPDATE sekolah SET nama = ?, alamat = ? WHERE id = ?', [$nama, $alamat !== '' ? $alamat : null, $id]);
    }

    /** Jumlah per kelas dan label untuk halaman detail. */
    public static function breakdown(int $id): array
    {
        return Database::run(
            "SELECT kelas, COUNT(*) AS total, SUM(label = 'Saintek') AS saintek, SUM(label = 'Soshum') AS soshum,
                    SUM(label = 'Campuran') AS campuran, SUM(label IS NULL) AS tanpa_label
             FROM siswa WHERE sekolah_id = ? GROUP BY kelas ORDER BY kelas",
            [$id]
        )->fetchAll();
    }
}
