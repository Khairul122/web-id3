<?php
declare(strict_types=1);

/**
 * Gambar pohon keputusan sebagai SVG (atas ke bawah). Daun diletakkan berurutan dari kiri,
 * node atribut di tengah anak-anaknya. Cabang tanpa data tidak digambar (diprediksi dengan mayoritas node),
 * agar gambar tetap muat; keterangan di bawah gambar menyebutkannya.
 */
final class TreeImage
{
    // SVG dipakai sebagai file gambar terpisah sehingga tidak bisa membaca tokens.css; nilai di bawah salinan token itu.
    private const COLORS = [
        'ink' => '#172c3e', 'muted' => '#526170', 'line' => '#89949d', 'paper' => '#fffdf8', 'navy' => '#071d31', 'gold' => '#f8c454',
        'Saintek' => ['#e3edf7', '#1f5f9e'], 'Soshum' => ['#fbeedd', '#8a4b08'], 'Campuran' => ['#f0e8f6', '#6b3c8f'], 
    ];
    private const BOX_H = 46;
    private const LEVEL = 124;
    private const GAP = 18;
    private const TOP = 92;
    private const SIDE = 32;
    private const CHAR = 6.9;

    private int $maxDepth = 0;

    public static function svg(array $tree, string $title, string $subtitle): string
    {
        return (new self())->render($tree, $title, $subtitle);
    }

    private function render(array $tree, string $title, string $subtitle): string
    {
        $root = $this->place($this->measure($tree, null, 0), self::SIDE);
        $width = (int) ceil(max($root['span'], self::CHAR * mb_strlen($title) * 1.4, 1340) + self::SIDE * 2);
        $height = self::TOP + ($this->maxDepth + 1) * self::LEVEL - (self::LEVEL - self::BOX_H) + 64;
        $c = self::COLORS;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" font-family="Inter, Arial, Helvetica, sans-serif" role="img" aria-label="' . $this->x($title) . '">'
            . '<rect width="100%" height="100%" fill="' . $c['paper'] . '"/>'
            . '<text x="' . self::SIDE . '" y="36" font-size="20" font-weight="700" font-family="Georgia, \'Times New Roman\', serif" fill="' . $c['navy'] . '">' . $this->x($title) . '</text>'
            . '<text x="' . self::SIDE . '" y="58" font-size="12" fill="' . $c['muted'] . '">' . $this->x($subtitle) . '</text>';
        $svg .= $this->edges($root) . $this->nodes($root) . $this->legend($height);
        return $svg . '</svg>';
    }

    /** Bangun node tata letak beserta lebar ruang yang dibutuhkan subtree-nya (belum diberi posisi x). */
    private function measure(array $node, ?string $edge, int $depth): array
    {
        $this->maxDepth = max($this->maxDepth, $depth);
        $leaf = !isset($node['atribut']);
        $lines = $leaf
            ? [$node['kelas'], 'n = ' . $node['jumlah'] . ' · ' . $this->share($node)]
            : [$node['atribut'], 'n = ' . $node['jumlah'] . ' · E = ' . num($node['entropy'], 4)];
        $children = [];
        foreach ($leaf ? [] : $node['cabang'] as $value => $child) {
            if (empty($child['kosong'])) {
                $children[] = $this->measure($child, (string) $value, $depth + 1);
            }
        }
        $w = $this->boxWidth($lines);
        $own = max($w, $edge === null ? 0 : mb_strlen($edge) * self::CHAR + 16) + self::GAP;
        return [
            'y' => self::TOP + $depth * self::LEVEL, 'w' => $w, 'lines' => $lines, 'edge' => $edge,
            'kind' => $leaf ? $node['kelas'] : 'node', 'children' => $children,
            'span' => max($own, array_sum(array_column($children, 'span'))),
        ];
    }

    /** Beri posisi x: anak-anak dijajarkan di tengah ruang subtree, induk di tengah anak pertama dan terakhir. */
    private function place(array $node, float $left): array
    {
        if (!$node['children']) {
            return $node + ['x' => $left + $node['span'] / 2];
        }
        $cursor = $left + ($node['span'] - array_sum(array_column($node['children'], 'span'))) / 2;
        foreach ($node['children'] as $i => $child) {
            $node['children'][$i] = $this->place($child, $cursor);
            $cursor += $child['span'];
        }
        return $node + ['x' => ($node['children'][0]['x'] + end($node['children'])['x']) / 2];
    }

    private function boxWidth(array $lines): float
    {
        return max(array_map(static fn (string $l): float => mb_strlen($l) * self::CHAR, $lines)) + 28;
    }

    private function share(array $node): string
    {
        return $node['jumlah'] ? pct($node['distribusi'][$node['kelas']] / $node['jumlah'], 0) : '0%';
    }

    private function edges(array $node): string
    {
        $out = '';
        $bottom = $node['y'] + self::BOX_H;
        $mid = $bottom + 22;
        foreach ($node['children'] as $child) {
            $c = self::COLORS;
            $out .= '<path d="M' . round($node['x'], 1) . ' ' . $bottom . 'V' . $mid . 'H' . round($child['x'], 1) . 'V' . $child['y'] . '" fill="none" stroke="' . $c['line'] . '" stroke-width="1.4"/>';
            $label = $this->x($child['edge']);
            $lw = mb_strlen($child['edge']) * self::CHAR + 14;
            $ly = $mid + 30;
            $out .= '<rect x="' . round($child['x'] - $lw / 2, 1) . '" y="' . ($ly - 13) . '" width="' . round($lw, 1) . '" height="20" rx="10" fill="' . $c['paper'] . '" stroke="' . $c['line'] . '" stroke-width="0.8"/>'
                . '<text x="' . round($child['x'], 1) . '" y="' . ($ly + 1) . '" font-size="11.5" text-anchor="middle" fill="' . $c['ink'] . '">' . $label . '</text>';
            $out .= $this->edges($child);
        }
        return $out;
    }

    private function nodes(array $node): string
    {
        $c = self::COLORS;
        [$fill, $stroke] = $node['kind'] === 'node' ? [$c['paper'], $c['navy']] : $c[$node['kind']];
        $x = round($node['x'] - $node['w'] / 2, 1);
        $out = '<rect x="' . $x . '" y="' . $node['y'] . '" width="' . round($node['w'], 1) . '" height="' . self::BOX_H . '" rx="6" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="' . ($node['kind'] === 'node' ? 1.6 : 1.2) . '"' . '' . '/>'
            . '<text x="' . round($node['x'], 1) . '" y="' . ($node['y'] + 19) . '" font-size="13" font-weight="700" text-anchor="middle" fill="' . ($node['kind'] === 'node' ? $c['navy'] : $stroke) . '">' . $this->x($node['lines'][0]) . '</text>'
            . '<text x="' . round($node['x'], 1) . '" y="' . ($node['y'] + 36) . '" font-size="11" text-anchor="middle" fill="' . $c['muted'] . '">' . $this->x($node['lines'][1]) . '</text>';
        foreach ($node['children'] as $child) {
            $out .= $this->nodes($child);
        }
        return $out;
    }

    private function legend(int $height): string
    {
        $c = self::COLORS;
        $y = $height - 26;
        $out = '';
        $x = self::SIDE;
        foreach (['Saintek', 'Soshum', 'Campuran'] as $class) {
            $out .= '<rect x="' . $x . '" y="' . ($y - 10) . '" width="14" height="14" rx="3" fill="' . $c[$class][0] . '" stroke="' . $c[$class][1] . '"/>'
                . '<text x="' . ($x + 20) . '" y="' . ($y + 1) . '" font-size="11.5" fill="' . $c['ink'] . '">' . $class . '</text>';
            $x += 100;
        }
        return $out . '<text x="' . $x . '" y="' . ($y + 1) . '" font-size="11.5" fill="' . $c['muted'] . '">Kotak putih = atribut (n = jumlah data, E = entropy). Label pada garis = nilai atribut. Persentase daun = keyakinan. Cabang tanpa data tidak digambar dan memakai kelas mayoritas node di atasnya.</text>';
    }

    private function x(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8');
    }
}
