<?php
declare(strict_types=1);

/**
 * Algoritma ID3 untuk atribut kategorikal.
 * Baris data: ['x' => [kode_atribut => nilai], 'y' => kelas].
 */
final class Id3
{
    /** Urutan kelas untuk tie-break mayoritas agar hasil deterministik. */
    public const CLASSES = ['Saintek', 'Soshum', 'Campuran'];

    /** @var list<array<string, mixed>> */
    private array $steps = [];

    /**
     * @param list<string> $attributes kode atribut, sudah terurut menurut kolom urutan (tie-break gain)
     * @param array<string, list<string>> $domains nilai yang mungkin untuk tiap atribut
     */
    public function __construct(
        private readonly array $attributes,
        private readonly array $domains,
        private readonly int $minSamples = 2,
        private readonly ?int $maxDepth = null,
        private readonly array $classes = self::CLASSES,
    ) {
    }

    public static function entropy(array $distribution): float
    {
        $total = array_sum($distribution);
        $entropy = 0.0;
        foreach ($distribution as $count) {
            if ($count > 0) {
                $p = $count / $total;
                $entropy -= $p * log($p, 2);
            }
        }
        return $entropy;
    }

    /** @return array<string, int> */
    public function distribution(array $rows): array
    {
        $distribution = [];
        foreach ($this->classes as $class) {
            $distribution[$class] = 0;
        }
        foreach ($rows as $row) {
            $distribution[$row['y']] = ($distribution[$row['y']] ?? 0) + 1;
        }
        return $distribution;
    }

    public static function majority(array $distribution): string
    {
        $best = array_key_first($distribution);
        foreach ($distribution as $class => $count) {
            if ($count > $distribution[$best]) {
                $best = $class;
            }
        }
        return (string) $best;
    }

    /** Gain satu atribut beserta rincian tiap nilai, dipakai untuk tabel langkah. */
    public function gain(array $rows, string $attribute, ?float $parentEntropy = null): array
    {
        $parentEntropy ??= self::entropy($this->distribution($rows));
        $total = count($rows);
        $remainder = 0.0;
        $details = [];
        foreach ($this->domains[$attribute] as $value) {
            $subset = array_values(array_filter($rows, static fn (array $row): bool => $row['x'][$attribute] === $value));
            $distribution = $this->distribution($subset);
            $entropy = $subset ? self::entropy($distribution) : 0.0;
            $remainder += count($subset) / $total * $entropy;
            $details[] = ['nilai' => $value, 'jumlah' => count($subset), 'distribusi' => $distribution, 'entropy' => round($entropy, 6)];
        }
        return ['atribut' => $attribute, 'gain' => round($parentEntropy - $remainder, 6), 'nilai' => $details];
    }

    /** @return array{tree: array, steps: list<array>} */
    public function train(array $rows): array
    {
        if (!$rows) {
            throw new InvalidArgumentException('Data latih kosong.');
        }
        $this->steps = [];
        $tree = $this->build($rows, $this->attributes, 0, []);
        return ['tree' => $tree, 'steps' => $this->steps];
    }

    private function build(array $rows, array $attributes, int $depth, array $path): array
    {
        $distribution = $this->distribution($rows);
        $entropy = self::entropy($distribution);
        $node = ['jumlah' => count($rows), 'distribusi' => $distribution, 'entropy' => round($entropy, 6)];
        $leaf = $node + ['kelas' => self::majority($distribution)];
        if ($entropy == 0.0 || !$attributes || count($rows) < $this->minSamples || ($this->maxDepth !== null && $depth >= $this->maxDepth)) {
            return $leaf;
        }

        $gains = [];
        $best = null;
        foreach ($attributes as $attribute) {
            $gain = $this->gain($rows, $attribute, $entropy);
            $gains[] = $gain;
            // Gain seri: atribut dengan urutan lebih kecil (lebih dulu di daftar) tetap dipilih.
            if ($best === null || $gain['gain'] > $best['gain']) {
                $best = $gain;
            }
        }
        // Sesuai kriteria berhenti proposal (BAB II 2.5): pemecahan tetap dilakukan walau gain 0;
        // berhenti hanya bila kelas sama, atribut habis, atau data habis.
        $chosen = $best['atribut'];
        $this->steps[] = [
            'node_path' => $path ? implode(' > ', array_map(static fn (array $p): string => $p[0] . ' = ' . $p[1], $path)) : 'Akar',
            'jumlah' => count($rows),
            'distribusi' => $distribution,
            'entropy_parent' => round($entropy, 6),
            'detail_gain' => $gains,
            'atribut_terpilih' => $chosen,
        ];
        $node['atribut'] = $chosen;
        $node['cabang'] = [];
        $remaining = array_values(array_diff($attributes, [$chosen]));
        foreach ($this->domains[$chosen] as $value) {
            $subset = array_values(array_filter($rows, static fn (array $row): bool => $row['x'][$chosen] === $value));
            $node['cabang'][$value] = $subset
                ? $this->build($subset, $remaining, $depth + 1, [...$path, [$chosen, $value]])
                : ['jumlah' => 0, 'distribusi' => $distribution, 'entropy' => 0.0, 'kelas' => $leaf['kelas'], 'kosong' => true];
        }
        return $node;
    }

    /**
     * Telusuri pohon. Nilai yang tidak dikenal berhenti di node terakhir dan memakai kelas mayoritasnya.
     * @return array{kelas: string, keyakinan: float, distribusi: array, jalur: list<array>}
     */
    public static function predict(array $tree, array $x): array
    {
        $node = $tree;
        $path = [];
        while (isset($node['atribut'])) {
            $value = $x[$node['atribut']] ?? null;
            if ($value === null || !isset($node['cabang'][$value])) {
                $path[] = ['atribut' => $node['atribut'], 'nilai' => $value ?? '(kosong)', 'dikenal' => false];
                break;
            }
            $path[] = ['atribut' => $node['atribut'], 'nilai' => $value, 'dikenal' => true];
            $node = $node['cabang'][$value];
        }
        $class = $node['kelas'] ?? self::majority($node['distribusi']);
        $total = array_sum($node['distribusi']);
        return [
            'kelas' => $class,
            'keyakinan' => $total > 0 ? $node['distribusi'][$class] / $total : 0.0,
            'distribusi' => $node['distribusi'],
            'jalur' => $path,
        ];
    }

    /** Aturan IF-THEN dari setiap jalur akar ke daun yang memiliki data. */
    public static function rules(array $node, array $conditions = []): array
    {
        if (!isset($node['atribut'])) {
            if (!empty($node['kosong'])) {
                return [];
            }
            return [[
                'kondisi' => $conditions,
                'kelas' => $node['kelas'],
                'support' => $node['jumlah'],
                'keyakinan' => $node['jumlah'] > 0 ? $node['distribusi'][$node['kelas']] / $node['jumlah'] : 0.0,
            ]];
        }
        $rules = [];
        foreach ($node['cabang'] as $value => $child) {
            array_push($rules, ...self::rules($child, [...$conditions, ['atribut' => $node['atribut'], 'nilai' => (string) $value]]));
        }
        return $rules;
    }

    /** @return array{node: int, daun: int, kedalaman: int} */
    public static function stats(array $node, int $depth = 0): array
    {
        if (!isset($node['atribut'])) {
            return ['node' => 1, 'daun' => 1, 'kedalaman' => $depth];
        }
        $stats = ['node' => 1, 'daun' => 0, 'kedalaman' => $depth];
        foreach ($node['cabang'] as $child) {
            $child = self::stats($child, $depth + 1);
            $stats['node'] += $child['node'];
            $stats['daun'] += $child['daun'];
            $stats['kedalaman'] = max($stats['kedalaman'], $child['kedalaman']);
        }
        return $stats;
    }
}
