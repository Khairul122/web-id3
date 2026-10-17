<?php
declare(strict_types=1);

// Ekspor hasil ID3 sistem (data dari database) sebagai JSON untuk dibandingkan dengan tests/verify_id3_dataset.py.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__) . '/app/core/bootstrap.php';

$k = 5;
$output = ['domains' => Atribut::domains(), 'atribut' => Atribut::activeCodes(), 'k' => $k, 'skenario' => []];
foreach (array_keys(ModelId3::SCENARIOS) as $scenario) {
    $config = ['skenario' => $scenario, 'atribut' => $output['atribut'], 'min_sampel' => 2, 'max_depth' => null];
    $result = TrainingService::train($config);
    $codes = Database::run('SELECT id, kode_siswa FROM siswa')->fetchAll(PDO::FETCH_KEY_PAIR);
    $folds = array_map(static fn (array $fold): array => array_map(static fn (int $i): string => $codes[$result['rows'][$i]['id']], $fold), Evaluator::folds($result['rows'], $k));
    $evaluation = Evaluator::crossValidate($result['rows'], $k, static fn (array $rows): array => TrainingService::engine($config, $rows)->train($rows)['tree']);
    $output['skenario'][$scenario] = [
        'kode_siswa' => array_map(static fn (array $row): string => $codes[$row['id']], $result['rows']),
        'tree' => $result['tree'],
        'steps' => $result['steps'],
        'akurasi_latih' => $result['akurasi_latih'],
        'folds' => $folds,
        'confusion' => $evaluation['confusion'],
        'akurasi' => $evaluation['akurasi'],
        'f1' => $evaluation['f1'],
    ];
}
echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
