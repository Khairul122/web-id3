"""Tes HTTP panel admin: semua halaman, alur form-review-simpan, dialog pesan, CSRF, dan hak akses.
Butuh Apache Laragon di http://localhost/web-id3/. Data uji diberi awalan UJI dan dibersihkan di akhir."""
import http.client
import os
import re
import secrets
import subprocess
from http.cookies import SimpleCookie
from urllib.parse import urlencode, urlsplit

PREFIX = '/web-id3/'
TAG = secrets.token_hex(6)
ADMIN = 'auth_test_' + secrets.token_hex(6)
GURU = 'auth_test_' + secrets.token_hex(6)
SISWA = 'auth_test_' + secrets.token_hex(6)
SISWA_KOSONG = 'auth_test_' + secrets.token_hex(6)
PASSWORD = secrets.token_urlsafe(20)


class Client:
    def __init__(self):
        self.cookies = SimpleCookie()

    def request(self, path, data=None, follow=True):
        """GET/POST; 303 diikuti (maks 5) agar dialog flash di halaman tujuan bisa diperiksa."""
        for _ in range(5):
            connection = http.client.HTTPConnection('127.0.0.1', 80, source_address=('127.0.0.3', 0), timeout=30)
            headers = {'Host': 'localhost'}
            if self.cookies:
                headers['Cookie'] = '; '.join(f'{k}={v.value}' for k, v in self.cookies.items())
            body = urlencode(data, doseq=True) if data is not None else None
            if body is not None:
                headers['Content-Type'] = 'application/x-www-form-urlencoded'
            connection.request('POST' if data is not None else 'GET', path if path.startswith('/') else PREFIX + path, body, headers)
            response = connection.getresponse()
            for key, value in response.getheaders():
                if key.lower() == 'set-cookie':
                    self.cookies.load(value)
            text = response.read().decode('utf-8', 'replace')
            status, location, ctype = response.status, response.getheader('Location'), response.getheader('Content-Type', '')
            connection.close()
            assert not re.search(r'<b>(?:Warning|Fatal error|Notice|Deprecated)</b>', text), text[:400]
            if follow and status == 303 and location:
                path, data = urlsplit(location).path + ('?' + urlsplit(location).query if urlsplit(location).query else ''), None
                continue
            return status, text, ctype, location
        raise AssertionError('Terlalu banyak redirect')


def csrf(text):
    return re.search(r'name="_csrf" value="([a-f0-9]+)"', text).group(1)


def flash(text):
    match = re.search(r'data-flash open[^>]*>.*?<h2[^>]*>([^<]+)</h2>', text, re.S)
    return match.group(1) if match else None


def login(client, username, password=PASSWORD):
    _, page, _, _ = client.request('login.php')
    status, page, _, _ = client.request('login.php', {'_csrf': csrf(page), 'username': username, 'password': password})
    assert status == 200 and flash(page) == 'Berhasil masuk', flash(page)
    return page


def fixture(action, username, role, kode=''):
    env = dict(os.environ, SIMINAT_TEST_PASSWORD=PASSWORD, SIMINAT_TEST_ROLE=role, SIMINAT_TEST_KODE=kode, SIMINAT_TEST_RATE_LIMIT_IP='127.0.0.3')
    subprocess.run(['php', 'tests/auth_fixture.php', action, username], env=env, check=True, capture_output=True)


previous_model = subprocess.run(['php', 'tests/admin_fixture.php', 'state'], check=True, capture_output=True, text=True).stdout.strip() or '0'
fixture('create', ADMIN, 'admin')
fixture('create', GURU, 'guru_bk')
try:
    admin = Client()
    page = login(admin, ADMIN)
    assert 'data-confirm-title="Keluar dari akun?"' in page, 'Logout harus memakai dialog konfirmasi'

    for route in ['admin/', 'admin/siswa/', 'admin/siswa/?label=none&q=a', 'admin/siswa/form', 'admin/siswa/import', 'admin/sekolah/',
                  'admin/sekolah/detail?id=1', 'admin/atribut/', 'admin/atribut/detail?id=2', 'admin/diskretisasi/', 'admin/training/',
                  'admin/training/form', 'admin/pengujian/', 'admin/prediksi/', 'admin/laporan/', 'admin/pengguna/', 'admin/akun/', 'admin/akun/password']:
        status, page, _, _ = admin.request(route)
        assert status == 200 and '<h1>' in page, route

    # Training: form -> review -> simpan
    _, page, _, _ = admin.request('admin/training/form')
    codes = re.findall(r'name="atribut\[\]" value="([A-Za-z_]+)"', page)
    status, page, _, _ = admin.request('admin/training/form', {'_csrf': csrf(page), 'nama': 'UJI-OTOMATIS ' + TAG, 'skenario': 'semua', 'atribut[]': codes, 'min_sampel': '2', 'max_depth': '', 'aktifkan': '1'})
    assert status == 200 and 'Information gain di akar' in page
    status, page, _, _ = admin.request('admin/training/review', {'_csrf': csrf(page)})
    assert flash(page) == 'Training berhasil' and 'Pohon keputusan' in page and 'Aturan IF-THEN' in page
    model_id = re.search(r'training/aktifkan|pengujian/form\?model_id=(\d+)', page).group(1)

    status, svg, ctype, _ = admin.request('admin/training/pohon?id=' + model_id)
    assert status == 200 and 'image/svg+xml' in ctype and svg.startswith('<svg') and 'UJI-OTOMATIS' in svg

    # Validasi form training
    status, page, _, _ = admin.request('admin/training/form', {'_csrf': csrf(page), 'nama': '', 'skenario': 'x', 'min_sampel': '0'})
    assert status == 422 and flash(page) == 'Gagal menyimpan' and 'Pilih minimal satu atribut' in page

    # Pengujian k-fold
    _, page, _, _ = admin.request('admin/pengujian/form?model_id=' + model_id)
    status, page, _, _ = admin.request('admin/pengujian/form', {'_csrf': csrf(page), 'model_id': model_id, 'k': '5'})
    assert flash(page) == 'Pengujian selesai' and 'Confusion matrix' in page
    evaluation_id = re.search(r'jenis=evaluasi&amp;id=(\d+)', page).group(1)

    # Cek Rekomendasi: form -> review -> hasil
    _, page, _, _ = admin.request('admin/prediksi/form')
    form = {'_csrf': csrf(page), 'nama': 'UJI Prediksi Lain ' + TAG, 'sekolah': 'MAN', 'peminatan': 'Umum', 'rata_agama': '93', 'nilai_barab': '91',
            'rata_bahasa': '95.5', 'nilai_mtk': '96', 'rata_mipa': '95', 'rata_ips': '92', 'kat_Kecenderungan': ''}
    status, page, _, _ = admin.request('admin/prediksi/form', form)
    assert 'Tinjau Input' in page and 'Tinggi' in page
    status, page, _, _ = admin.request('admin/prediksi/review', {'_csrf': csrf(page)})
    assert flash(page) == 'Rekomendasi selesai' and 'Jalur keputusan' in page
    other_prediction_id = re.search(r'Hasil #(\d+)', page).group(1)
    status, page, _, _ = admin.request('admin/prediksi/form', {'_csrf': csrf(page), 'sekolah': 'X'})
    assert status == 422 and 'Isi nilai atau pilih kategori' in page

    # Prediksi massal
    _, page, _, _ = admin.request('admin/prediksi/massal')
    status, page, _, _ = admin.request('admin/prediksi/massal', {'_csrf': csrf(page)})
    assert flash(page) == 'Prediksi massal selesai', flash(page)

    # Siswa: tambah -> tinjau -> simpan -> ubah -> hapus
    _, page, _, _ = admin.request('admin/siswa/form')
    siswa = {'_csrf': csrf(page), 'kode_siswa': 'UJI-K9-01', 'nama': 'UJI Siswa ' + TAG, 'sekolah_id': '1', 'kelas': 'XII.9', 'nis': '', 'nisn': '',
             'jk': 'P', 'peminatan': 'Umum', 'label': '', 'sumber_label': '', 'detail_label': '', 'jurusan_diterima': '',
             'rata_agama': '94', 'nilai_barab': '', 'kat_Kat_BArab': 'Sedang', 'rata_bahasa': '96', 'nilai_mtk': '91', 'rata_mipa': '93', 'rata_ips': '95', 'kat_Kecenderungan': ''}
    status, page, _, _ = admin.request('admin/siswa/form', siswa)
    assert 'Tinjau Data Siswa' in page and '(manual)' in page
    status, page, _, _ = admin.request('admin/siswa/review', {'_csrf': csrf(page)})
    assert flash(page) == 'Berhasil disimpan' and 'UJI Siswa' in page
    siswa_id = re.search(r'siswa/form\?id=(\d+)', page).group(1)
    status, page, _, _ = admin.request('admin/siswa/form', dict(siswa, _csrf=csrf(page), kode_siswa='UJI-K9-01'))
    assert status == 422 and 'Kode siswa sudah dipakai' in page, 'Kode duplikat harus ditolak saat tambah'
    _, page, _, _ = admin.request('admin/siswa/form?id=' + siswa_id)
    admin.request('admin/siswa/form?id=' + siswa_id, dict(siswa, _csrf=csrf(page), nama='UJI Ubah ' + TAG))
    _, page, _, _ = admin.request('admin/siswa/review')
    status, page, _, _ = admin.request('admin/siswa/review', {'_csrf': csrf(page)})
    assert flash(page) == 'Berhasil disimpan' and 'UJI Ubah' in page

    # Akun siswa: validasi kode, isolasi riwayat, dan identitas simulasi dari server.
    _, page, _, _ = admin.request('admin/pengguna/form')
    student_account = {'_csrf': csrf(page), 'nama': 'UJI Akun', 'username': 'uji_akun_' + TAG, 'role': 'siswa', 'password': PASSWORD, 'password_confirmation': PASSWORD}
    status, page, _, _ = admin.request('admin/pengguna/form', dict(student_account, kode_siswa=''))
    assert status == 422 and 'Pilih kode siswa' in page
    status, page, _, _ = admin.request('admin/pengguna/form', dict(student_account, _csrf=csrf(page), kode_siswa='UJI-TIDAK-ADA'))
    assert status == 422 and 'Pilih kode siswa' in page

    fixture('create', SISWA, 'siswa', 'UJI-K9-01')
    siswa_client = Client()
    page = login(siswa_client, SISWA)
    assert 'UJI Ubah ' + TAG in page and 'UJI Prediksi Lain ' + TAG not in page
    assert 'Panel Siswa' in page and 'Riwayat Saya' in page and 'Master Data' not in page and 'Proses ID3' not in page
    for route in ['admin/siswa/detail?id=' + siswa_id, 'admin/sekolah/', 'admin/atribut/', 'admin/diskretisasi/',
                  'admin/training/', 'admin/pengujian/', 'admin/laporan/csv?jenis=siswa', 'admin/pengguna/']:
        assert flash(siswa_client.request(route)[1]) == 'Akses ditolak', route
    assert 'UJI Prediksi Lain ' + TAG not in siswa_client.request('admin/prediksi/')[1]
    assert flash(siswa_client.request('admin/prediksi/detail?id=' + other_prediction_id)[1]) == 'Data tidak ditemukan'
    assert flash(siswa_client.request('admin/prediksi/massal')[1]) == 'Akses ditolak'
    assert flash(siswa_client.request('admin/prediksi/hapus', {'id': other_prediction_id})[1]) == 'Akses ditolak'
    _, page, _, _ = siswa_client.request('admin/prediksi/form')
    assert 'name="sekolah"' not in page and 'name="peminatan"' not in page and 'name="nama"' not in page
    forged = dict(form, _csrf=csrf(page), nama='Nama Palsu', sekolah='MAS', peminatan='IPA')
    status, page, _, _ = siswa_client.request('admin/prediksi/form', forged)
    assert status == 200 and 'UJI Ubah ' + TAG in page and 'Nama Palsu' not in page
    status, page, _, _ = siswa_client.request('admin/prediksi/review', {'_csrf': csrf(page)})
    assert flash(page) == 'Rekomendasi selesai' and 'Nama Palsu' not in page and 'UJI Ubah ' + TAG in page
    own_prediction_id = re.search(r'Hasil #(\d+)', page).group(1)
    linked_id = subprocess.run(['php', 'tests/admin_fixture.php', 'prediction-student', own_prediction_id], check=True, capture_output=True, text=True).stdout
    assert linked_id == siswa_id
    assert own_prediction_id in siswa_client.request('admin/prediksi/')[1]
    assert 'Label asli siswa' not in page and 'Diproses oleh' not in page

    # Guru hanya dapat mereset akun siswa dari data siswa yang dituju.
    guru_reset = Client()
    login(guru_reset, GURU)
    _, page, _, _ = guru_reset.request('admin/siswa/detail?id=' + siswa_id)
    assert 'Reset Sandi' in page
    assert flash(guru_reset.request('admin/siswa/reset-sandi', {'id': siswa_id})[1]) == 'Sesi form berakhir'
    status, card, _, _ = guru_reset.request('admin/siswa/reset-sandi', {'_csrf': csrf(page), 'id': siswa_id})
    new_password = re.search(r'data-label="Sandi awal"><strong>([a-f0-9]{12})</strong>', card).group(1)
    assert status == 200 and new_password not in guru_reset.request('admin/siswa/kartu-akun')[1]
    assert flash(siswa_client.request('admin/')[1]) == 'Sesi berakhir'
    _, login_page, _, _ = Client().request('login.php')
    failed = Client()
    _, login_page, _, _ = failed.request('login.php')
    assert failed.request('login.php', {'_csrf': csrf(login_page), 'username': SISWA, 'password': PASSWORD})[0] == 422
    login(Client(), SISWA, new_password)

    fixture('create', SISWA_KOSONG, 'siswa', 'UJI-TIDAK-ADA')
    empty_client = Client()
    page = login(empty_client, SISWA_KOSONG)
    assert 'Data Anda belum tersedia' in page
    assert 'Data Anda belum tersedia' in empty_client.request('admin/prediksi/form')[1]

    # Pembuatan akun massal memakai kelas uji khusus agar tidak menyentuh siswa nyata.
    _, page, _, _ = admin.request('admin/siswa/form')
    admin.request('admin/siswa/form', dict(siswa, _csrf=csrf(page), kode_siswa='UJI-K9-02', nama='UJI Massal ' + TAG, kelas='UJI.XII'))
    _, page, _, _ = admin.request('admin/siswa/review')
    _, page, _, _ = admin.request('admin/siswa/review', {'_csrf': csrf(page)})
    batch_siswa_id = re.search(r'siswa/form\?id=(\d+)', page).group(1)
    _, detail, _, _ = guru_reset.request('admin/siswa/detail?id=' + batch_siswa_id)
    assert flash(guru_reset.request('admin/siswa/reset-sandi', {'_csrf': csrf(detail), 'id': batch_siswa_id})[1]) == 'Gagal mereset sandi'
    own_user_id = subprocess.run(['php', 'tests/admin_fixture.php', 'user-id', SISWA], check=True, capture_output=True, text=True).stdout
    _, edit, _, _ = admin.request('admin/pengguna/form?id=' + own_user_id)
    status, edit, _, _ = admin.request('admin/pengguna/form?id=' + own_user_id, {'_csrf': csrf(edit), 'nama': 'Fixture Pengujian Autentikasi', 'username': SISWA, 'role': 'siswa', 'kode_siswa': 'UJI-K9-02', 'password': ''})
    assert status == 422 and 'tidak dapat ditautkan ke siswa lain' in edit
    _, page, _, _ = admin.request('admin/pengguna/form')
    _, page, _, _ = admin.request('admin/pengguna/form', {'_csrf': csrf(page), 'nama': 'UJI Benturan', 'username': 'uji-k9-02', 'role': 'guru_bk', 'password': PASSWORD, 'password_confirmation': PASSWORD})
    collision_id = re.search(r'pengguna/form\?id=(\d+)', page).group(1)
    _, page, _, _ = admin.request('admin/pengguna/akun-siswa?kelas=UJI.XII')
    assert '1 siswa belum memiliki akun' in page
    assert flash(admin.request('admin/pengguna/akun-siswa', {'_csrf': csrf(page), 'sekolah_id': '0', 'kelas': 'UJI.XII'})[1]) == 'Gagal membuat akun'
    _, page, _, _ = admin.request('admin/pengguna/detail?id=' + collision_id)
    admin.request('admin/pengguna/hapus', {'_csrf': csrf(page), 'id': collision_id})
    _, page, _, _ = admin.request('admin/pengguna/akun-siswa?kelas=UJI.XII')
    status, card, _, _ = admin.request('admin/pengguna/akun-siswa', {'_csrf': csrf(page), 'sekolah_id': '0', 'kelas': 'UJI.XII'})
    batch_password = re.search(r'data-label="Sandi awal"><strong>([a-f0-9]{12})</strong>', card).group(1)
    assert status == 200 and 'uji-k9-02' in card and batch_password not in admin.request('admin/siswa/kartu-akun')[1]
    stored_hash = subprocess.run(['php', 'tests/admin_fixture.php', 'user-hash', 'UJI-K9-02'], check=True, capture_output=True, text=True).stdout
    assert stored_hash.startswith('$2y$') and batch_password not in stored_hash
    login(Client(), 'uji-k9-02', batch_password)

    status, page, _, _ = admin.request('admin/siswa/hapus', {'_csrf': 'salah', 'id': siswa_id})
    assert flash(page) == 'Sesi form berakhir'
    status, page, _, _ = admin.request('admin/siswa/hapus', {'_csrf': csrf(page), 'id': siswa_id})
    assert flash(page) == 'Berhasil dihapus'
    _, page, _, _ = admin.request('admin/siswa/form')
    admin.request('admin/siswa/form', dict(siswa, _csrf=csrf(page), nama='UJI Setelah Impor ' + TAG))
    _, page, _, _ = admin.request('admin/siswa/review')
    admin.request('admin/siswa/review', {'_csrf': csrf(page)})
    linked = Client()
    linked_page = login(linked, SISWA, new_password)
    assert 'UJI Setelah Impor ' + TAG in linked_page
    assert 'Hasil #' + own_prediction_id in linked.request('admin/prediksi/detail?id=' + own_prediction_id)[1]
    assert own_prediction_id in linked.request('admin/prediksi/')[1]
    _, edit, _, _ = admin.request('admin/pengguna/form?id=' + own_user_id)
    assert flash(admin.request('admin/pengguna/form?id=' + own_user_id, {'_csrf': csrf(edit), 'nama': 'Fixture Pengujian Autentikasi', 'username': SISWA, 'role': 'guru_bk', 'kode_siswa': '', 'password': ''})[1]) == 'Berhasil disimpan'
    assert flash(linked.request('admin/')[1]) == 'Sesi berakhir'
    status, page, _, _ = admin.request('admin/siswa/form', {'_csrf': csrf(page)})
    assert status == 422 and flash(page) == 'Gagal menyimpan'

    # Master data lain: simpan ulang nilai yang sama
    _, page, _, _ = admin.request('admin/sekolah/form?id=2')
    name = re.search(r'id="nama" name="nama"[^>]*value="([^"]+)"', page).group(1)
    alamat = re.search(r'<textarea id="alamat"[^>]*>([^<]*)</textarea>', page).group(1)
    status, page, _, _ = admin.request('admin/sekolah/form?id=2', {'_csrf': csrf(page), 'nama': name, 'alamat': alamat})
    assert flash(page) == 'Berhasil disimpan'
    _, page, _, _ = admin.request('admin/diskretisasi/form')
    status, page, _, _ = admin.request('admin/diskretisasi/form', {'_csrf': csrf(page), 'batas_tinggi': '95', 'batas_sedang': '96'})
    assert status == 422 and 'lebih kecil' in page
    status, page, _, _ = admin.request('admin/diskretisasi/form', {'_csrf': csrf(page), 'batas_tinggi': '95', 'batas_sedang': '92'})
    assert flash(page) == 'Berhasil disimpan'

    # Import: baca file server -> tinjau -> batal (data tidak diganti)
    _, page, _, _ = admin.request('admin/siswa/import')
    status, page, _, _ = admin.request('admin/siswa/import', {'_csrf': csrf(page), 'sumber': 'server'})
    assert 'Validasi lulus' in page and '>247<' in page
    status, page, _, _ = admin.request('admin/siswa/import-review', {'_csrf': csrf(page), 'aksi': 'batal'})
    assert flash(page) == 'Import dibatalkan'

    # Laporan
    for route in ['admin/laporan/cetak?jenis=siswa', 'admin/laporan/cetak?jenis=model&id=' + model_id, 'admin/laporan/cetak?jenis=evaluasi&id=' + evaluation_id, 'admin/laporan/cetak?jenis=prediksi']:
        status, page, _, _ = admin.request(route)
        assert status == 200 and 'data-print' in page, route
    status, page, ctype, _ = admin.request('admin/laporan/csv?jenis=siswa')
    assert status == 200 and 'text/csv' in ctype and 'Kode;Sekolah;Kelas' in page

    # Pengguna
    _, page, _, _ = admin.request('admin/pengguna/form')
    status, page, _, _ = admin.request('admin/pengguna/form', {'_csrf': csrf(page), 'nama': 'UJI Guru', 'username': 'uji_otomatis_' + TAG, 'role': 'guru_bk', 'password': PASSWORD, 'password_confirmation': PASSWORD})
    assert flash(page) == 'Berhasil disimpan'
    user_id = re.search(r'pengguna/form\?id=(\d+)', page).group(1)
    status, page, _, _ = admin.request('admin/pengguna/hapus', {'_csrf': csrf(page), 'id': user_id})
    assert flash(page) == 'Berhasil dihapus'

    # Route tidak dikenal dan method khusus POST
    status, page, _, _ = admin.request('admin/tidak-ada/')
    assert flash(page) == 'Halaman tidak ditemukan'
    assert admin.request('admin/training/hapus', follow=False)[0] == 405

    # Guru BK: tidak boleh mengubah data
    guru = Client()
    login(guru, GURU)
    for route in ['admin/training/form', 'admin/pengguna/', 'admin/siswa/form', 'admin/prediksi/massal']:
        status, page, _, _ = guru.request(route)
        assert flash(page) == 'Akses ditolak', route
    status, page, _, _ = guru.request('admin/prediksi/form')
    assert status == 200 and 'Cek Rekomendasi' in page
    assert 'siswa/hapus' not in guru.request('admin/siswa/')[1]

    # Logout dengan dialog berhasil keluar
    status, page, _, _ = admin.request('logout.php', {'_csrf': csrf(page := admin.request('admin/')[1])})
    assert flash(page) == 'Berhasil keluar'
    print('PASS: halaman panel, training, pengujian, prediksi, CRUD master data, import, laporan, pengguna, dialog, CSRF, dan hak akses guru BK.')
finally:
    subprocess.run(['php', 'tests/admin_fixture.php', 'cleanup', previous_model], check=True, capture_output=True)
    fixture('delete', ADMIN, 'admin')
    fixture('delete', GURU, 'guru_bk')
    fixture('delete', SISWA, 'siswa')
    fixture('delete', SISWA_KOSONG, 'siswa')
