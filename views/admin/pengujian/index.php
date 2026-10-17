<div class="page-head">
  <div>
    <h1>Pengujian Model</h1>
    <p>Stratified k-fold cross validation dengan seed tetap <?= Evaluator::SEED ?>. Karena kelas tidak seimbang, baca F1 macro bersama akurasi.</p>
  </div>
  <?php if ($isAdmin): ?><div class="page-actions"><a class="btn btn--accent" href="<?= e(admin_url('pengujian/form')) ?>"><?= icon('chart') ?> Uji Model</a></div><?php endif; ?>
</div>
<?php if (!$evaluations): ?>
  <div class="card empty"><?= icon('chart') ?><strong>Belum ada pengujian</strong>Uji model yang sudah dilatih untuk mendapatkan akurasi dan confusion matrix.</div>
<?php else: ?>
<div class="table-wrap">
  <table class="table table--cards">
    <thead><tr><th>Model</th><th>Skenario</th><th class="num">k</th><th class="num">Data</th><th class="num">Akurasi</th><th class="num">Precision</th><th class="num">Recall</th><th class="num">F1</th><th>Waktu</th><th class="actions">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($evaluations as $row): ?>
      <tr>
        <td class="cell-title"><a href="<?= e(admin_url('pengujian/detail?id=' . $row['id'])) ?>"><?= e($row['model_nama']) ?></a></td>
        <td data-label="Skenario"><?= e(ModelId3::SCENARIOS[$row['skenario']] ?? $row['skenario']) ?></td>
        <td data-label="k" class="num"><?= (int) $row['k'] ?></td>
        <td data-label="Data" class="num"><?= (int) $row['jumlah_data'] ?></td>
        <td data-label="Akurasi" class="num"><?= e(pct($row['akurasi'])) ?></td>
        <td data-label="Precision" class="num"><?= e(pct($row['precision_macro'])) ?></td>
        <td data-label="Recall" class="num"><?= e(pct($row['recall_macro'])) ?></td>
        <td data-label="F1" class="num"><?= e(pct($row['f1_macro'])) ?></td>
        <td data-label="Waktu" class="nowrap"><?= e(wib($row['created_at'])) ?></td>
        <td class="actions">
          <a class="link-btn" href="<?= e(admin_url('pengujian/detail?id=' . $row['id'])) ?>">Detail</a>
          <?php if ($isAdmin): ?>
          <form class="inline-form" method="post" action="<?= e(admin_url('pengujian/hapus')) ?>" data-confirm="Hasil pengujian ini akan dihapus permanen." data-confirm-title="Hapus pengujian?" data-confirm-ok="Ya, hapus" data-confirm-danger>
            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $row['id'] ?>"><button class="link-btn link-btn--danger" type="submit">Hapus</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
