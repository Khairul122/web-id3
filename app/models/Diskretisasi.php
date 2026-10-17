<?php
declare(strict_types=1);

final class Diskretisasi
{
    public static function current(): array
    {
        return Database::run('SELECT * FROM aturan_diskretisasi WHERE id = 1')->fetch()
            ?: ['batas_tinggi' => '95.00', 'batas_sedang' => '92.00', 'updated_at' => null];
    }

    public static function save(float $tinggi, float $sedang): void
    {
        Database::run(
            'INSERT INTO aturan_diskretisasi (id, batas_tinggi, batas_sedang) VALUES (1, ?, ?)
             ON DUPLICATE KEY UPDATE batas_tinggi = VALUES(batas_tinggi), batas_sedang = VALUES(batas_sedang)',
            [$tinggi, $sedang]
        );
    }
}
