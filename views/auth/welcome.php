<div class="account-summary"><span>Peran akun</span><strong><?= $user['role'] === 'admin' ? 'Administrator' : 'Guru BK' ?></strong><span>Username</span><strong><?= e($user['username']) ?></strong></div>
<p class="workspace-note">Akses akun Anda sudah aktif. Pengelolaan data siswa dan pemodelan ID3 akan ditambahkan pada tahap pengembangan berikutnya.</p>
<a class="auth-submit" href="<?= e(url()) ?>">Lihat Beranda <?= icon('arrow') ?></a>
<div class="account-actions"><a href="<?= e(url('admin/?page=password')) ?>">Ganti kata sandi</a><form method="post" action="<?= e(url('logout.php')) ?>"><?= Csrf::field() ?><button type="submit">Keluar dari akun</button></form></div>
