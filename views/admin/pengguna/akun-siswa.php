<div class="page-head"><div><h1>Buat Akun Siswa</h1><p>Akun dibuat untuk siswa yang belum memilikinya. Sandi awal tampil satu kali setelah dibuat.</p></div></div>
<form class="toolbar" method="get" aria-label="Filter siswa tanpa akun">
  <label for="sekolah_id">Sekolah</label><select id="sekolah_id" name="sekolah_id"><option value="">Semua sekolah</option><?php foreach ($schools as $school): ?><option value="<?= (int) $school['id'] ?>" <?= $schoolId === (int) $school['id'] ? 'selected' : '' ?>><?= e($school['nama']) ?></option><?php endforeach; ?></select>
  <label for="kelas">Kelas</label><select id="kelas" name="kelas"><option value="">Semua kelas</option><?php foreach ($classes as $class): ?><option value="<?= e($class) ?>" <?= $kelas === $class ? 'selected' : '' ?>><?= e($class) ?></option><?php endforeach; ?></select>
  <button class="btn" type="submit">Tampilkan</button>
</form>
<section class="card"><h2><?= count($students) ?> siswa belum memiliki akun</h2>
<?php if ($students): ?>
<form method="post" action="<?= e(admin_url('pengguna/akun-siswa')) ?>" data-confirm="<?= count($students) ?> akun siswa akan dibuat. Simpan atau cetak sandi awal pada halaman berikutnya." data-confirm-title="Buat akun siswa?" data-confirm-ok="Ya, buat akun">
  <?= Csrf::field() ?><input type="hidden" name="sekolah_id" value="<?= (int) ($schoolId ?? 0) ?>"><input type="hidden" name="kelas" value="<?= e($kelas) ?>">
  <button class="btn btn--accent" type="submit">Buat <?= count($students) ?> Akun</button>
</form>
<?php else: ?><p>Semua siswa pada filter ini sudah memiliki akun.</p><?php endif; ?>
</section>
