<div class="page-head">
  <div><h1><?= e($account['nama']) ?></h1><p><?= e(PenggunaService::ROLES[$account['role']]) ?></p></div>
  <div class="page-actions">
    <a class="btn" href="<?= e(admin_url('pengguna/form?id=' . $account['id'])) ?>"><?= icon('edit') ?> Ubah</a>
    <?php if ((int) $account['id'] !== (int) $user['id']): ?>
    <form method="post" action="<?= e(admin_url('pengguna/hapus')) ?>" data-confirm="Akun <?= e($account['username']) ?> akan dihapus dan tidak dapat masuk lagi." data-confirm-title="Hapus pengguna?" data-confirm-ok="Ya, hapus" data-confirm-danger>
      <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $account['id'] ?>"><button class="btn btn--danger" type="submit"><?= icon('trash') ?> Hapus</button>
    </form>
    <?php endif; ?>
  </div>
</div>
<section class="card">
  <dl class="dl">
    <dt>Username</dt><dd><?= e($account['username']) ?></dd>
    <dt>Peran</dt><dd><?= e(PenggunaService::ROLES[$account['role']]) ?></dd>
    <?php if ($account['kode_siswa']): ?><dt>Kode siswa</dt><dd><?= e($account['kode_siswa']) ?></dd><?php endif; ?>
    <dt>Login terakhir</dt><dd><?= e(wib($account['last_login'])) ?></dd>
    <dt>Dibuat</dt><dd><?= e(wib($account['created_at'])) ?></dd>
    <dt>Hak akses</dt><dd><?= e(['admin' => 'Semua menu, termasuk master data, training, dan pengguna.', 'guru_bk' => 'Melihat data dan hasil, menjalankan Cek Rekomendasi, mencetak laporan, dan mereset sandi siswa.', 'siswa' => 'Melihat profil dan rekomendasi sendiri, menjalankan simulasi, serta melihat riwayat sendiri.'][$account['role']]) ?></dd>
  </dl>
</section>
