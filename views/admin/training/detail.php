<div class="page-head">
  <div>
    <h1><?= e($model['nama']) ?> <?php if ($model['aktif']): ?><span class="badge badge--aktif">Aktif</span><?php endif; ?></h1>
    <p><?= e(ModelId3::SCENARIOS[$model['skenario']] ?? $model['skenario']) ?> · dibuat <?= e(wib($model['created_at'])) ?> oleh <?= e($model['pembuat'] ?? '-') ?></p>
  </div>
  <div class="page-actions">
    <a class="btn btn--outline" href="<?= e(admin_url('laporan/cetak?jenis=model&id=' . $model['id'])) ?>"><?= icon('printer') ?> Cetak</a>
    <?php if ($isAdmin): ?>
      <a class="btn btn--outline" href="<?= e(admin_url('pengujian/form?model_id=' . $model['id'])) ?>"><?= icon('chart') ?> Uji Model</a>
      <?php if (!$model['aktif']): ?>
        <form method="post" action="<?= e(admin_url('training/aktifkan')) ?>" data-confirm="Model ini akan dipakai untuk semua prediksi berikutnya." data-confirm-title="Aktifkan model?" data-confirm-ok="Ya, aktifkan">
          <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $model['id'] ?>"><button class="btn" type="submit"><?= icon('check') ?> Aktifkan</button>
        </form>
        <form method="post" action="<?= e(admin_url('training/hapus')) ?>" data-confirm="Model beserta langkah perhitungan, aturan, hasil pengujian, dan prediksinya akan dihapus permanen." data-confirm-title="Hapus model?" data-confirm-ok="Ya, hapus" data-confirm-danger>
          <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $model['id'] ?>"><button class="btn btn--danger" type="submit"><?= icon('trash') ?> Hapus</button>
        </form>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<div class="stat-grid">
  <div class="stat"><span class="stat__label">Data latih</span><span class="stat__value"><?= (int) $model['jumlah_data'] ?></span><span class="stat__note"><?= e(distribution_text($model['tree']['distribusi'])) ?></span></div>
  <div class="stat"><span class="stat__label">Node / daun</span><span class="stat__value"><?= (int) $model['jumlah_node'] ?>/<?= (int) $model['jumlah_daun'] ?></span><span class="stat__note">Kedalaman <?= (int) $model['kedalaman'] ?> · <?= count($rules) ?> daun berisi data</span></div>
  <div class="stat"><span class="stat__label">Akurasi latih</span><span class="stat__value"><?= e(pct($model['akurasi_latih'])) ?></span><span class="stat__note">Prediksi data latih sendiri</span></div>
  <div class="stat"><span class="stat__label">Akurasi uji</span><span class="stat__value"><?= $evaluation ? e(pct($evaluation['akurasi'])) : '-' ?></span><span class="stat__note"><?= $evaluation ? e($evaluation['k'] . '-fold cross validation') : 'Belum diuji' ?></span></div>
</div>

<section class="card" aria-labelledby="tree-title">
  <div class="card__head"><h2 id="tree-title">Pohon keputusan</h2><p>Klik node atribut untuk melipat atau membuka cabang. Label pada garis adalah nilai atribut.</p></div>
  <ul class="tree"><?= tree_html($model['tree']) ?></ul>
</section>

<section class="card" aria-labelledby="steps-title">
  <div class="card__head"><h2 id="steps-title">Langkah perhitungan entropy dan gain</h2><p>Satu langkah per node yang dievaluasi. Dapat dicocokkan dengan perhitungan manual di Bab IV.</p></div>
  <?php foreach ($steps as $step): ?>
    <details class="card" <?= $step['urutan'] == 1 ? 'open' : '' ?>>
      <summary><strong>Langkah <?= (int) $step['urutan'] ?>:</strong> <?= e($step['node_path']) ?> · <?= (int) $step['jumlah'] ?> data · entropy <?= e(num($step['entropy_parent'], 4)) ?> · terpilih <strong><?= e($step['atribut_terpilih'] ?? 'tidak ada, jadi daun') ?></strong></summary>
      <p class="table-caption">Distribusi: <?= e(distribution_text($step['distribusi'])) ?></p>
      <div class="table-wrap"><table class="table">
        <thead><tr><th>Atribut</th><th>Nilai: jumlah (entropy)</th><th class="num">Gain</th></tr></thead>
        <tbody>
        <?php foreach ($step['detail_gain'] as $gain): ?>
          <tr class="<?= $gain['atribut'] === $step['atribut_terpilih'] ? 'row-best' : '' ?>">
            <td class="cell-strong"><?= e($gain['atribut']) ?></td>
            <td><?= e(implode('; ', array_map(static fn (array $v): string => $v['nilai'] . ': ' . $v['jumlah'] . ' (' . num($v['entropy'], 4) . ')', array_filter($gain['nilai'], static fn (array $v): bool => $v['jumlah'] > 0)))) ?></td>
            <td class="num"><?= e(num($gain['gain'], 4)) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table></div>
    </details>
  <?php endforeach; ?>
  <?php if (!$steps): ?><p class="table-caption">Semua data latih berada di satu kelas sehingga pohon hanya berisi satu daun.</p><?php endif; ?>
</section>

<section class="card" aria-labelledby="rules-title">
  <div class="card__head"><h2 id="rules-title">Aturan IF-THEN (<?= count($rules) ?>)</h2><p>Dibentuk dari setiap jalur akar ke daun. Support = jumlah data latih di daun.</p></div>
  <div class="table-wrap"><table class="table table--cards">
    <thead><tr><th class="num">No</th><th>Aturan</th><th>Kelas</th><th class="num">Support</th><th class="num">Keyakinan</th></tr></thead>
    <tbody>
    <?php foreach ($rules as $rule): ?>
      <tr>
        <td data-label="No" class="num"><?= (int) $rule['no_aturan'] ?></td>
        <td class="cell-title"><?= e(ModelId3::ruleText($rule['kondisi'], $rule['kelas'])) ?></td>
        <td data-label="Kelas"><?= badge($rule['kelas']) ?></td>
        <td data-label="Support" class="num"><?= (int) $rule['support'] ?></td>
        <td data-label="Keyakinan" class="num"><?= e(pct($rule['keyakinan'])) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table></div>
</section>
