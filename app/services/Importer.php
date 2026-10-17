<?php
declare(strict_types=1);

/** Membaca Dataset_Bersih_ID3.xlsx (sheet Data_Numerik dan Data_Tanpa_Label) tanpa library tambahan. */
final class Importer
{
    public const EXPECTED = ['total' => 247, 'berlabel' => 147, 'tanpa_label' => 100];

    /** Kode mapel legger yang disimpan ke nilai_mapel. */
    public const SUBJECTS = ['QH', 'AA', 'FIK', 'SKI', 'BAR', 'PP', 'BINDO', 'MTK', 'BING', 'PJOK', 'SEJ', 'SB', 'ITaf', 'IHad', 'UFiq', 'ABAR', 'BIO', 'KIM', 'FIS', 'INFOP', 'MTL', 'SOS', 'EKO', 'GEO', 'BIDTL', 'BIGTL', 'BARTL', 'BKOR', 'VKET'];

    private const SCORE_COLUMNS = [
        'Rata_Agama' => 'rata_agama', 'Nilai_BArab' => 'nilai_barab', 'Rata_Bahasa' => 'rata_bahasa',
        'Nilai_MTK' => 'nilai_mtk', 'Rata_MIPA' => 'rata_mipa', 'Rata_IPS' => 'rata_ips', 'Rata_Umum' => 'rata_umum',
    ];

    /** @return array<string, list<array<string, string>>> nama sheet => baris (kunci = header) */
    public static function readXlsx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::RDONLY) !== true) {
            throw new RuntimeException('File bukan workbook Excel (.xlsx) yang valid.');
        }
        try {
            $shared = [];
            if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
                foreach (self::xml($xml)->si as $item) {
                    $shared[] = implode('', array_map('strval', $item->xpath('.//*[local-name()="t"]')));
                }
            }
            $targets = [];
            foreach (self::xml((string) $zip->getFromName('xl/_rels/workbook.xml.rels'))->Relationship as $relation) {
                $targets[(string) $relation['Id']] = (string) $relation['Target'];
            }
            $sheets = [];
            foreach (self::xml((string) $zip->getFromName('xl/workbook.xml'))->sheets->sheet as $sheet) {
                $target = $targets[(string) $sheet->attributes('r', true)->id] ?? '';
                $file = str_starts_with($target, '/') ? ltrim($target, '/') : 'xl/' . $target;
                $content = $zip->getFromName($file);
                if ($content !== false) {
                    $sheets[(string) $sheet['name']] = self::sheetRows(self::xml($content), $shared);
                }
            }
            return $sheets;
        } finally {
            $zip->close();
        }
    }

    private static function xml(string $content): SimpleXMLElement
    {
        $xml = $content !== '' ? simplexml_load_string($content, SimpleXMLElement::class, LIBXML_NONET) : false;
        if ($xml === false) {
            throw new RuntimeException('Struktur workbook tidak dapat dibaca.');
        }
        return $xml;
    }

    private static function sheetRows(SimpleXMLElement $sheet, array $shared): array
    {
        $grid = [];
        foreach ($sheet->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $cell) {
                $column = preg_replace('/\d+$/', '', (string) $cell['r']);
                $type = (string) $cell['t'];
                $value = match ($type) {
                    's' => $shared[(int) $cell->v] ?? '',
                    'inlineStr' => implode('', array_map('strval', $cell->xpath('.//*[local-name()="t"]'))),
                    default => (string) $cell->v,
                };
                $cells[$column] = trim($value);
            }
            $grid[] = $cells;
        }
        $header = array_shift($grid) ?? [];
        $rows = [];
        foreach ($grid as $cells) {
            $row = [];
            foreach ($header as $column => $name) {
                $row[$name] = $cells[$column] ?? '';
            }
            if (implode('', $row) !== '') {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * Ubah sheet menjadi baris siswa siap simpan dan kumpulkan kesalahan validasi.
     * @return array{rows: list<array>, errors: list<string>, summary: array}
     */
    public static function parse(array $sheets, array $domains): array
    {
        $errors = [];
        foreach (['Data_Numerik', 'Data_Tanpa_Label'] as $name) {
            if (!isset($sheets[$name])) {
                $errors[] = "Sheet {$name} tidak ditemukan.";
            }
        }
        if ($errors) {
            return ['rows' => [], 'errors' => $errors, 'summary' => []];
        }
        $rows = [];
        foreach ($sheets['Data_Numerik'] as $line => $raw) {
            $row = self::identity($raw);
            $row['label'] = $raw['Minat_Jurusan'] ?? '';
            $row['sumber_label'] = $raw['Sumber_Label'] ?? '';
            $row['detail_label'] = $raw['Detail_Label'] ?? '';
            $row['jurusan_diterima'] = $raw['Jurusan_Diterima'] ?? '';
            $row['nilai'] = [];
            foreach (self::SCORE_COLUMNS as $source => $column) {
                $row['nilai'][$column] = ($raw[$source] ?? '') === '' ? null : $raw[$source];
            }
            $row['mapel'] = [];
            foreach (self::SUBJECTS as $subject) {
                if (($raw[$subject] ?? '') !== '') {
                    $row['mapel'][$subject] = $raw[$subject];
                }
            }
            $row['atribut'] = null;
            array_push($errors, ...self::validate($row, 'Data_Numerik baris ' . ($line + 2), $domains));
            $rows[] = $row;
        }
        foreach ($sheets['Data_Tanpa_Label'] as $line => $raw) {
            $row = self::identity($raw);
            $row += ['label' => '', 'sumber_label' => '', 'detail_label' => '', 'jurusan_diterima' => '', 'nilai' => null, 'mapel' => []];
            $row['atribut'] = [];
            foreach (array_keys($domains) as $attribute) {
                if (!in_array($attribute, ['Sekolah_Asal', 'Peminatan'], true)) {
                    $row['atribut'][$attribute] = $raw[$attribute] ?? '';
                }
            }
            array_push($errors, ...self::validate($row, 'Data_Tanpa_Label baris ' . ($line + 2), $domains));
            $rows[] = $row;
        }
        $codes = array_count_values(array_column($rows, 'kode_siswa'));
        foreach ($codes as $code => $count) {
            if ($count > 1) {
                $errors[] = "ID siswa {$code} muncul {$count} kali.";
            }
        }
        $labeled = count(array_filter($rows, static fn (array $row): bool => $row['label'] !== ''));
        $distribution = [];
        foreach ($rows as $row) {
            if ($row['label'] !== '') {
                $distribution[$row['sekolah_kode']][$row['label']] = ($distribution[$row['sekolah_kode']][$row['label']] ?? 0) + 1;
            }
        }
        $summary = ['total' => count($rows), 'berlabel' => $labeled, 'tanpa_label' => count($rows) - $labeled, 'distribusi' => $distribution];
        foreach (self::EXPECTED as $key => $expected) {
            if ($summary[$key] !== $expected) {
                $errors[] = "Jumlah {$key} {$summary[$key]}, seharusnya {$expected}. Import dihentikan sesuai aturan data.";
            }
        }
        return ['rows' => $rows, 'errors' => $errors, 'summary' => $summary];
    }

    private static function identity(array $raw): array
    {
        $jk = strtoupper($raw['JK'] ?? '');
        return [
            'kode_siswa' => $raw['ID_Siswa'] ?? '',
            'sekolah_kode' => str_starts_with($raw['Sekolah'] ?? '', 'MAN') ? 'MAN' : (str_starts_with($raw['Sekolah'] ?? '', 'MAS') ? 'MAS' : ''),
            'kelas' => $raw['Kelas'] ?? '',
            'nama' => $raw['Nama'] ?? '',
            'nis' => $raw['NIS'] ?? '',
            'nisn' => $raw['NISN'] ?? '',
            'jk' => in_array($jk, ['L', 'P'], true) ? $jk : '',
            'peminatan' => $raw['Peminatan'] ?? '',
        ];
    }

    private static function validate(array $row, string $where, array $domains): array
    {
        $errors = [];
        if (!preg_match('/^[A-Z]{3}-K\d+-\d+$/D', $row['kode_siswa'])) {
            $errors[] = "{$where}: ID siswa tidak valid.";
        }
        if ($row['sekolah_kode'] === '') {
            $errors[] = "{$where}: sekolah tidak dikenali.";
        }
        if ($row['nama'] === '' || mb_strlen($row['nama']) > 150 || $row['kelas'] === '') {
            $errors[] = "{$where}: nama atau kelas kosong.";
        }
        if (!in_array($row['peminatan'], $domains['Peminatan'] ?? [], true)) {
            $errors[] = "{$where}: peminatan \"{$row['peminatan']}\" tidak dikenal.";
        }
        if ($row['nilai'] !== null) {
            if (!in_array($row['label'], Id3::CLASSES, true)) {
                $errors[] = "{$where}: label \"{$row['label']}\" tidak dikenal.";
            }
            foreach ([...array_values($row['nilai']), ...array_values($row['mapel'])] as $value) {
                if ($value !== null && (!is_numeric($value) || $value < 0 || $value > 100)) {
                    $errors[] = "{$where}: nilai harus angka 0 sampai 100.";
                    break;
                }
            }
        }
        foreach ($row['atribut'] ?? [] as $attribute => $value) {
            if (!in_array($value, $domains[$attribute] ?? [], true)) {
                $errors[] = "{$where}: nilai {$attribute} \"{$value}\" tidak dikenal.";
            }
        }
        return $errors;
    }

    /** Ganti seluruh data siswa dengan hasil import dalam satu transaksi. Mengembalikan jumlah baris tersimpan. */
    public static function store(array $rows, Discretizer $discretizer): int
    {
        $schools = array_column(Sekolah::all(), 'id', 'kode');
        return Database::transaction(static function () use ($rows, $discretizer, $schools): int {
            Siswa::deleteAll();
            foreach ($rows as $row) {
                $row['sekolah_id'] = $schools[$row['sekolah_kode']];
                $categories = $row['atribut'] ?? $discretizer->attributes(array_map(static fn ($v): ?float => $v === null ? null : (float) $v, $row['nilai']));
                $row['atribut'] = ['Sekolah_Asal' => $row['sekolah_kode'], 'Peminatan' => $row['peminatan']] + $categories;
                Siswa::save($row);
            }
            return count($rows);
        });
    }
}
