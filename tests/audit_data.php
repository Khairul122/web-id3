<?php
declare(strict_types=1);

/**
 * Audit isi database terhadap metode penelitian: jumlah data, domain atribut,
 * kategori hasil diskretisasi, dan kecocokan prediksi tersimpan dengan model aktif.
 * Jalankan: php tests/audit_data.php  (exit 1 bila ada temuan)
 */
require __DIR__ . '/../app/core/bootstrap.php';

$problems = [];
$check = static function (bool $ok, string $message) use (&$problems): void {
    echo ($ok ? 'OK   ' : 'GAGAL') . "  {$message}\n";
    if (!$ok) {
        $problems[] = $message;
    }
};

$counts = Siswa::counts();
$check((int) $counts['total'] === Importer::EXPECTED['total'] && (int) $counts['berlabel'] === Importer::EXPECTED['berlabel'] && (int) $counts['tanpa_label'] === Importer::EXPECTED['tanpa_label'],
    "Jumlah siswa {$counts['total']} ({$counts['berlabel']} berlabel, {$counts['tanpa_label']} tanpa label)");

$badLabels = Database::run('SELECT COUNT(*) FROM siswa WHERE label IS NOT NULL AND label NOT IN (' . implode(',', array_fill(0, count(Id3::CLASSES), '?')) . ')', Id3::CLASSES)->fetchColumn();
$check((int) $badLabels === 0, "Label di luar " . implode('/', Id3::CLASSES) . ": {$badLabels}");

$domains = Atribut::domains();
$discretizer = Discretizer::fromDb();
$students = Database::run('SELECT s.id, s.kode_siswa, s.peminatan, k.kode AS sekolah FROM siswa s JOIN sekolah k ON k.id = s.sekolah_id')->fetchAll();
$scores = array_column(Siswa::scoreRows(), null, 'siswa_id');
$attributes = [];
foreach (Database::run('SELECT sa.siswa_id, a.kode, sa.nilai FROM siswa_atribut sa JOIN atribut a ON a.id = sa.atribut_id')->fetchAll() as $row) {
    $attributes[(int) $row['siswa_id']][$row['kode']] = $row['nilai'];
}
$missing = $outOfDomain = $mismatch = [];
foreach ($students as $student) {
    $own = $attributes[(int) $student['id']] ?? [];
    foreach (array_keys($domains) as $code) {
        if (!isset($own[$code])) {
            $missing[] = "{$student['kode_siswa']}.{$code}";
        } elseif (!in_array($own[$code], $domains[$code], true)) {
            $outOfDomain[] = "{$student['kode_siswa']}.{$code}={$own[$code]}";
        }
    }
    $expected = ['Sekolah_Asal' => $student['sekolah'], 'Peminatan' => $student['peminatan']];
    if (isset($scores[$student['id']])) {
        $row = array_map(static fn ($v): ?float => $v === null ? null : (float) $v, $scores[$student['id']]);
        $expected += $discretizer->attributes($row);
    }
    foreach ($expected as $code => $value) {
        if (isset($own[$code]) && $own[$code] !== $value) {
            $mismatch[] = "{$student['kode_siswa']}.{$code}: tersimpan {$own[$code]}, seharusnya {$value}";
        }
    }
}
$check(!$missing, 'Atribut lengkap per siswa' . ($missing ? ': ' . implode(', ', array_slice($missing, 0, 5)) : ''));
$check(!$outOfDomain, 'Nilai atribut sesuai domain' . ($outOfDomain ? ': ' . implode(', ', array_slice($outOfDomain, 0, 5)) : ''));
$check(!$mismatch, "Kategori sesuai aturan diskretisasi (Tinggi >= {$discretizer->tinggi}, Sedang >= {$discretizer->sedang})" . ($mismatch ? ': ' . implode('; ', array_slice($mismatch, 0, 5)) : ''));

$model = ModelId3::active();
$check($model !== null, 'Ada model aktif' . ($model ? ": {$model['nama']}" : ''));
if ($model) {
    $unknown = array_diff($model['atribut'], array_keys($domains));
    $check(!$unknown, 'Atribut model dikenal di tabel atribut');

    $stale = [];
    $rows = Database::run('SELECT id, siswa_id, hasil FROM prediksi WHERE model_id = ? AND siswa_id IS NOT NULL', [(int) $model['id']])->fetchAll();
    foreach ($rows as $row) {
        $live = Id3::predict($model['tree'], $attributes[(int) $row['siswa_id']] ?? []);
        if ($live['kelas'] !== $row['hasil']) {
            $stale[] = "#{$row['id']} tersimpan {$row['hasil']}, sekarang {$live['kelas']}";
        }
    }
    $check(!$stale, count($rows) . ' prediksi tersimpan cocok dengan model aktif' . ($stale ? ': ' . implode('; ', array_slice($stale, 0, 5)) : ''));

    $unpredicted = Siswa::countUnpredicted((int) $model['id']);
    $check($unpredicted === 0, "Siswa tanpa label belum diprediksi model aktif: {$unpredicted}");

    $orphans = (int) Database::run('SELECT COUNT(*) FROM prediksi p LEFT JOIN model_id3 m ON m.id = p.model_id WHERE m.id IS NULL')->fetchColumn();
    $check($orphans === 0, "Prediksi tanpa model: {$orphans}");
}

$unlinked = (int) Database::run("SELECT COUNT(*) FROM users u LEFT JOIN siswa s ON s.kode_siswa = u.kode_siswa WHERE u.role = 'siswa' AND s.id IS NULL")->fetchColumn();
$check($unlinked === 0, "Akun siswa tanpa data siswa: {$unlinked}");

echo $problems ? "\n" . count($problems) . " temuan.\n" : "\nData sesuai metode.\n";
exit($problems ? 1 : 0);
