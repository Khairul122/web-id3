<?php
declare(strict_types=1);

/** Pengujian model tersimpan dengan stratified k-fold memakai konfigurasi training yang sama. */
final class EvaluasiService
{
    /**
     * @return array{id: int, result: array, catatan: string}
     * @throws DomainException bila data tidak cukup untuk k fold
     */
    public static function run(array $model, int $k, int $userId): array
    {
        $config = ModelId3::config($model);
        $rows = Siswa::trainingRows($config['skenario']);
        if (count($rows) < $k) {
            throw new DomainException('Data skenario ini (' . count($rows) . ' siswa) lebih sedikit dari k.');
        }
        $result = Evaluator::crossValidate($rows, $k, static fn (array $training): array => TrainingService::engine($config, $training)->train($training)['tree']);
        $smallest = min(array_count_values(array_column($rows, 'y')));
        return [
            'id' => Evaluasi::create((int) $model['id'], $k, $result, $userId),
            'result' => $result,
            'catatan' => $smallest < $k ? 'Ada kelas dengan data kurang dari k sehingga tidak muncul di setiap fold.' : '',
        ];
    }

    /** Kelas yang muncul sebagai data aktual atau hasil prediksi, beserta nilai sel terbesar untuk heatmap. */
    public static function matrixView(array $matrix): array
    {
        $classes = array_values(array_filter(array_keys($matrix), static fn (string $c): bool => array_sum($matrix[$c]) > 0 || array_sum(array_column($matrix, $c)) > 0));
        $max = 0;
        foreach ($classes as $actual) {
            foreach ($classes as $predicted) {
                $max = max($max, (int) $matrix[$actual][$predicted]);
            }
        }
        return [$classes, $max];
    }
}
