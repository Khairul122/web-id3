<?php
declare(strict_types=1);

// Uji mesin ID3 terhadap contoh play-tennis (Quinlan 1986) dan k-fold. Jalankan: php tests/id3_manual_test.php
require dirname(__DIR__) . '/app/services/Id3.php';
require dirname(__DIR__) . '/app/services/Evaluator.php';

$data = [
    ['Sunny', 'Hot', 'High', 'Weak', 'No'], ['Sunny', 'Hot', 'High', 'Strong', 'No'], ['Overcast', 'Hot', 'High', 'Weak', 'Yes'],
    ['Rain', 'Mild', 'High', 'Weak', 'Yes'], ['Rain', 'Cool', 'Normal', 'Weak', 'Yes'], ['Rain', 'Cool', 'Normal', 'Strong', 'No'],
    ['Overcast', 'Cool', 'Normal', 'Strong', 'Yes'], ['Sunny', 'Mild', 'High', 'Weak', 'No'], ['Sunny', 'Cool', 'Normal', 'Weak', 'Yes'],
    ['Rain', 'Mild', 'Normal', 'Weak', 'Yes'], ['Sunny', 'Mild', 'Normal', 'Strong', 'Yes'], ['Overcast', 'Mild', 'High', 'Strong', 'Yes'],
    ['Overcast', 'Hot', 'Normal', 'Weak', 'Yes'], ['Rain', 'Mild', 'High', 'Strong', 'No'],
];
$attributes = ['Outlook', 'Temperature', 'Humidity', 'Wind'];
$rows = array_map(static fn (array $r): array => ['x' => array_combine($attributes, array_slice($r, 0, 4)), 'y' => $r[4]], $data);
$domains = ['Outlook' => ['Sunny', 'Overcast', 'Rain'], 'Temperature' => ['Hot', 'Mild', 'Cool'], 'Humidity' => ['High', 'Normal'], 'Wind' => ['Weak', 'Strong']];
$id3 = new Id3($attributes, $domains, 1, null, ['Yes', 'No']);

function check(bool $condition, string $message): void
{
    if (!$condition) {
        fwrite(STDERR, "GAGAL: {$message}\n");
        exit(1);
    }
    echo "OK   {$message}\n";
}

check(abs(Id3::entropy(['Yes' => 9, 'No' => 5]) - 0.9403) < 0.00005, 'Entropy akar = 0,9403');
check(Id3::entropy(['Yes' => 4, 'No' => 0]) === 0.0, '0 * log2(0) dianggap 0');
$expected = ['Outlook' => 0.2467, 'Temperature' => 0.0292, 'Humidity' => 0.1518, 'Wind' => 0.0481];
foreach ($expected as $attribute => $gain) {
    check(abs($id3->gain($rows, $attribute)['gain'] - $gain) < 0.00005, "Gain {$attribute} = {$gain}");
}

$result = $id3->train($rows);
$tree = $result['tree'];
check($tree['atribut'] === 'Outlook', 'Akar pohon = Outlook');
check($tree['cabang']['Overcast']['kelas'] === 'Yes', 'Overcast langsung Yes');
check($tree['cabang']['Sunny']['atribut'] === 'Humidity', 'Cabang Sunny dipecah Humidity');
check($tree['cabang']['Rain']['atribut'] === 'Wind', 'Cabang Rain dipecah Wind');
check($result['steps'][0]['node_path'] === 'Akar' && count($result['steps'][0]['detail_gain']) === 4, 'Langkah akar menyimpan gain semua atribut');
check(count(Id3::rules($tree)) === 5, 'Lima aturan IF-THEN');
check(Id3::stats($tree) === ['node' => 8, 'daun' => 5, 'kedalaman' => 2], 'Statistik pohon 8 node, 5 daun, kedalaman 2');

foreach ($rows as $row) {
    check(Id3::predict($tree, $row['x'])['kelas'] === $row['y'], 'Prediksi data latih ' . implode('/', $row['x'])) ;
}
$unknown = Id3::predict($tree, ['Outlook' => 'Snow']);
check($unknown['kelas'] === 'Yes' && $unknown['jalur'][0]['dikenal'] === false, 'Nilai tak dikenal memakai mayoritas node terakhir');

// Gain seri: atribut dengan urutan lebih kecil dipilih.
$tie = [['x' => ['A' => '1', 'B' => '1'], 'y' => 'Yes'], ['x' => ['A' => '2', 'B' => '2'], 'y' => 'No']];
$tieTree = (new Id3(['B', 'A'], ['A' => ['1', '2'], 'B' => ['1', '2']], 1, null, ['Yes', 'No']))->train($tie)['tree'];
check($tieTree['atribut'] === 'B', 'Gain seri memilih atribut urutan pertama');

// Cabang tanpa data menjadi daun mayoritas induk.
$sparse = (new Id3(['A'], ['A' => ['1', '2', '3']], 1, null, ['Yes', 'No']))->train([...$tie, ['x' => ['A' => '1', 'B' => '1'], 'y' => 'Yes']])['tree'];
check(!empty($sparse['cabang']['3']['kosong']) && $sparse['cabang']['3']['kelas'] === 'Yes', 'Cabang kosong = mayoritas induk');

// Gain 0 di akar (pola XOR) tetap dipecah, sesuai kriteria berhenti proposal.
$xor = [];
foreach ([['0', '0', 'No'], ['0', '1', 'Yes'], ['1', '0', 'Yes'], ['1', '1', 'No']] as [$a, $b, $y]) {
    $xor[] = ['x' => ['A' => $a, 'B' => $b], 'y' => $y];
}
$xorResult = (new Id3(['A', 'B'], ['A' => ['0', '1'], 'B' => ['0', '1']], 1, null, ['Yes', 'No']))->train($xor);
check($xorResult['steps'][0]['detail_gain'][0]['gain'] == 0.0 && $xorResult['tree']['atribut'] === 'A', 'Gain 0 tetap dipecah (XOR)');
check(count(array_filter($xor, static fn (array $r): bool => Id3::predict($xorResult['tree'], $r['x'])['kelas'] === $r['y'])) === 4, 'Pohon XOR mengklasifikasi semua data latih');

// Data dengan atribut identik tetapi kelas berbeda: berhenti saat atribut habis, daun = mayoritas.
$same = [['x' => ['A' => '1'], 'y' => 'Yes'], ['x' => ['A' => '1'], 'y' => 'Yes'], ['x' => ['A' => '1'], 'y' => 'No']];
$sameTree = (new Id3(['A'], ['A' => ['1', '2']], 1, null, ['Yes', 'No']))->train($same)['tree'];
check($sameTree['cabang']['1']['kelas'] === 'Yes' && !isset($sameTree['cabang']['1']['atribut']), 'Atribut habis menghasilkan daun mayoritas');

// K-fold: stratifikasi, seed tetap, jumlah prediksi = jumlah data.
$train = static fn (array $training): array => (new Id3($attributes, $domains, 1, null, ['Yes', 'No']))->train($training)['tree'];
$first = Evaluator::crossValidate($rows, 7, $train, ['Yes', 'No']);
$second = Evaluator::crossValidate($rows, 7, $train, ['Yes', 'No']);
check($first['jumlah'] === 14 && array_sum(array_map('array_sum', $first['confusion'])) === 14, 'Confusion matrix memuat 14 prediksi');
check($first === $second, 'Hasil k-fold sama saat diulang dengan seed 42');
foreach (Evaluator::folds($rows, 7) as $fold) {
    check(count($fold) === 2, 'Fold berisi 2 data');
}
check(Evaluator::metrics(['A' => ['A' => 5, 'B' => 0], 'B' => ['A' => 0, 'B' => 5]])['f1'] === 1.0, 'Metrik sempurna = 1');

echo "\nSemua pengujian ID3 lulus.\n";
