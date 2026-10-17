<?php
declare(strict_types=1);

final class Prediksi
{
    public const PER_PAGE = 20;

    /** @return array{rows: list<array>, total: int} */
    public static function paginate(array $filter, int $page): array
    {
        $where = ['1 = 1'];
        $params = [];
        if (in_array($filter['sumber'] ?? '', ['form', 'massal'], true)) {
            $where[] = 'p.sumber = ?';
            $params[] = $filter['sumber'];
        }
        if (in_array($filter['hasil'] ?? '', Id3::CLASSES, true)) {
            $where[] = 'p.hasil = ?';
            $params[] = $filter['hasil'];
        }
        $where = implode(' AND ', $where);
        $total = (int) Database::run("SELECT COUNT(*) FROM prediksi p WHERE {$where}", $params)->fetchColumn();
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

    public static function recent(int $limit = 5): array
    {
        return Database::run(
            'SELECT p.id, p.sumber, p.hasil, p.keyakinan, p.created_at, COALESCE(s.nama, p.nama_input) AS nama
             FROM prediksi p LEFT JOIN siswa s ON s.id = p.siswa_id ORDER BY p.id DESC LIMIT ' . max(1, $limit)
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

    public static function export(): array
    {
        return Database::run(
            'SELECT p.id, p.sumber, p.hasil, p.keyakinan, p.created_at, COALESCE(s.nama, p.nama_input) AS nama, s.kode_siswa, m.nama AS model_nama
             FROM prediksi p JOIN model_id3 m ON m.id = p.model_id LEFT JOIN siswa s ON s.id = p.siswa_id ORDER BY p.id DESC'
        )->fetchAll();
    }
}
