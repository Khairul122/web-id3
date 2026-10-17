<?php
declare(strict_types=1);

// Helper tampilan untuk view panel admin.

/**
 * Batang bertumpuk SVG (lebar dalam persen, tanpa style inline agar lolos CSP).
 * @param array<string, int> $segments kelas CSS fill-* => jumlah
 */
function stacked_bar(array $segments, int $total, string $label): string
{
    $svg = '<svg viewBox="0 0 100 12" preserveAspectRatio="none" role="img" aria-label="' . e($label) . '">';
    $x = 0.0;
    foreach ($segments as $class => $count) {
        if ($total > 0 && $count > 0) {
            $width = $count / $total * 100;
            $svg .= '<rect class="fill-' . e($class) . '" x="' . round($x, 3) . '" y="0" width="' . round($width, 3) . '" height="12"/>';
            $x += $width;
        }
    }
    return $svg . '</svg>';
}

function legend_dot(string $class): string
{
    return '<svg viewBox="0 0 10 10" aria-hidden="true"><circle class="fill-' . e($class) . '" cx="5" cy="5" r="5"/></svg>';
}

/** Kelas warna heatmap 0 sampai 5 menurut proporsi terhadap nilai terbesar. */
function heat(int $value, int $max): string
{
    return 'heat-' . ($value === 0 || $max === 0 ? 0 : max(1, (int) ceil($value / $max * 5)));
}

function distribution_text(array $distribution): string
{
    $parts = [];
    foreach ($distribution as $class => $count) {
        if ($count > 0) {
            $parts[] = $class . ' ' . $count;
        }
    }
    return $parts ? implode(', ', $parts) : 'tanpa data';
}

/** Pohon keputusan sebagai daftar bersarang. Node atribut bisa dilipat dengan <details>. */
function tree_html(array $node, ?string $edge = null, int $depth = 0, bool $openAll = false): string
{
    $edgeHtml = $edge === null ? '' : '<span class="tree-edge">' . e($edge) . '</span>';
    if (!isset($node['atribut'])) {
        $empty = !empty($node['kosong']);
        return '<li>' . $edgeHtml . '<span class="tree-leaf' . ($empty ? ' tree-leaf--empty' : '') . '">' . badge($node['kelas'])
            . '<small>' . ($empty ? 'cabang tanpa data, mayoritas induk' : e($node['jumlah'] . ' data: ' . distribution_text($node['distribusi']))) . '</small></span></li>';
    }
    $children = '';
    foreach ($node['cabang'] as $value => $child) {
        $children .= tree_html($child, (string) $value, $depth + 1, $openAll);
    }
    return '<li>' . $edgeHtml . '<details' . ($openAll || $depth < 2 ? ' open' : '') . '><summary><span class="tree-node"><span class="tree-node__toggle" aria-hidden="true"></span><strong>'
        . e($node['atribut']) . '</strong><small>' . e($node['jumlah'] . ' data · entropy ' . num($node['entropy'], 4)) . '</small></span></summary><ul>' . $children . '</ul></details></li>';
}

function invalid_attr(array $errors, string $key): string
{
    return isset($errors[$key]) ? 'aria-invalid="true" aria-describedby="' . e($key) . '-error"' : '';
}

function field_error(array $errors, string $key): string
{
    return isset($errors[$key]) ? '<p class="field__error" id="' . e($key) . '-error">' . e($errors[$key]) . '</p>' : '';
}

function text_field(array $form, array $errors, string $key, string $label, int $max, string $extra = ''): string
{
    return '<div class="field"><label for="' . e($key) . '">' . e($label) . '</label><input id="' . e($key) . '" name="' . e($key) . '" maxlength="' . $max . '" value="' . e($form[$key] ?? '') . '" ' . $extra . ' ' . invalid_attr($errors, $key) . '>' . field_error($errors, $key) . '</div>';
}
