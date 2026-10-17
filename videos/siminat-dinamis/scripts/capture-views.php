<?php
declare(strict_types=1);

// Render the application's real views with an anonymous example; never persist a prediction.
$root = dirname(__DIR__, 3);
require $root . '/app/core/bootstrap.php';
$out = dirname(__DIR__) . '/capture';
if (!is_dir($out)) mkdir($out, 0777, true);
$model = ModelId3::active();
if (!$model) throw new RuntimeException('An active model is required to verify the example.');
$domains = Atribut::domains();
$schools = Sekolah::all();
$rule = Discretizer::fromDb();
$errors = [];
$form = [
    'nama' => 'Siswa Contoh', 'sekolah' => 'MAN', 'peminatan' => 'Umum',
    'nilai' => ['rata_agama' => '93', 'nilai_barab' => '92', 'rata_bahasa' => '93', 'nilai_mtk' => '96', 'rata_mipa' => '97', 'rata_ips' => '90'],
    'manual' => [],
];
$form['sekolah'] = in_array('MAN', $domains['Sekolah_Asal'], true) ? 'MAN' : $domains['Sekolah_Asal'][0];
$attributes = PrediksiService::toAttributes($form, $rule);
$result = Id3::predict($model['tree'], $attributes);
$attributeList = Atribut::all();
$prediction = [
    'nama' => 'Siswa Contoh', 'hasil' => $result['kelas'], 'keyakinan' => $result['keyakinan'],
    'distribusi_json' => $result['distribusi'], 'jalur_json' => $result['jalur'],
    'sumber' => 'form', 'created_at' => '2026-10-17 00:00:00', 'label_asli' => null,
    'input_json' => ['atribut' => $attributes, 'nilai' => $form['nilai']],
    'model_nama' => $model['nama'], 'model_aktif' => true, 'pembuat' => 'Demo',
];
$prodi = PrediksiService::PRODI[$prediction['hasil']] ?? [];
foreach (['form', 'review', 'detail'] as $view) {
    ob_start();
    require $root . '/views/admin/prediksi/' . $view . '.php';
    $body = ob_get_clean();
    $html = '<!doctype html><html lang="id"><head><meta charset="utf-8"><base href="http://web-id3.test/"><link rel="stylesheet" href="assets/css/tokens.css"><link rel="stylesheet" href="assets/css/admin.css"><style>body{padding:32px;background:#f5f3ec}body>main{max-width:960px;margin:auto}.demo-label{font:600 14px sans-serif;color:#071d31;padding:0 0 16px}</style></head><body><main><p class="demo-label">DEMONSTRASI · DATA CONTOH</p>' . $body . '</main></body></html>';
    file_put_contents($out . '/' . $view . '.html', $html);
}
file_put_contents($out . '/example.json', json_encode(['form' => $form, 'attributes' => $attributes, 'result' => $result, 'prodi' => $prodi], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo json_encode(['hasil' => $result['kelas'], 'jalur' => $result['jalur'], 'attributes' => $attributes], JSON_UNESCAPED_UNICODE), PHP_EOL;
