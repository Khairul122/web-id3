<div class="page-head">
  <div>
    <h1>Training Model</h1>
    <p>Riwayat model pohon keputusan ID3. Hanya satu model yang aktif dan dipakai untuk prediksi.</p>
  </div>
  <?php if ($isAdmin): ?><div class="page-actions"><a class="btn btn--accent" href="<?= e(admin_url('training/form')) ?>"><?= icon('play') ?> Latih Model Baru</a></div><?php endif; ?>
</div>
<?php if (!$models): ?>
  <div class="card empty"><?= icon('branch') ?><strong>Belum ada model</strong>Latih model pertama dari data siswa berlabel.<?php if ($isAdmin): ?><br><a class="btn" href="<?= e(admin_url('training/form')) ?>"><?= icon('play') ?> Latih Model</a><?php endif; ?></div>
<?php else: ?>
<div class="table-wrap">
  <table class="table table--cards">
    <thead><tr><th>Nama model</th><th>Skenario</th><th class="num">Data</th><th class="num">Node / daun</th><th class="num">Akurasi latih</th><th class="num">Akurasi uji</th><th>Dibuat</th><th class="actions">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($models as $model): ?>
      <tr>
        <td class="cell-title"><a href="<?= e(admin_url('training/detail?id=' . $model['id'])) ?>"><?= e($model['nama']) ?></a> <?php if ($model['aktif']): ?><span class="badge badge--aktif">Aktif</span><?php endif; ?></td>
        <td data-label="Skenario"><?= e(ModelId3::SCENARIOS[$model['skenario']] ?? $model['skenario']) ?></td>
        <td data-label="Data" class="num"><?= (int) $model['jumlah_data'] ?></td>
        <td data-label="Node / daun" class="num"><?= (int) $model['jumlah_node'] ?> / <?= (int) $model['jumlah_daun'] ?></td>
        <td data-label="Akurasi latih" class="num"><?= e(pct($model['akurasi_latih'])) ?></td>
        <td data-label="Akurasi uji" class="num"><?= e(pct($model['akurasi_uji'])) ?></td>
        <td data-label="Dibuat" class="nowrap"><?= e(wib($model['created_at'])) ?></td>
        <td class="actions">
          <a class="link-btn" href="<?= e(admin_url('training/detail?id=' . $model['id'])) ?>">Detail</a>
          <?php if ($isAdmin && !$model['aktif']): ?>
            <form class="inline-form" method="post" action="<?= e(admin_url('training/aktifkan')) ?>" data-confirm="Model <?= e($model['nama']) ?> akan dipakai untuk semua prediksi berikutnya." data-confirm-title="Aktifkan model?" data-confirm-ok="Ya, aktifkan">
              <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $model['id'] ?>"><button class="link-btn" type="submit">Aktifkan</button>
            </form>
            <form class="inline-form" method="post" action="<?= e(admin_url('training/hapus')) ?>" data-confirm="Model beserta langkah perhitungan, aturan, hasil pengujian, dan prediksinya akan dihapus permanen." data-confirm-title="Hapus model?" data-confirm-ok="Ya, hapus" data-confirm-danger>
              <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $model['id'] ?>"><button class="link-btn link-btn--danger" type="submit">Hapus</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>
