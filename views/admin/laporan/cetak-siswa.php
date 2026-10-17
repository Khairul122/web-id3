<?php require APP_ROOT . '/views/partials/print_head.php'; ?>
<p class="table-caption"><?= count($rows) ?> siswa.</p>
<div class="table-wrap"><table class="table">
  <thead><tr><th>Kode</th><th>Nama</th><th>Kelas</th><?php foreach ($attributes as $attribute): ?><th><?= e($attribute['kode']) ?></th><?php endforeach; ?><th>Label</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $row): ?>
    <tr>
      <td class="nowrap"><?= e($row['kode_siswa']) ?></td><td><?= e($row['nama']) ?></td><td><?= e($row['kelas']) ?></td>
      <?php foreach ($attributes as $attribute): ?><td><?= e($row['atribut'][$attribute['kode']] ?? '-') ?></td><?php endforeach; ?>
      <td><?= e($row['label'] ?? '-') ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table></div>
