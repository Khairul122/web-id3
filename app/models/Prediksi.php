<?php
declare(strict_types=1);

final class Prediksi
{
    public const PER_PAGE = 20;

    /** @return array{rows: list<array>, total: int} */
    public static function paginate(array $filter, int $page, ?array $owner = null, ?int $schoolId = null): array
    {
        $where = ['1 = 1'];
        $params = [];
        if ($owner !== null) {
            $where[] = $owner[1] === null ? 'p.dibuat_oleh = ?' : '(p.dibuat_oleh = ? OR p.siswa_id = ?)';
            $params[] = $owner[0];
            if ($owner[1] !== null) {
                $params[] = $owner[1];
            }
        }
        if ($schoolId !== null) {
            $where[] = self::schoolWhere();
            $params[] = $schoolId;
            $params[] = Sekolah::find($schoolId)['kode'];
        }
        if (in_array($filter['sumber'] ?? '', ['form', 'massal'], true)) {
            $where[] = 'p.sumber = ?';
            $params[] = $filter['sumber'];
        }
        if (in_array($filter['hasil'] ?? '', Id3::CLASSES, true)) {
            $where[] = 'p.hasil = ?';
            $params[] = $filter['hasil'];
        }
        $where = implode(' AND ', $where);
        $total = (int) Database::run("SELECT COUNT(*) FROM prediksi p LEFT JOIN siswa s ON s.id = p.siswa_id WHERE {$where}", $params)->fetchColumn();
        $offset = (max($page, 1) - 1) * self::PER_PAGE;
        $rows = Database::run(
            "SELECT p.id, p.sumber, p.hasil, p.keyakinan, p.created_at, COALESCE(s.nama, p.nama_input) AS nama, s.kode_siswa, m.nama AS model_nama
             FROM prediksi p JOIN model_id3 m ON m.id = p.model_id LEFT JOIN siswa s ON s.id = p.siswa_id
             WHERE {$where} ORDER BY p.id DESC LIMIT " . self::PER_PAGE . " OFFSET {$offset}",
            $params
        )->fetchAll();
        return ['rows' => $rows, 'total' => $total];
    }

    public static function find(int $id): ?array
    {
        $row = Database::run(
            'SELECT p.*, COALESCE(s.nama, p.nama_input) AS nama, s.kode_siswa, s.label AS label_asli, m.nama AS model_nama, m.aktif AS model_aktif, u.nama AS pembuat
             FROM prediksi p JOIN model_id3 m ON m.id = p.model_id LEFT JOIN siswa s ON s.id = p.siswa_id LEFT JOIN users u ON u.id = p.dibuat_oleh
             WHERE p.id = ?',
            [$id]
        )->fetch();
        if (!$row) {
            return null;
        }
        foreach (['input_json', 'distribusi_json', 'jalur_json'] as $column) {
            $row[$column] = json_decode($row[$column], true);
        }
        return $row;
    }

    public static function ownedBy(array $prediction, int $userId, ?int $siswaId): bool
    {
        return (int) $prediction['dibuat_oleh'] === $userId || ($siswaId !== null && (int) $prediction['siswa_id'] === $siswaId);
    }

    public static function belongsToSchool(array $prediction, int $schoolId): bool
    {
        if ($prediction['siswa_id'] !== null) {
            return (int) Database::run('SELECT sekolah_id FROM siswa WHERE id = ?', [$prediction['siswa_id']])->fetchColumn() === $schoolId;
        }
        return ($prediction['input_json']['atribut']['Sekolah_Asal'] ?? null) === Sekolah::find($schoolId)['kode'];
    }

    private static function schoolWhere(): string
    {
        return "(s.sekolah_id = ? OR (p.siswa_id IS NULL AND JSON_UNQUOTE(JSON_EXTRACT(p.input_json, '$.atribut.Sekolah_Asal')) = ?))";
    }

    public static function hasCreatedBy(int $userId): bool
    {
        return (bool) Database::run('SELECT 1 FROM prediksi WHERE dibuat_oleh = ? LIMIT 1', [$userId])->fetchColumn();
    }

    public static function create(int $modelId, string $source, ?int $siswaId, ?string $name, array $input, array $result, int $userId): int
    {
        Database::run(
            'INSERT INTO prediksi (model_id, sumber, siswa_id, nama_input, input_json, hasil, keyakinan, distribusi_json, jalur_json, dibuat_oleh) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$modelId, $source, $siswaId, $name, json_out($input), $result['kelas'], round($result['keyakinan'], 4), json_out($result['distribusi']), json_out($result['jalur']), $userId]
        );
        return (int) Database::pdo()->lastInsertId();
    }

    /** Hapus hasil prediksi massal sebuah model sebelum dijalankan ulang. */
    public static function deleteBatch(int $modelId): void
    {
        Database::run("DELETE FROM prediksi WHERE sumber = 'massal' AND model_id = ?", [$modelId]);
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM prediksi WHERE id = ?', [$id]);
    }

    public static function recent(int $limit = 5, ?int $schoolId = null): array
    {
        return Database::run(
            'SELECT p.id, p.sumber, p.hasil, p.keyakinan, p.created_at, COALESCE(s.nama, p.nama_input) AS nama
             FROM prediksi p LEFT JOIN siswa s ON s.id = p.siswa_id' . ($schoolId ? ' WHERE ' . self::schoolWhere() : '') . ' ORDER BY p.id DESC LIMIT ' . max(1, $limit),
            $schoolId ? [$schoolId, Sekolah::find($schoolId)['kode']] : []
        )->fetchAll();
    }

    public static function forSiswa(int $siswaId): array
    {
        return Database::run(
            'SELECT p.id, p.sumber, p.hasil, p.keyakinan, p.created_at, m.nama AS model_nama FROM prediksi p JOIN model_id3 m ON m.id = p.model_id
             WHERE p.siswa_id = ? ORDER BY p.id DESC',
            [$siswaId]
        )->fetchAll();
    }

    public static function countBySource(): array
    {
        return array_column(Database::run('SELECT sumber, COUNT(*) AS total FROM prediksi GROUP BY sumber')->fetchAll(), 'total', 'sumber');
    }

    public static function countBatchByResult(int $modelId, ?int $schoolId = null): array
    {
        $counts = Database::run("SELECT p.hasil, COUNT(DISTINCT p.siswa_id) AS total FROM prediksi p JOIN siswa s ON s.id = p.siswa_id WHERE p.sumber = 'massal' AND p.model_id = ? AND s.label IS NULL" . ($schoolId ? ' AND s.sekolah_id = ?' : '') . ' GROUP BY p.hasil', $schoolId ? [$modelId, $schoolId] : [$modelId])->fetchAll();
        return array_replace(array_fill_keys(Id3::CLASSES, 0), array_column($counts, 'total', 'hasil'));
    }

    public static function export(?int $schoolId = null): array
    {
        return Database::run(
            'SELECT p.id, p.sumber, p.hasil, p.keyakinan, p.created_at, COALESCE(s.nama, p.nama_input) AS nama, s.kode_siswa, m.nama AS model_nama
             FROM prediksi p JOIN model_id3 m ON m.id = p.model_id LEFT JOIN siswa s ON s.id = p.siswa_id' . ($schoolId ? ' WHERE ' . self::schoolWhere() : '') . ' ORDER BY p.id DESC',
            $schoolId ? [$schoolId, Sekolah::find($schoolId)['kode']] : []
        )->fetchAll();
    }
}
