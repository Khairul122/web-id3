<?php
declare(strict_types=1);

final class Evaluasi
{
    public static function all(): array
    {
        return Database::run(
            'SELECT e.id, e.model_id, e.k, e.jumlah_data, e.akurasi, e.precision_macro, e.recall_macro, e.f1_macro, e.created_at,
                    m.nama AS model_nama, m.skenario
             FROM evaluasi e JOIN model_id3 m ON m.id = e.model_id ORDER BY e.id DESC'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::run(
            'SELECT e.*, m.nama AS model_nama, m.skenario, m.atribut_dipakai, m.min_sampel, m.max_depth, u.nama AS pembuat
             FROM evaluasi e JOIN model_id3 m ON m.id = e.model_id LEFT JOIN users u ON u.id = e.dibuat_oleh WHERE e.id = ?',
            [$id]
        )->fetch();
        if (!$row) {
            return null;
        }
        foreach (['confusion_json', 'per_kelas_json', 'detail_fold_json', 'atribut_dipakai'] as $column) {
            $row[$column] = json_decode($row[$column], true);
        }
        return $row;
    }

    public static function create(int $modelId, int $k, array $result, int $userId): int
    {
        Database::run(
            'INSERT INTO evaluasi (model_id, k, jumlah_data, akurasi, precision_macro, recall_macro, f1_macro, confusion_json, per_kelas_json, detail_fold_json, dibuat_oleh)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                $modelId, $k, $result['jumlah'], round($result['akurasi'], 4), round($result['precision'], 4), round($result['recall'], 4), round($result['f1'], 4),
                json_out($result['confusion']), json_out($result['per_kelas']), json_out($result['folds']), $userId,
            ]
        );
        return (int) Database::pdo()->lastInsertId();
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM evaluasi WHERE id = ?', [$id]);
    }

    public static function latestForModel(int $modelId): ?array
    {
        $id = Database::run('SELECT id FROM evaluasi WHERE model_id = ? ORDER BY id DESC LIMIT 1', [$modelId])->fetchColumn();
        return $id ? self::find((int) $id) : null;
    }
}
