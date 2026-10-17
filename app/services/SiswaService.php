<?php
declare(strict_types=1);

/** Logika data siswa: validasi form, kategori atribut, dan penyimpanan. */
final class SiswaService
{
    public const EMPTY = [
        'id' => null, 'kode_siswa' => '', 'sekolah_id' => '', 'kelas' => '', 'nama' => '', 'nis' => '', 'nisn' => '', 'jk' => '',
        'peminatan' => '', 'label' => '', 'sumber_label' => '', 'detail_label' => '', 'jurusan_diterima' => '',
        'nilai' => [], 'manual' => [], 'rata_umum' => null,
    ];

    public static function fromRecord(array $siswa): array
    {
        $nilai = [];
        foreach (array_keys(Discretizer::SCORE_LABELS) as $column) {
            $value = $siswa['nilai'][$column] ?? null;
            $nilai[$column] = $value === null ? '' : rtrim(rtrim((string) $value, '0'), '.');
        }
        $manual = $siswa['atribut'];
        unset($manual['Sekolah_Asal'], $manual['Peminatan']);
        return [
            'id' => (int) $siswa['id'],
            'kode_siswa' => $siswa['kode_siswa'], 'sekolah_id' => (string) $siswa['sekolah_id'], 'kelas' => $siswa['kelas'],
            'nama' => $siswa['nama'], 'nis' => $siswa['nis'] ?? '', 'nisn' => $siswa['nisn'] ?? '', 'jk' => $siswa['jk'] ?? '',
            'peminatan' => $siswa['peminatan'], 'label' => $siswa['label'] ?? '', 'sumber_label' => $siswa['sumber_label'] ?? '',
            'detail_label' => $siswa['detail_label'] ?? '', 'jurusan_diterima' => $siswa['jurusan_diterima'] ?? '',
            'nilai' => $nilai, 'manual' => $manual, 'rata_umum' => $siswa['nilai']['rata_umum'] ?? null,
        ];
    }

    /** @return array{0: array, 1: array<string, string>} data form dan pesan error per field */
    public static function fromPost(?int $id, ?string $rataUmum): array
    {
        $text = static fn (string $key): string => trim(input($key));
        $form = [
            'id' => $id, 'kode_siswa' => strtoupper($text('kode_siswa')), 'sekolah_id' => $text('sekolah_id'), 'kelas' => $text('kelas'),
            'nama' => $text('nama'), 'nis' => $text('nis'), 'nisn' => $text('nisn'), 'jk' => $text('jk'), 'peminatan' => $text('peminatan'),
            'label' => $text('label'), 'sumber_label' => $text('sumber_label'), 'detail_label' => $text('detail_label'),
            'jurusan_diterima' => $text('jurusan_diterima'), 'nilai' => [], 'manual' => [], 'rata_umum' => $rataUmum,
        ];
        foreach (array_keys(Discretizer::SCORE_LABELS) as $column) {
            $form['nilai'][$column] = str_replace(',', '.', $text($column));
        }
        foreach ([...array_keys(Discretizer::SOURCES), 'Kecenderungan'] as $attribute) {
            $form['manual'][$attribute] = $text('kat_' . $attribute);
        }

        $errors = [];
        $domains = Atribut::domains();
        if (!preg_match('/^[A-Z0-9-]{3,20}$/D', $form['kode_siswa'])) {
            $errors['kode_siswa'] = 'Gunakan 3 sampai 20 huruf kapital, angka, atau tanda hubung. Contoh: MAN-K1-01.';
        } elseif (Siswa::kodeExists($form['kode_siswa'], $id)) {
            $errors['kode_siswa'] = 'Kode siswa sudah dipakai.';
        }
        if (!ctype_digit($form['sekolah_id']) || !Sekolah::find((int) $form['sekolah_id'])) {
            $errors['sekolah_id'] = 'Pilih sekolah.';
        }
        if ($form['kelas'] === '' || mb_strlen($form['kelas']) > 10) {
            $errors['kelas'] = 'Kelas wajib diisi, maksimal 10 karakter. Contoh: XII.1.';
        }
        if ($form['nama'] === '' || mb_strlen($form['nama']) > 150) {
            $errors['nama'] = 'Nama wajib diisi, maksimal 150 karakter.';
        }
        if ($form['nis'] !== '' && !preg_match('/^[0-9.\/-]{1,30}$/D', $form['nis'])) {
            $errors['nis'] = 'NIS hanya berisi angka, titik, garis miring, atau tanda hubung.';
        }
        if ($form['nisn'] !== '' && !preg_match('/^\d{10}$/D', $form['nisn'])) {
            $errors['nisn'] = 'NISN terdiri dari 10 digit angka.';
        }
        if (!in_array($form['jk'], ['', 'L', 'P'], true)) {
            $errors['jk'] = 'Pilih jenis kelamin.';
        }
        if (!in_array($form['peminatan'], $domains['Peminatan'], true)) {
            $errors['peminatan'] = 'Pilih peminatan.';
        }
        if ($form['label'] !== '' && !in_array($form['label'], Id3::CLASSES, true)) {
            $errors['label'] = 'Pilih label yang tersedia.';
        }
        foreach (['sumber_label' => 100, 'detail_label' => 255, 'jurusan_diterima' => 255] as $key => $max) {
            if (mb_strlen($form[$key]) > $max) {
                $errors[$key] = "Maksimal {$max} karakter.";
            }
        }
        foreach ($form['nilai'] as $column => $value) {
            if ($value !== '' && (!is_numeric($value) || (float) $value < 0 || (float) $value > 100)) {
                $errors[$column] = 'Isi angka 0 sampai 100, atau kosongkan.';
            }
        }
        foreach (Discretizer::SOURCES as $attribute => $column) {
            if ($form['nilai'][$column] === '' && !in_array($form['manual'][$attribute], $domains[$attribute], true)) {
                $errors[$column] = 'Isi nilai atau pilih kategori.';
            }
        }
        $tendencyComputed = $form['nilai']['rata_mipa'] !== '' && $form['nilai']['rata_ips'] !== '';
        if (!$tendencyComputed && !in_array($form['manual']['Kecenderungan'], $domains['Kecenderungan'], true)) {
            $errors['Kecenderungan'] = 'Pilih kecenderungan, atau isi rata-rata MIPA dan IPS.';
        }
        return [$form, $errors];
    }

    /** Data siap simpan untuk Siswa::save(), termasuk kategori final semua atribut. */
    public static function toRecord(array $form, Discretizer $discretizer): array
    {
        $scores = array_map(static fn (string $v): ?float => $v === '' ? null : round((float) $v, 2), $form['nilai']);
        $computed = $discretizer->attributes($scores);
        $attributes = ['Sekolah_Asal' => Sekolah::find((int) $form['sekolah_id'])['kode'], 'Peminatan' => $form['peminatan']];
        foreach (Discretizer::SOURCES as $attribute => $column) {
            $attributes[$attribute] = $scores[$column] === null ? $form['manual'][$attribute] : $computed[$attribute];
        }
        $attributes['Kecenderungan'] = $scores['rata_mipa'] !== null && $scores['rata_ips'] !== null ? $computed['Kecenderungan'] : $form['manual']['Kecenderungan'];
        return [
            ...$form,
            'sekolah_id' => (int) $form['sekolah_id'],
            'nilai' => [...$scores, 'rata_umum' => $form['rata_umum']],
            'atribut' => $attributes,
        ];
    }

    /** Simpan draf form yang sudah ditinjau. Mengembalikan id siswa. */
    public static function save(array $form): int
    {
        $record = self::toRecord($form, Discretizer::fromDb());
        return Database::transaction(static fn (): int => Siswa::save($record, $form['id']));
    }
}
