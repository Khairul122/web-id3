<?php
declare(strict_types=1);

// Olah data penelitian: latih ID3 murni (tanpa pruning) untuk setiap skenario, uji k-fold, aktifkan satu model,
// lalu prediksi siswa tanpa label. Pakai: php scripts/proses_penelitian.php [skenario_aktif] [k]
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__) . '/app/core/bootstrap.php';

$activeScenario = $argv[1] ?? 'tanpa_campuran';
$k = (int) ($argv[2] ?? 5);
if (!isset(ModelId3::SCENARIOS[$activeScenario]) || $k < 3 || $k > 10) {
    fwrite(STDERR, "Skenario atau k tidak valid.\n");
    exit(1);
}
$userId = (int) Database::run("SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1")->fetchColumn();
$date = (new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta')))->format('d-m-Y');
$activeId = null;
foreach (ModelId3::SCENARIOS as $scenario => $label) {
    $config = [
        'nama' => 'ID3 ' . $label . ' ' . $date,
        'skenario' => $scenario,
        'atribut' => Atribut::activeCodes(),
        'min_sampel' => 2,
        'max_depth' => null,
        'aktifkan' => false,
    ];
    [$id, $result] = TrainingService::run($config, $userId);
    $evaluation = EvaluasiService::run(ModelId3::find($id), $k, $userId)['result'];
    printf("%-28s model #%d: %d data, %d node, akurasi latih %s, %d-fold akurasi %s, F1 macro %s\n",
        $label, $id, count($result['rows']), $result['stats']['node'], pct($result['akurasi_latih']), $k, pct($evaluation['akurasi']), pct($evaluation['f1']));
    if ($scenario === $activeScenario) {
        $activeId = $id;
    }
}
ModelId3::activate($activeId);
$summary = PrediksiService::runBatch(ModelId3::find($activeId), $userId);
printf("Model aktif #%d (%s). Prediksi massal: %s\n", $activeId, ModelId3::SCENARIOS[$activeScenario], json_encode($summary));
