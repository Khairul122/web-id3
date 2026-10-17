<?php
declare(strict_types=1);

/**
 * Gambar pohon keputusan sebagai SVG (atas ke bawah). Daun diletakkan berurutan dari kiri,
 * node atribut di tengah anak-anaknya. Cabang tanpa data digabung satu kotak per node.
 */
final class TreeImage
{
    // SVG dipakai sebagai file gambar terpisah sehingga tidak bisa membaca tokens.css; nilai di bawah salinan token itu.
    private const COLORS = [
        'ink' => '#172c3e', 'muted' => '#526170', 'line' => '#89949d', 'paper' => '#fffdf8', 'navy' => '#071d31', 'gold' => '#f8c454',
        'Saintek' => ['#e3edf7', '#1f5f9e'], 'Soshum' => ['#fbeedd', '#8a4b08'], 'Campuran' => ['#f0e8f6', '#6b3c8f'], 'kosong' => ['#f8f6f0', '#526170'],
    ];
    private const BOX_H = 46;
    private const LEVEL = 124;
    private const GAP = 18;
    private const TOP = 92;
    private const SIDE = 32;
    private const CHAR = 6.9;

    private float $cursor = 0;
    private int $maxDepth = 0;

    public static function svg(array $tree, string $title, string $subtitle): string
    {
        return (new self())->render($tree, $title, $subtitle);
    }

    private function render(array $tree, string $title, string $subtitle): string
    {
        $root = $this->layout($tree, null, 0);
        $width = (int) ceil(max($this->cursor, self::CHAR * mb_strlen($title) * 1.4, 1060) + self::SIDE * 2);
        $height = self::TOP + ($this->maxDepth + 1) * self::LEVEL - (self::LEVEL - self::BOX_H) + 64;
        $c = self::COLORS;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" font-family="Inter, Arial, Helvetica, sans-serif" role="img" aria-label="' . $this->x($title) . '">'
            . '<rect width="100%" height="100%" fill="' . $c['paper'] . '"/>'
            . '<text x="' . self::SIDE . '" y="36" font-size="20" font-weight="700" font-family="Georgia, \'Times New Roman\', serif" fill="' . $c['navy'] . '">' . $this->x($title) . '</text>'
            . '<text x="' . self::SIDE . '" y="58" font-size="12" fill="' . $c['muted'] . '">' . $this->x($subtitle) . '</text>';
        $svg .= $this->edges($root) . $this->nodes($root) . $this->legend($height);
        return $svg . '</svg>';
    }

    /** Tentukan posisi setiap kotak. Kembalikan node tata letak: x (tengah), y, w, label, anak. */
    private function layout(array $node, ?string $edge, int $depth): array
    {
        $this->maxDepth = max($this->maxDepth, $depth);
        $y = self::TOP + $depth * self::LEVEL;
        if (!isset($node['atribut'])) {
            $lines = [$node['kelas'], 'n = ' . $node['jumlah'] . ' · ' . $this->share($node)];
            return $this->leafBox($lines, $edge, $y, $node['kelas']);
        }
        $children = [];
        $empty = [];
        foreach ($node['cabang'] as $value => $child) {
            if (!empty($child['kosong'])) {
                $empty[] = (string) $value;
            } else {
                $children[] = $this->layout($child, (string) $value, $depth + 1);
            }
        }
        if ($empty) {
            $this->maxDepth = max($this->maxDepth, $depth + 1);
            $children[] = $this->leafBox(['Tanpa data', '→ ' . Id3::majority($node['distribusi'])], implode(', ', $empty), $y + self::LEVEL, 'kosong');
        }
        $lines = [$node['atribut'], 'n = ' . $node['jumlah'] . ' · E = ' . num($node['entropy'], 4)];
        $x = ($children[0]['x'] + end($children)['x']) / 2;
        return ['x' => $x, 'y' => $y, 'w' => $this->boxWidth($lines), 'lines' => $lines, 'edge' => $edge, 'kind' => 'node', 'children' => $children];
    }

    private function leafBox(array $lines, ?string $edge, int $y, string $kind): array
    {
        $w = $this->boxWidth($lines);
        $slot = max($w, $edge === null ? 0 : mb_strlen($edge) * self::CHAR + 16) + self::GAP;
        $x = self::SIDE + $this->cursor + $slot / 2;
        $this->cursor += $slot;
        return ['x' => $x, 'y' => $y, 'w' => $w, 'lines' => $lines, 'edge' => $edge, 'kind' => $kind, 'children' => []];
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
        $out = '<rect x="' . $x . '" y="' . $node['y'] . '" width="' . round($node['w'], 1) . '" height="' . self::BOX_H . '" rx="6" fill="' . $fill . '" stroke="' . $stroke . '" stroke-width="' . ($node['kind'] === 'node' ? 1.6 : 1.2) . '"' . ($node['kind'] === 'kosong' ? ' stroke-dasharray="4 3"' : '') . '/>'
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
        return $out . '<text x="' . $x . '" y="' . ($y + 1) . '" font-size="11.5" fill="' . $c['muted'] . '">Kotak putih = atribut (n = jumlah data, E = entropy). Label pada garis = nilai atribut. Persentase daun = keyakinan.</text>';
    }

    private function x(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8');
    }
}
