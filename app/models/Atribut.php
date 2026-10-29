<?php
declare(strict_types=1);

final class Atribut
{
    public static function all(): array
    {
        return Database::run(
            'SELECT a.*, GROUP_CONCAT(n.nilai ORDER BY n.urutan SEPARATOR ", ") AS domain
             FROM atribut a LEFT JOIN atribut_nilai n ON n.atribut_id = a.id GROUP BY a.id ORDER BY a.urutan, a.id'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        return Database::run('SELECT * FROM atribut WHERE id = ?', [$id])->fetch() ?: null;
    }

    /** @return array<string, list<string>> kode => nilai domain terurut */
    public static function domains(): array
    {
        $domains = [];
        foreach (Database::run('SELECT a.kode, n.nilai FROM atribut a JOIN atribut_nilai n ON n.atribut_id = a.id ORDER BY a.urutan, n.urutan')->fetchAll() as $row) {
            $domains[$row['kode']][] = $row['nilai'];
        }
        return $domains;
    }

    /** @return array<string, int> kode => id */
    public static function ids(): array
    {
        return array_column(Database::run('SELECT id, kode FROM atribut')->fetchAll(), 'id', 'kode');
    }

    /** @return list<string> kode atribut aktif terurut */
    public static function activeCodes(): array
    {
        return array_column(Database::run('SELECT kode FROM atribut WHERE aktif = 1 ORDER BY urutan, id')->fetchAll(), 'kode');
    }

    public static function update(int $id, string $nama, string $deskripsi, bool $aktif, int $urutan): void
    {
        Database::run('UPDATE atribut SET nama = ?, deskripsi = ?, aktif = ?, urutan = ? WHERE id = ?', [$nama, $deskripsi !== '' ? $deskripsi : null, (int) $aktif, $urutan, $id]);
    }

    /** Jumlah siswa berlabel per nilai atribut dan kelas. */
    public static function distribution(int $id, ?int $schoolId = null): array
    {
        return Database::run(
            "SELECT n.nilai, COUNT(s.id) AS total, SUM(s.label = 'Saintek') AS saintek, SUM(s.label = 'Soshum') AS soshum,
                    SUM(s.label = 'Campuran') AS campuran, SUM(s.id IS NOT NULL AND s.label IS NULL) AS tanpa_label
             FROM atribut_nilai n
             LEFT JOIN siswa_atribut sa ON sa.atribut_id = n.atribut_id AND sa.nilai = n.nilai
             LEFT JOIN siswa s ON s.id = sa.siswa_id" . ($schoolId ? ' AND s.sekolah_id = ?' : '') . "
             WHERE n.atribut_id = ? GROUP BY n.id ORDER BY n.urutan",
            $schoolId ? [$schoolId, $id] : [$id]
        )->fetchAll();
    }
}
