<?php
declare(strict_types=1);

/** Cek Rekomendasi dari form dan prediksi massal siswa tanpa label memakai model aktif. */
final class PrediksiService
{
    public const EMPTY = ['nama' => '', 'sekolah' => '', 'peminatan' => '', 'nilai' => [], 'manual' => []];

    /** Contoh prodi per rumpun untuk kartu hasil, diambil dari Mapping_Label dataset. */
    public const PRODI = [
        'Saintek' => ['Pendidikan Matematika', 'Pendidikan Biologi', 'Teknik Informatika', 'Teknologi Informasi', 'Agroekoteknologi'],
        'Soshum' => ['Pendidikan Agama Islam', 'Hukum Keluarga Islam', 'Ekonomi Syariah', 'Pendidikan Bahasa Arab', 'Kewirausahaan'],
        'Campuran' => ['Gabungan pilihan Saintek dan Soshum; diskusikan dengan guru BK'],
    ];

    /** @return array{0: array, 1: array<string, string>} */
    public static function fromPost(?array $siswa = null): array
    {
        $form = ['nama' => trim(input('nama')), 'sekolah' => input('sekolah'), 'peminatan' => input('peminatan'), 'nilai' => [], 'manual' => []];
        if ($siswa !== null) {
            $form = self::withSiswaIdentity($form, $siswa);
        }
        foreach (array_keys(Discretizer::SCORE_LABELS) as $column) {
            $form['nilai'][$column] = str_replace(',', '.', trim(input($column)));
        }
        foreach ([...array_keys(Discretizer::SOURCES), 'Kecenderungan'] as $attribute) {
            $form['manual'][$attribute] = input('kat_' . $attribute);
        }
        $domains = Atribut::domains();
        $errors = [];
        if (mb_strlen($form['nama']) > 150) {
            $errors['nama'] = 'Nama maksimal 150 karakter.';
        }
        if (!in_array($form['sekolah'], $domains['Sekolah_Asal'], true)) {
            $errors['sekolah'] = 'Pilih sekolah.';
        }
        if (!in_array($form['peminatan'], $domains['Peminatan'], true)) {
            $errors['peminatan'] = 'Pilih peminatan.';
        }
        foreach (Discretizer::SOURCES as $attribute => $column) {
            $value = $form['nilai'][$column];
            if ($value !== '' && (!is_numeric($value) || (float) $value < 0 || (float) $value > 100)) {
                $errors[$column] = 'Isi angka 0 sampai 100, atau kosongkan.';
            } elseif ($value === '' && !in_array($form['manual'][$attribute], $domains[$attribute], true)) {
                $errors[$column] = 'Isi nilai atau pilih kategori.';
            }
        }
        if (($form['nilai']['rata_mipa'] === '' || $form['nilai']['rata_ips'] === '') && !in_array($form['manual']['Kecenderungan'], $domains['Kecenderungan'], true)) {
            $errors['Kecenderungan'] = 'Pilih kecenderungan, atau isi rata-rata MIPA dan IPS.';
        }
        return [$form, $errors];
    }

    public static function withSiswaIdentity(array $form, array $siswa): array
    {
        return array_replace($form, ['nama' => $siswa['nama'], 'sekolah' => $siswa['sekolah_kode'], 'peminatan' => $siswa['peminatan']]);
    }

    /** Isi awal nilai rapor dari data siswa. Nilai kosong memakai kategori tersimpan sebagai pilihan manual. */
    public static function withSiswaScores(array $form, array $siswa): array
    {
        foreach (array_keys(Discretizer::SCORE_LABELS) as $column) {
            $form['nilai'][$column] = (string) ($siswa['nilai'][$column] ?? '');
        }
        foreach ([...array_keys(Discretizer::SOURCES), 'Kecenderungan'] as $attribute) {
            $form['manual'][$attribute] = $siswa['atribut'][$attribute] ?? '';
        }
        return $form;
    }

    /** Ubah isian form menjadi nilai atribut ID3. */
    public static function toAttributes(array $form, Discretizer $discretizer): array
    {
        $scores = array_map(static fn (string $v): ?float => $v === '' ? null : (float) $v, $form['nilai']);
        $computed = $discretizer->attributes($scores);
        $attributes = ['Sekolah_Asal' => $form['sekolah'], 'Peminatan' => $form['peminatan']];
        foreach (Discretizer::SOURCES as $attribute => $column) {
            $attributes[$attribute] = $scores[$column] === null ? $form['manual'][$attribute] : $computed[$attribute];
        }
        $attributes['Kecenderungan'] = $scores['rata_mipa'] !== null && $scores['rata_ips'] !== null ? $computed['Kecenderungan'] : $form['manual']['Kecenderungan'];
        return $attributes;
    }

    /** Prediksi dari form lalu simpan ke riwayat. Mengembalikan id prediksi. */
    public static function predictForm(array $model, array $form, int $userId, ?int $siswaId = null): int
    {
        $attributes = self::toAttributes($form, Discretizer::fromDb());
        $input = ['atribut' => $attributes, 'nilai' => array_filter($form['nilai'], static fn (string $v): bool => $v !== '')];
        return Prediksi::create((int) $model['id'], 'form', $siswaId, $form['nama'] !== '' ? $form['nama'] : null, $input, Id3::predict($model['tree'], $attributes), $userId);
    }

    /** Prediksi massal siswa tanpa label, atau semua siswa bila $includeLabeled. Hasil massal lama dari model yang sama diganti. */
    public static function runBatch(array $model, int $userId, bool $includeLabeled = false): array
    {
        $students = Siswa::forBatch($includeLabeled);
        return Database::transaction(static function () use ($model, $students, $userId): array {
            Prediksi::deleteBatch((int) $model['id']);
            $summary = array_fill_keys(Id3::CLASSES, 0) + ['berlabel' => 0, 'sesuai' => 0];
            foreach ($students as $student) {
                $result = Id3::predict($model['tree'], $student['atribut']);
                Prediksi::create((int) $model['id'], 'massal', (int) $student['id'], null, ['atribut' => $student['atribut']], $result, $userId);
                $summary[$result['kelas']]++;
                if ($student['label'] !== null) {
                    $summary['berlabel']++;
                    $summary['sesuai'] += (int) ($student['label'] === $result['kelas']);
                }
            }
            return $summary;
        });
    }
}
