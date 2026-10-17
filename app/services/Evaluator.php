<?php
declare(strict_types=1);

/** Stratified k-fold cross validation, confusion matrix, dan metrik macro. */
final class Evaluator
{
    public const SEED = 42;

    /**
     * Bagi indeks baris ke k fold. Tiap kelas diacak dengan seed tetap lalu dibagikan bergiliran.
     * @return list<list<int>>
     */
    public static function folds(array $rows, int $k, int $seed = self::SEED): array
    {
        $byClass = [];
        foreach ($rows as $index => $row) {
            $byClass[$row['y']][] = $index;
        }
        ksort($byClass);
        mt_srand($seed);
        $folds = array_fill(0, $k, []);
        $cursor = 0;
        foreach ($byClass as $indexes) {
            shuffle($indexes);
            foreach ($indexes as $index) {
                $folds[$cursor % $k][] = $index;
                $cursor++;
            }
        }
        mt_srand();
        return $folds;
    }

    /** @param callable(array): array $train fungsi yang mengembalikan pohon dari baris latih */
    public static function crossValidate(array $rows, int $k, callable $train, array $classes = Id3::CLASSES): array
    {
        if ($k < 2 || $k > count($rows)) {
            throw new InvalidArgumentException('Nilai k tidak sesuai dengan jumlah data.');
        }
        $matrix = [];
        foreach ($classes as $actual) {
            foreach ($classes as $predicted) {
                $matrix[$actual][$predicted] = 0;
            }
        }
        $foldDetails = [];
        foreach (self::folds($rows, $k) as $number => $testIndexes) {
            $testSet = array_flip($testIndexes);
            $training = [];
            foreach ($rows as $index => $row) {
                if (!isset($testSet[$index])) {
                    $training[] = $row;
                }
            }
            $tree = $train($training);
            $correct = 0;
            foreach ($testIndexes as $index) {
                $predicted = Id3::predict($tree, $rows[$index]['x'])['kelas'];
                $matrix[$rows[$index]['y']][$predicted]++;
                $correct += (int) ($predicted === $rows[$index]['y']);
            }
            $foldDetails[] = [
                'fold' => $number + 1,
                'latih' => count($training),
                'uji' => count($testIndexes),
                'benar' => $correct,
                'akurasi' => count($testIndexes) ? $correct / count($testIndexes) : 0.0,
            ];
        }
        return ['confusion' => $matrix, 'folds' => $foldDetails] + self::metrics($matrix);
    }

    /** Metrik dari confusion matrix. Kelas tanpa data aktual maupun prediksi tidak ikut rata-rata macro. */
    public static function metrics(array $matrix): array
    {
        $total = 0;
        $correct = 0;
        $perClass = [];
        foreach ($matrix as $actual => $row) {
            $total += array_sum($row);
            $correct += $row[$actual] ?? 0;
        }
        foreach (array_keys($matrix) as $class) {
            $tp = $matrix[$class][$class];
            $actualCount = array_sum($matrix[$class]);
            $predictedCount = array_sum(array_column($matrix, $class));
            if ($actualCount === 0 && $predictedCount === 0) {
                continue;
            }
            $precision = $predictedCount ? (float) ($tp / $predictedCount) : 0.0;
            $recall = $actualCount ? (float) ($tp / $actualCount) : 0.0;
            $perClass[$class] = [
                'precision' => $precision,
                'recall' => $recall,
                'f1' => $precision + $recall > 0 ? 2 * $precision * $recall / ($precision + $recall) : 0.0,
                'support' => $actualCount,
            ];
        }
        $count = max(count($perClass), 1);
        return [
            'jumlah' => $total,
            'akurasi' => $total ? (float) ($correct / $total) : 0.0,
            'precision' => array_sum(array_column($perClass, 'precision')) / $count,
            'recall' => array_sum(array_column($perClass, 'recall')) / $count,
            'f1' => array_sum(array_column($perClass, 'f1')) / $count,
            'per_kelas' => $perClass,
        ];
    }
}
