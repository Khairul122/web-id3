<?php
declare(strict_types=1);

final class LaporanController extends Controller
{
    private const PRINTS = ['siswa', 'model', 'evaluasi', 'prediksi'];

    public function index(): void
    {
        $this->render('laporan/index', [
            'models' => ModelId3::all(),
            'evaluations' => Evaluasi::all(),
            'schools' => Sekolah::all(),
            'pageTitle' => 'Laporan', 'active' => 'laporan', 'crumbs' => ['Laporan' => null],
        ]);
    }

    /** Halaman siap cetak A4. ?jenis=siswa|model|evaluasi|prediksi */
    public function cetak(): void
    {
        $type = query('jenis');
        if (!in_array($type, self::PRINTS, true)) {
            $this->notFound('laporan/', 'Jenis laporan tidak tersedia.');
        }
        $data = match ($type) {
            'siswa' => ['rows' => Siswa::export(['sekolah_id' => query_int('sekolah_id'), 'label' => query('label')]), 'attributes' => Atribut::all()],
            'model' => $this->modelData(),
            'evaluasi' => $this->evaluationData(),
            'prediksi' => ['rows' => Prediksi::export()],
        };
        $titles = ['siswa' => 'Laporan Data Siswa', 'model' => 'Laporan Model ID3', 'evaluasi' => 'Laporan Pengujian Model', 'prediksi' => 'Laporan Hasil Prediksi'];
        $this->render('laporan/cetak-' . $type, $data + [
            'printedAt' => wib(gmdate('Y-m-d H:i:s')),
            'pageTitle' => $titles[$type], 'active' => 'laporan', 'crumbs' => ['Laporan' => 'laporan/', $titles[$type] => null],
        ]);
    }

    private function modelData(): array
    {
        $model = ModelId3::find(query_int('id')) ?? $this->notFound('laporan/', 'Model tidak tersedia.');
        return ['model' => $model, 'steps' => ModelId3::steps((int) $model['id']), 'rules' => ModelId3::rules((int) $model['id'])];
    }

    private function evaluationData(): array
    {
        $evaluation = Evaluasi::find(query_int('id')) ?? $this->notFound('laporan/', 'Hasil pengujian tidak tersedia.');
        [$classes, $max] = EvaluasiService::matrixView($evaluation['confusion_json']);
        return ['evaluation' => $evaluation, 'matrix' => $evaluation['confusion_json'], 'classes' => $classes, 'max' => $max];
    }

    /** Unduh CSV. ?jenis=siswa|prediksi. Pemisah titik koma agar langsung terbaca Excel berbahasa Indonesia. */
    public function csv(): void
    {
        $type = query('jenis');
        if ($type === 'siswa') {
            $codes = array_column(Atribut::all(), 'kode');
            $header = ['Kode', 'Sekolah', 'Kelas', 'Nama', 'NISN', 'Peminatan', ...$codes, 'Label'];
            $rows = array_map(static fn (array $r): array => [
                $r['kode_siswa'], $r['sekolah_kode'], $r['kelas'], $r['nama'], $r['nisn'] ?? '', $r['peminatan'],
                ...array_map(static fn (string $c): string => $r['atribut'][$c] ?? '', $codes), $r['label'] ?? '',
            ], Siswa::export([]));
        } elseif ($type === 'prediksi') {
            $header = ['ID', 'Waktu (WIB)', 'Sumber', 'Kode siswa', 'Nama', 'Model', 'Hasil', 'Keyakinan'];
            $rows = array_map(static fn (array $r): array => [
                $r['id'], wib($r['created_at']), $r['sumber'], $r['kode_siswa'] ?? '', $r['nama'] ?? '', $r['model_nama'], $r['hasil'], number_format((float) $r['keyakinan'] * 100, 1, ',', ''),
            ], Prediksi::export());
        } else {
            $this->notFound('laporan/', 'Jenis CSV tidak tersedia.');
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="siminat-' . $type . '-' . gmdate('Ymd') . '.csv"');
        $out = fopen('php://output', 'wb');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $header, ';', '"', '');
        foreach ($rows as $row) {
            // Cegah formula injection saat dibuka di Excel.
            fputcsv($out, array_map(static fn ($v): string => preg_match('/^[=+\-@]/', (string) $v) ? "'" . $v : (string) $v, $row), ';', '"', '');
        }
        fclose($out);
    }
}
