<?php
// Variabel: $page, $total, $perPage, $params (query string tanpa page).
$pages = max(1, (int) ceil($total / $perPage));
$from = $total ? ($page - 1) * $perPage + 1 : 0;
$to = min($page * $perPage, $total);
$link = static fn (int $number): string => '?' . http_build_query([...$params, 'page' => $number]);
?>
<div class="pagination">
  <span>Menampilkan <?= $from ?> sampai <?= $to ?> dari <?= $total ?></span>
  <?php if ($pages > 1): ?>
  <nav aria-label="Halaman">
    <?php if ($page > 1): ?><a href="<?= e($link($page - 1)) ?>" aria-label="Halaman sebelumnya">‹</a><?php endif; ?>
    <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
      <?php if ($i === $page): ?><span aria-current="page"><?= $i ?></span><?php else: ?><a href="<?= e($link($i)) ?>"><?= $i ?></a><?php endif; ?>
    <?php endfor; ?>
    <?php if ($page < $pages): ?><a href="<?= e($link($page + 1)) ?>" aria-label="Halaman berikutnya">›</a><?php endif; ?>
  </nav>
  <?php endif; ?>
</div>
