<?php
declare(strict_types=1);

// Import Dataset_Bersih_ID3.xlsx lewat CLI. Mengganti seluruh data siswa. Pakai: php scripts/import_dataset.php [file.xlsx]
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require dirname(__DIR__) . '/app/core/bootstrap.php';

$file = $argv[1] ?? APP_ROOT . '/Dataset_Bersih_ID3.xlsx';
$parsed = Importer::parse(Importer::readXlsx($file), Atribut::domains());
if ($parsed['errors']) {
    fwrite(STDERR, "Import dihentikan:\n- " . implode("\n- ", $parsed['errors']) . "\n");
    exit(1);
}
$saved = Importer::store($parsed['rows'], Discretizer::fromDb());
printf("%d siswa tersimpan: %d berlabel, %d tanpa label.\n", $saved, $parsed['summary']['berlabel'], $parsed['summary']['tanpa_label']);
