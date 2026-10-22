<?php
declare(strict_types=1);

final class Siswa
{
    public const PER_PAGE = 20;

    /** @return array{rows: list<array>, total: int} */
    public static function paginate(array $filter, int $page): array
    {
        [$where, $params] = self::where($filter);
        $total = (int) Database::run("SELECT COUNT(*) FROM siswa s WHERE {$where}", $params)->fetchColumn();
        $offset = (max($page, 1) - 1) * self::PER_PAGE;
        $rows = Database::run(
            "SELECT s.id, s.kode_siswa, s.nama, s.kelas, s.peminatan, s.label, k.kode AS sekolah_kode
             FROM siswa s JOIN sekolah k ON k.id = s.sekolah_id WHERE {$where}
             ORDER BY s.kode_siswa LIMIT " . self::PER_PAGE . " OFFSET {$offset}",
            $params
        )->fetchAll();
        return ['rows' => $rows, 'total' => $total];
    }

    /** Semua baris sesuai filter, untuk cetak dan CSV. */
    public static function export(array $filter): array
    {
        [$where, $params] = self::where($filter);
        $rows = Database::run(
            "SELECT s.*, k.kode AS sekolah_kode, k.nama AS sekolah_nama FROM siswa s JOIN sekolah k ON k.id = s.sekolah_id
             WHERE {$where} ORDER BY s.kode_siswa",
            $params
        )->fetchAll();
        $attributes = self::attributeMap(array_column($rows, 'id'));
        foreach ($rows as &$row) {
            $row['atribut'] = $attributes[$row['id']] ?? [];
        }
        return $rows;
    }

    private static function where(array $filter): array
    {
        $where = ['1 = 1'];
        $params = [];
        if (($filter['q'] ?? '') !== '') {
            $where[] = '(s.nama LIKE ? OR s.kode_siswa LIKE ? OR s.nisn LIKE ?)';
            $like = '%' . addcslashes($filter['q'], '%_\\') . '%';
            array_push($params, $like, $like, $like);
        }
        if (!empty($filter['sekolah_id'])) {
            $where[] = 's.sekolah_id = ?';
            $params[] = (int) $filter['sekolah_id'];
        }
        if (($filter['kelas'] ?? '') !== '') {
            $where[] = 's.kelas = ?';
            $params[] = $filter['kelas'];
        }
        if (($filter['label'] ?? '') === 'none') {
            $where[] = 's.label IS NULL';
        } elseif (($filter['label'] ?? '') === 'labeled') {
            $where[] = 's.label IS NOT NULL';
        } elseif (in_array($filter['label'] ?? '', Id3::CLASSES, true)) {
            $where[] = 's.label = ?';
            $params[] = $filter['label'];
        }
        return [implode(' AND ', $where), $params];
    }

    public static function kelasList(): array
    {
        return array_column(Database::run('SELECT DISTINCT kelas FROM siswa ORDER BY kelas')->fetchAll(), 'kelas');
    }

    public static function countUnpredicted(int $modelId): int
    {
        return (int) Database::run("SELECT COUNT(*) FROM siswa s WHERE s.label IS NULL AND NOT EXISTS (SELECT 1 FROM prediksi p WHERE p.siswa_id = s.id AND p.sumber = 'massal' AND p.model_id = ?)", [$modelId])->fetchColumn();
    }

    public static function find(int $id): ?array
    {
        $row = Database::run(
            'SELECT s.*, k.kode AS sekolah_kode, k.nama AS sekolah_nama FROM siswa s JOIN sekolah k ON k.id = s.sekolah_id WHERE s.id = ?',
            [$id]
        )->fetch();
        if (!$row) {
            return null;
        }
        $row['nilai'] = Database::run('SELECT * FROM nilai_rata WHERE siswa_id = ?', [$id])->fetch() ?: null;
        $mapel = array_column(Database::run('SELECT kode_mapel, nilai FROM nilai_mapel WHERE siswa_id = ?', [$id])->fetchAll(), 'nilai', 'kode_mapel');
        // Urutan kolom mengikuti legger sekolah.
        $row['mapel'] = array_intersect_key(array_replace(array_flip(Importer::SUBJECTS), $mapel), $mapel);
        $row['atribut'] = self::attributeMap([$id])[$id] ?? [];
        return $row;
    }

    public static function findByKode(string $kode): ?array
    {
        $id = Database::run('SELECT id FROM siswa WHERE kode_siswa = ?', [$kode])->fetchColumn();
        return $id ? self::find((int) $id) : null;
    }

    public static function kodeExists(string $kode, ?int $exceptId = null): bool
    {
        return (bool) Database::run('SELECT 1 FROM siswa WHERE kode_siswa = ? AND id <> ?', [$kode, $exceptId ?? 0])->fetchColumn();
    }

    /** @return array<int, array<string, string>> siswa_id => [kode atribut => nilai] */
    private static function attributeMap(array $ids): array
    {
        if (!$ids) {
            return [];
        }
        $marks = implode(',', array_fill(0, count($ids), '?'));
        $map = [];
        foreach (Database::run("SELECT sa.siswa_id, a.kode, sa.nilai FROM siswa_atribut sa JOIN atribut a ON a.id = sa.atribut_id WHERE sa.siswa_id IN ({$marks}) ORDER BY a.urutan", array_values($ids))->fetchAll() as $row) {
            $map[(int) $row['siswa_id']][$row['kode']] = $row['nilai'];
        }
        return $map;
    }

    /**
     * Simpan siswa beserta nilai dan kategori atributnya.
     * $data['atribut'] wajib berisi kategori final untuk setiap atribut.
     */
    public static function save(array $data, ?int $id = null): int
    {
        $id = self::write($data, $id);
        // Form tidak mengubah nilai mapel legger; nilai mapel hanya diganti bila dikirim.
        if (isset($data['mapel'])) {
            Database::run('DELETE FROM nilai_mapel WHERE siswa_id = ?', [$id]);
            foreach ($data['mapel'] as $subject => $value) {
                Database::run('INSERT INTO nilai_mapel (siswa_id, kode_mapel, nilai) VALUES (?, ?, ?)', [$id, $subject, (int) round((float) $value)]);
            }
        }
        self::saveScores($id, $data['nilai'] ?? []);
        self::saveAttributes($id, $data['atribut']);
        return $id;
    }

    private static function write(array $data, ?int $id): int
    {
        $values = [
            $data['kode_siswa'], $data['sekolah_id'], $data['kelas'], $data['nama'],
            self::nullable($data['nis'] ?? ''), self::nullable($data['nisn'] ?? ''), self::nullable($data['jk'] ?? ''),
            $data['peminatan'], self::nullable($data['label'] ?? ''), self::nullable($data['sumber_label'] ?? ''),
            self::nullable($data['detail_label'] ?? ''), self::nullable($data['jurusan_diterima'] ?? ''),
        ];
        if ($id === null) {
            Database::run(
                'INSERT INTO siswa (kode_siswa, sekolah_id, kelas, nama, nis, nisn, jk, peminatan, label, sumber_label, detail_label, jurusan_diterima)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                $values
            );
            return (int) Database::pdo()->lastInsertId();
        }
        Database::run(
            'UPDATE siswa SET kode_siswa = ?, sekolah_id = ?, kelas = ?, nama = ?, nis = ?, nisn = ?, jk = ?, peminatan = ?,
             label = ?, sumber_label = ?, detail_label = ?, jurusan_diterima = ? WHERE id = ?',
            [...$values, $id]
        );
        return $id;
    }

    private static function saveScores(int $id, array $nilai): void
    {
        if (!array_filter($nilai, static fn ($value): bool => $value !== null)) {
            Database::run('DELETE FROM nilai_rata WHERE siswa_id = ?', [$id]);
            return;
        }
        Database::run(
            'REPLACE INTO nilai_rata (siswa_id, rata_agama, nilai_barab, rata_bahasa, nilai_mtk, rata_mipa, rata_ips, rata_umum) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$id, $nilai['rata_agama'] ?? null, $nilai['nilai_barab'] ?? null, $nilai['rata_bahasa'] ?? null, $nilai['nilai_mtk'] ?? null, $nilai['rata_mipa'] ?? null, $nilai['rata_ips'] ?? null, $nilai['rata_umum'] ?? null]
        );
    }

    /** @param array<string, string> $attributes kode atribut => nilai */
    public static function saveAttributes(int $id, array $attributes): void
    {
        $ids = Atribut::ids();
        foreach ($attributes as $code => $value) {
            Database::run('REPLACE INTO siswa_atribut (siswa_id, atribut_id, nilai) VALUES (?, ?, ?)', [$id, $ids[$code], $value]);
        }
    }

    private static function nullable(string $value): ?string
    {
        return $value === '' ? null : $value;
    }

    public static function delete(int $id): void
    {
        Database::run('DELETE FROM siswa WHERE id = ?', [$id]);
    }

    public static function deleteAll(): void
    {
        Database::run('DELETE FROM siswa');
    }

    /** Semua baris nilai_rata (nilai angka) untuk dihitung ulang kategorinya. */
    public static function scoreRows(): array
    {
        return Database::run('SELECT * FROM nilai_rata')->fetchAll();
    }

    /**
     * Baris berlabel untuk ID3 sesuai skenario.
     * @return list<array{id: int, x: array<string, string>, y: string}>
     */
    public static function trainingRows(string $scenario): array
    {
        $where = match ($scenario) {
            'man' => "k.kode = 'MAN'",
            'mas' => "k.kode = 'MAS'",
            'tanpa_campuran' => "s.label <> 'Campuran'",
            default => '1 = 1',
        };
        $rows = Database::run("SELECT s.id, s.label FROM siswa s JOIN sekolah k ON k.id = s.sekolah_id WHERE s.label IS NOT NULL AND {$where} ORDER BY s.kode_siswa")->fetchAll();
        $attributes = self::attributeMap(array_column($rows, 'id'));
        return array_map(static fn (array $row): array => ['id' => (int) $row['id'], 'x' => $attributes[$row['id']] ?? [], 'y' => $row['label']], $rows);
    }

    /** Siswa untuk prediksi massal: tanpa label saja (sesuai metode) atau semua siswa. */
    public static function forBatch(bool $includeLabeled = false): array
    {
        $rows = Database::run('SELECT id, kode_siswa, nama, label FROM siswa' . ($includeLabeled ? '' : ' WHERE label IS NULL') . ' ORDER BY kode_siswa')->fetchAll();
        $attributes = self::attributeMap(array_column($rows, 'id'));
        foreach ($rows as &$row) {
            $row['atribut'] = $attributes[$row['id']] ?? [];
        }
        return $rows;
    }

    public static function counts(): array
    {
        return Database::run('SELECT COUNT(*) AS total, SUM(label IS NOT NULL) AS berlabel, SUM(label IS NULL) AS tanpa_label FROM siswa')->fetch();
    }

    /** Jumlah per sekolah dan label untuk dashboard. */
    public static function labelBySchool(): array
    {
        return Database::run(
            "SELECT k.kode, k.nama, SUM(s.label = 'Saintek') AS saintek, SUM(s.label = 'Soshum') AS soshum,
                    SUM(s.label = 'Campuran') AS campuran, SUM(s.label IS NULL) AS tanpa_label, COUNT(s.id) AS total
             FROM sekolah k LEFT JOIN siswa s ON s.sekolah_id = k.id GROUP BY k.id ORDER BY k.id"
        )->fetchAll();
    }
}
