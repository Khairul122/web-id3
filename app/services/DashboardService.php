<?php
declare(strict_types=1);

final class DashboardService
{
    public static function summary(): array
    {
        $counts = Siswa::counts();
        $model = ModelId3::active();
        $evaluation = $model ? Evaluasi::latestForModel((int) $model['id']) : null;
        $predictions = Prediksi::countBySource();
        return [
            'counts' => $counts,
            'bySchool' => Siswa::labelBySchool(),
            'model' => $model,
            'evaluation' => $evaluation,
            'recent' => Prediksi::recent(6),
            'activeAttributes' => count(Atribut::activeCodes()),
            'steps' => [
                ['Import data siswa', (int) $counts['total'] > 0, 'siswa/import'],
                ['Latih model ID3 dan aktifkan', $model !== null, 'training/form'],
                ['Uji model dengan k-fold', $evaluation !== null, 'pengujian/form'],
                ['Prediksi siswa tanpa label', ($predictions['massal'] ?? 0) > 0, 'prediksi/massal'],
            ],
        ];
    }
}
