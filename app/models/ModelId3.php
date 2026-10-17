<?php
declare(strict_types=1);

/** Model pohon keputusan ID3 yang tersimpan. */
final class ModelId3
{
    public const SCENARIOS = [
        'semua' => 'Semua data berlabel',
        'man' => 'MAN 4 Aceh Utara saja',
        'mas' => 'MAS Al-Muslimun saja',
        'tanpa_campuran' => 'Tanpa kelas Campuran',
    ];

    public static function all(): array
    {
        return Database::run(
            'SELECT m.id, m.nama, m.skenario, m.jumlah_data, m.jumlah_node, m.jumlah_daun, m.kedalaman, m.akurasi_latih, m.aktif, m.created_at,
                    u.nama AS pembuat, (SELECT e.akurasi FROM evaluasi e WHERE e.model_id = m.id ORDER BY e.id DESC LIMIT 1) AS akurasi_uji
             FROM model_id3 m LEFT JOIN users u ON u.id = m.dibuat_oleh ORDER BY m.id DESC'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $row = Database::run('SELECT m.*, u.nama AS pembuat FROM model_id3 m LEFT JOIN users u ON u.id = m.dibuat_oleh WHERE m.id = ?', [$id])->fetch();
        if (!$row) {
            return null;
        }
        $row['tree'] = json_decode($row['tree_json'], true, 512, JSON_THROW_ON_ERROR);
        $row['atribut'] = json_decode($row['atribut_dipakai'], true, 512, JSON_THROW_ON_ERROR);
        return $row;
    }

    public static function active(): ?array
    {
        $id = Database::run('SELECT id FROM model_id3 WHERE aktif = 1 LIMIT 1')->fetchColumn();
        return $id ? self::find((int) $id) : null;
    }

    public static function create(array $config, array $result, int $userId): int
    {
        return Database::transaction(static function () use ($config, $result, $userId): int {
            if ($config['aktifkan']) {
                Database::run('UPDATE model_id3 SET aktif = 0');
            }
            Database::run(
                'INSERT INTO model_id3 (nama, skenario, jumlah_data, atribut_dipakai, min_sampel, max_depth, tree_json, jumlah_node, jumlah_daun, kedalaman, akurasi_latih, dibuat_oleh, aktif)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $config['nama'], $config['skenario'], count($result['rows']), json_out($config['atribut']), $config['min_sampel'], $config['max_depth'],
                    json_out($result['tree']), $result['stats']['node'], $result['stats']['daun'], $result['stats']['kedalaman'],
                    round($result['akurasi_latih'], 4), $userId, (int) $config['aktifkan'],
                ]
            );
            $id = (int) Database::pdo()->lastInsertId();
            foreach ($result['steps'] as $number => $step) {
                Database::run(
                    'INSERT INTO model_langkah (model_id, urutan, node_path, jumlah, distribusi, entropy_parent, detail_gain, atribut_terpilih) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [$id, $number + 1, mb_substr($step['node_path'], 0, 500), $step['jumlah'], json_out($step['distribusi']), $step['entropy_parent'], json_out($step['detail_gain']), $step['atribut_terpilih']]
                );
            }
            foreach ($result['rules'] as $number => $rule) {
                Database::run(
                    'INSERT INTO model_aturan (model_id, no_aturan, kondisi, kelas, support, keyakinan) VALUES (?, ?, ?, ?, ?, ?)',
                    [$id, $number + 1, json_out($rule['kondisi']), $rule['kelas'], $rule['support'], round($rule['keyakinan'], 4)]
                );
            }
            return $id;
        });
    }

    public static function activate(int $id): void
    {
        Database::transaction(static function () use ($id): void {
            Database::run('UPDATE model_id3 SET aktif = 0');
            Database::run('UPDATE model_id3 SET aktif = 1 WHERE id = ?', [$id]);
        });
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM model_id3 WHERE id = ?', [$id]);
    }

    public static function steps(int $id): array
    {
        return array_map(static function (array $row): array {
            $row['distribusi'] = json_decode($row['distribusi'], true);
            $row['detail_gain'] = json_decode($row['detail_gain'], true);
            return $row;
        }, Database::run('SELECT * FROM model_langkah WHERE model_id = ? ORDER BY urutan', [$id])->fetchAll());
    }

    public static function rules(int $id): array
    {
        return array_map(static function (array $row): array {
            $row['kondisi'] = json_decode($row['kondisi'], true);
            return $row;
        }, Database::run('SELECT * FROM model_aturan WHERE model_id = ? ORDER BY no_aturan', [$id])->fetchAll());
    }

    /** Konfigurasi latih dari model tersimpan, dipakai ulang oleh pengujian k-fold. */
    public static function config(array $model): array
    {
        return [
            'skenario' => $model['skenario'],
            'atribut' => $model['atribut'],
            'min_sampel' => (int) $model['min_sampel'],
            'max_depth' => $model['max_depth'] === null ? null : (int) $model['max_depth'],
        ];
    }

    public static function ruleText(array $conditions, string $class): string
    {
        $parts = array_map(static fn (array $c): string => $c['atribut'] . ' = ' . $c['nilai'], $conditions);
        return 'JIKA ' . ($parts ? implode(' DAN ', $parts) : '(semua data)') . ' MAKA ' . $class;
    }
}
