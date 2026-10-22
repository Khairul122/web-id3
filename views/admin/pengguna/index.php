<div class="page-head">
  <div><h1>Pengguna</h1><p>Akun administrator, guru BK, dan siswa.</p></div>
  <div class="page-actions"><a class="btn btn--outline" href="<?= e(admin_url('pengguna/akun-siswa')) ?>">Buat Akun Siswa</a><a class="btn" href="<?= e(admin_url('pengguna/form')) ?>"><?= icon('plus') ?> Tambah Pengguna</a></div>
</div>
<form class="toolbar" method="get"><label for="role_filter">Peran</label><select id="role_filter" name="role"><option value="">Semua peran</option><?php foreach (PenggunaService::ROLES as $value => $label): ?><option value="<?= e($value) ?>" <?= $role === $value ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select><button class="btn" type="submit">Tampilkan</button></form>
<div class="table-wrap">
  <table class="table table--cards">
    <thead><tr><th>Nama</th><th>Username</th><th>Peran</th><th>Kode siswa</th><th>Login terakhir</th><th class="actions">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($users as $account): ?>
      <tr>
        <td class="cell-title"><a href="<?= e(admin_url('pengguna/detail?id=' . $account['id'])) ?>"><?= e($account['nama']) ?></a><?= (int) $account['id'] === (int) $user['id'] ? ' <small>(Anda)</small>' : '' ?></td>
        <td data-label="Username"><?= e($account['username']) ?></td>
        <td data-label="Peran"><span class="badge badge--<?= $account['role'] === 'admin' ? 'admin' : 'none' ?>"><?= e(PenggunaService::ROLES[$account['role']]) ?></span></td>
        <td data-label="Kode siswa"><?= e($account['kode_siswa'] ?? '-') ?></td>
        <td data-label="Login terakhir" class="nowrap"><?= e(wib($account['last_login'])) ?></td>
        <td class="actions">
          <a class="link-btn" href="<?= e(admin_url('pengguna/detail?id=' . $account['id'])) ?>">Detail</a>
          <a class="link-btn" href="<?= e(admin_url('pengguna/form?id=' . $account['id'])) ?>">Ubah</a>
          <?php if ((int) $account['id'] !== (int) $user['id']): ?>
          <form class="inline-form" method="post" action="<?= e(admin_url('pengguna/hapus')) ?>" data-confirm="Akun <?= e($account['username']) ?> akan dihapus dan tidak dapat masuk lagi." data-confirm-title="Hapus pengguna?" data-confirm-ok="Ya, hapus" data-confirm-danger>
            <?= Csrf::field() ?><input type="hidden" name="id" value="<?= (int) $account['id'] ?>"><button class="link-btn link-btn--danger" type="submit">Hapus</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
