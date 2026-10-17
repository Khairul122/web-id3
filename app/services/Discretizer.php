<?php
declare(strict_types=1);

/** Mengubah nilai rapor menjadi kategori atribut ID3. Batas diambil dari tabel aturan_diskretisasi. */
final class Discretizer
{
    /** Atribut kategori nilai => kolom nilai_rata sumbernya. */
    public const SOURCES = [
        'Kat_Agama' => 'rata_agama',
        'Kat_BArab' => 'nilai_barab',
        'Kat_Bahasa' => 'rata_bahasa',
        'Kat_MTK' => 'nilai_mtk',
        'Kat_MIPA' => 'rata_mipa',
        'Kat_IPS' => 'rata_ips',
    ];

    /** Label kolom nilai untuk form dan tampilan. */
    public const SCORE_LABELS = [
        'rata_agama' => 'Rata-rata Agama',
        'nilai_barab' => 'Nilai Bahasa Arab',
        'rata_bahasa' => 'Rata-rata Bahasa',
        'nilai_mtk' => 'Nilai Matematika',
        'rata_mipa' => 'Rata-rata MIPA',
        'rata_ips' => 'Rata-rata IPS',
    ];

    /** Selisih rata-rata MIPA dan IPS minimal untuk menentukan kecenderungan (Kamus_Data dataset). */
    private const TENDENCY_GAP = 1.0;

    public function __construct(public readonly float $tinggi, public readonly float $sedang)
    {
    }

    public static function fromDb(): self
    {
        $rule = Diskretisasi::current();
        return new self((float) $rule['batas_tinggi'], (float) $rule['batas_sedang']);
    }

    public function category(?float $score): string
    {
        if ($score === null) {
            return 'Tidak Ada';
        }
        return $score >= $this->tinggi ? 'Tinggi' : ($score >= $this->sedang ? 'Sedang' : 'Rendah');
    }

    public function tendency(?float $mipa, ?float $ips): string
    {
        if ($mipa === null || $ips === null) {
            return 'Tidak Terukur';
        }
        if ($mipa - $ips >= self::TENDENCY_GAP) {
            return 'MIPA';
        }
        return $ips - $mipa >= self::TENDENCY_GAP ? 'IPS' : 'Seimbang';
    }

    /**
     * Kategori untuk atribut nilai dan Kecenderungan.
     * @param array<string, float|null> $scores kunci kolom nilai_rata
     * @return array<string, string>
     */
    public function attributes(array $scores): array
    {
        $result = [];
        foreach (self::SOURCES as $attribute => $column) {
            $result[$attribute] = $this->category($scores[$column] ?? null);
        }
        $result['Kecenderungan'] = $this->tendency($scores['rata_mipa'] ?? null, $scores['rata_ips'] ?? null);
        return $result;
    }
}
