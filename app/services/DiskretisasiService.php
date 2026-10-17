<?php
declare(strict_types=1);

final class DiskretisasiService
{
    /** @return array{0: array, 1: array<string, string>} batas (float) dan error */
    public static function fromPost(): array
    {
        $form = ['batas_tinggi' => trim(input('batas_tinggi')), 'batas_sedang' => trim(input('batas_sedang'))];
        $errors = [];
        $values = [];
        foreach ($form as $key => $value) {
            $value = str_replace(',', '.', $value);
            if (!is_numeric($value) || (float) $value <= 0 || (float) $value > 100) {
                $errors[$key] = 'Isi angka lebih dari 0 sampai 100.';
            }
            $values[$key] = round((float) $value, 2);
        }
        if (!$errors && $values['batas_sedang'] >= $values['batas_tinggi']) {
            $errors['batas_sedang'] = 'Batas Sedang harus lebih kecil dari batas Tinggi.';
        }
        return [$form, $errors, $values];
    }

    /** Simpan batas baru lalu hitung ulang kategori siswa yang memiliki nilai angka. Mengembalikan jumlah siswa terdampak. */
    public static function apply(float $tinggi, float $sedang): int
    {
        $discretizer = new Discretizer($tinggi, $sedang);
        return Database::transaction(static function () use ($tinggi, $sedang, $discretizer): int {
            Diskretisasi::save($tinggi, $sedang);
            $rows = Siswa::scoreRows();
            foreach ($rows as $row) {
                $scores = array_map(static fn ($v): ?float => $v === null ? null : (float) $v, $row);
                Siswa::saveAttributes((int) $row['siswa_id'], $discretizer->attributes($scores));
            }
            return count($rows);
        });
    }
}
