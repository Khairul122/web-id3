<div class="page-head"><div><h1>Kartu Akun Siswa</h1><p>Sandi awal hanya ditampilkan pada halaman ini. Cetak atau simpan sebelum menutupnya.</p></div>
<div class="page-actions"><button class="btn" type="button" data-print><?= icon('printer') ?> Cetak / Simpan PDF</button><a class="btn btn--outline" href="<?= e(admin_url('siswa/')) ?>">Kembali</a></div></div>
<?php if (!$cards): ?><div class="card empty"><strong>Tidak ada kartu akun</strong>Sandi sudah ditampilkan atau belum ada akun yang dibuat.</div><?php else: ?>
<div class="table-wrap"><table class="table table--cards"><thead><tr><th>Nama</th><th>Kelas</th><th>Username</th><th>Sandi awal</th></tr></thead><tbody>
<?php foreach ($cards as $card): ?><tr><td class="cell-title"><?= e($card['nama']) ?></td><td data-label="Kelas"><?= e($card['kelas']) ?></td><td data-label="Username"><?= e($card['username']) ?></td><td data-label="Sandi awal"><strong><?= e($card['password']) ?></strong></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php endif; ?>
