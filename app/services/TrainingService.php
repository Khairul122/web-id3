<?php
declare(strict_types=1);

/** Validasi konfigurasi training dan pembentukan model ID3 dari data di database. */
final class TrainingService
{
    public static function defaults(): array
    {
        return [
            'nama' => 'Model ' . (new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta')))->format('d-m-Y H.i'),
            'skenario' => 'semua',
            'atribut' => Atribut::activeCodes(),
            'min_sampel' => 2,
            'max_depth' => null,
            'aktifkan' => true,
        ];
    }

    /** @return array{0: array, 1: array<string, string>} */
    public static function fromPost(): array
    {
        $codes = array_column(Atribut::all(), 'kode');
        $chosen = $_POST['atribut'] ?? [];
        $chosen = is_array($chosen) ? array_values(array_intersect($codes, array_filter($chosen, 'is_string'))) : [];
        $form = [
            'nama' => trim(input('nama')),
            'skenario' => input('skenario'),
            'atribut' => $chosen,
            'min_sampel' => trim(input('min_sampel')),
            'max_depth' => trim(input('max_depth')),
            'aktifkan' => input('aktifkan') === '1',
        ];
        $errors = [];
        if ($form['nama'] === '' || mb_strlen($form['nama']) > 100) {
            $errors['nama'] = 'Nama model wajib diisi, maksimal 100 karakter.';
        }
        if (!isset(ModelId3::SCENARIOS[$form['skenario']])) {
            $errors['skenario'] = 'Pilih skenario data.';
        }
        if (!$chosen) {
            $errors['atribut'] = 'Pilih minimal satu atribut.';
        }
        if (!ctype_digit($form['min_sampel']) || (int) $form['min_sampel'] < 1 || (int) $form['min_sampel'] > 50) {
            $errors['min_sampel'] = 'Isi angka 1 sampai 50.';
        }
        if ($form['max_depth'] !== '' && (!ctype_digit($form['max_depth']) || (int) $form['max_depth'] < 1 || (int) $form['max_depth'] > 20)) {
            $errors['max_depth'] = 'Kosongkan atau isi angka 1 sampai 20.';
        }
        if (!$errors) {
            $form['min_sampel'] = (int) $form['min_sampel'];
            $form['max_depth'] = $form['max_depth'] === '' ? null : (int) $form['max_depth'];
        }
        return [$form, $errors];
    }

    /** Mesin ID3 dengan domain dari tabel atribut_nilai ditambah nilai yang muncul di data. */
    public static function engine(array $config, array $rows): Id3
    {
        $domains = Atribut::domains();
        foreach ($rows as $row) {
            foreach ($config['atribut'] as $code) {
                $value = $row['x'][$code] ?? null;
                if ($value !== null && !in_array($value, $domains[$code] ?? [], true)) {
                    $domains[$code][] = $value;
                }
            }
        }
        return new Id3($config['atribut'], $domains, $config['min_sampel'], $config['max_depth']);
    }

    /** Ringkasan sebelum training: distribusi, entropy, dan gain tiap atribut di akar. */
    public static function preview(array $config): array
    {
        $rows = Siswa::trainingRows($config['skenario']);
        $engine = self::engine($config, $rows);
        $distribution = $engine->distribution($rows);
        $entropy = $rows ? Id3::entropy($distribution) : 0.0;
        $gains = $rows ? array_map(static fn (string $code): array => $engine->gain($rows, $code, $entropy), $config['atribut']) : [];
        return [
            'jumlah' => count($rows),
            'distribusi' => $distribution,
            'entropy' => $entropy,
            'gains' => $gains,
            'gain_terbaik' => $gains ? max(array_column($gains, 'gain')) : 0,
        ];
    }

    /**
     * Latih ID3 dari data di database sesuai konfigurasi.
     * @return array{tree: array, steps: list<array>, rows: list<array>, rules: list<array>, stats: array, akurasi_latih: float}
     */
    public static function train(array $config): array
    {
        $rows = Siswa::trainingRows($config['skenario']);
        if (count($rows) < 2) {
            throw new DomainException('Data latih untuk skenario ini kurang dari 2 siswa.');
        }
        $result = self::engine($config, $rows)->train($rows);
        $correct = 0;
        foreach ($rows as $row) {
            $correct += (int) (Id3::predict($result['tree'], $row['x'])['kelas'] === $row['y']);
        }
        return $result + [
            'rows' => $rows,
            'rules' => Id3::rules($result['tree']),
            'stats' => Id3::stats($result['tree']),
            'akurasi_latih' => $correct / count($rows),
        ];
    }

    /** Latih lalu simpan. Mengembalikan [id model, hasil training]. */
    public static function run(array $config, int $userId): array
    {
        $result = self::train($config);
        return [ModelId3::create($config, $result, $userId), $result];
    }
}
