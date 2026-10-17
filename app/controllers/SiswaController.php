<?php
declare(strict_types=1);

final class SiswaController extends Controller
{
    private const MAX_UPLOAD = 2 * 1024 * 1024;

    public function index(): void
    {
        $filter = ['q' => query('q'), 'sekolah_id' => query_int('sekolah_id'), 'kelas' => query('kelas'), 'label' => query('label')];
        $page = max(1, query_int('page'));
        $this->render('siswa/index', [
            'filter' => $filter,
            'page' => $page,
            'result' => Siswa::paginate($filter, $page),
            'perPage' => Siswa::PER_PAGE,
            'schools' => Sekolah::all(),
            'classes' => Siswa::kelasList(),
            'labelOptions' => ['' => 'Semua label', 'labeled' => 'Berlabel', 'Saintek' => 'Saintek', 'Soshum' => 'Soshum', 'Campuran' => 'Campuran', 'none' => 'Tanpa label'],
            'pageTitle' => 'Data Siswa', 'active' => 'siswa', 'crumbs' => ['Master Data' => null, 'Data Siswa' => null],
        ]);
    }

    public function detail(): void
    {
        $siswa = Siswa::find(query_int('id')) ?? $this->notFound('siswa/', 'Siswa yang dipilih tidak tersedia.');
        $model = ModelId3::active();
        $this->render('siswa/detail', [
            'siswa' => $siswa,
            'attributes' => Atribut::all(),
            'model' => $model,
            'live' => $model ? Id3::predict($model['tree'], $siswa['atribut']) : null,
            'history' => Prediksi::forSiswa((int) $siswa['id']),
            'pageTitle' => $siswa['nama'], 'active' => 'siswa', 'crumbs' => ['Data Siswa' => 'siswa/', $siswa['kode_siswa'] => null],
        ]);
    }

    /** Langkah 1: isi form. Data valid disimpan sebagai draf di sesi lalu diarahkan ke tinjauan. */
    public function form(): void
    {
        $this->requireAdmin();
        $id = query_int('id') ?: null;
        $record = $id ? (Siswa::find($id) ?? $this->notFound('siswa/', 'Siswa yang dipilih tidak tersedia.')) : null;
        $draft = $_SESSION['siswa_draft'] ?? null;
        $form = query('draft') === '1' && $draft && $draft['id'] === $id ? $draft : ($record ? SiswaService::fromRecord($record) : SiswaService::EMPTY);
        $errors = [];
        $flash = null;
        if (is_post()) {
            $this->verifyPost('siswa/form' . ($id ? '?id=' . $id : ''));
            [$form, $errors] = SiswaService::fromPost($id, $record['nilai']['rata_umum'] ?? null);
            if (!$errors) {
                $_SESSION['siswa_draft'] = $form;
                back_to('siswa/review');
            }
            $flash = $this->formError('Ada ' . count($errors) . ' isian yang perlu diperbaiki.');
        }
        $pageTitle = $id ? 'Ubah Siswa' : 'Tambah Siswa';
        $this->render('siswa/form', compact('id', 'form', 'errors', 'flash', 'pageTitle') + [
            'schools' => Sekolah::all(),
            'domains' => Atribut::domains(),
            'rule' => Discretizer::fromDb(),
            'active' => 'siswa', 'crumbs' => ['Data Siswa' => 'siswa/', $pageTitle => null],
        ]);
    }

    /** Langkah 2: tinjau kategori atribut lalu simpan. */
    public function review(): void
    {
        $this->requireAdmin();
        $draft = $_SESSION['siswa_draft'] ?? null;
        if (!$draft) {
            flash('error', 'Tidak ada data untuk ditinjau', 'Isi form siswa terlebih dahulu.');
            back_to('siswa/form');
        }
        $id = $draft['id'];
        $formUrl = 'siswa/form?' . http_build_query(array_filter(['id' => $id, 'draft' => 1]));
        if (is_post()) {
            $this->verifyPost('siswa/review');
            // Validasi ulang: kode bisa sudah dipakai siswa lain sejak form diisi.
            if (Siswa::kodeExists($draft['kode_siswa'], $id) || ($id && !Siswa::find($id))) {
                flash('error', 'Gagal menyimpan', 'Kode siswa sudah dipakai atau data asal sudah dihapus. Periksa kembali form.');
                back_to($formUrl);
            }
            $savedId = SiswaService::save($draft);
            unset($_SESSION['siswa_draft']);
            flash('success', 'Berhasil disimpan', 'Data ' . $draft['nama'] . ($id ? ' telah diperbarui.' : ' telah ditambahkan.'));
            back_to('siswa/detail?id=' . $savedId);
        }
        $record = SiswaService::toRecord($draft, Discretizer::fromDb());
        $this->render('siswa/review', compact('id', 'formUrl', 'record') + [
            'school' => Sekolah::find($record['sekolah_id']),
            'attributes' => Atribut::all(),
            'pageTitle' => 'Tinjau Data Siswa', 'active' => 'siswa', 'crumbs' => ['Data Siswa' => 'siswa/', 'Tinjau' => null],
        ]);
    }

    public function hapus(): void
    {
        $this->requireAdmin();
        $this->verifyPost('siswa/');
        $siswa = Siswa::find((int) $this->field('id'));
        if (!$siswa) {
            flash('error', 'Gagal menghapus', 'Data siswa tidak ditemukan.');
            back_to('siswa/');
        }
        Siswa::delete((int) $siswa['id']);
        flash('success', 'Berhasil dihapus', 'Data ' . $siswa['nama'] . ' telah dihapus.');
        back_to('siswa/');
    }

    /** Import langkah 1: baca dan validasi workbook, hasilnya disimpan sementara di storage/uploads. */
    public function import(): void
    {
        $this->requireAdmin();
        $serverFile = APP_ROOT . '/data/Dataset_Bersih_ID3.xlsx';
        $flash = null;
        if (is_post()) {
            $this->verifyPost('siswa/import', self::MAX_UPLOAD + 65536);
            $temporary = null;
            try {
                if ($this->field('sumber') === 'server' && is_file($serverFile)) {
                    [$path, $name] = [$serverFile, basename($serverFile)];
                } else {
                    [$path, $name] = [$temporary = $this->receiveUpload(), (string) $_FILES['berkas']['name']];
                }
                $parsed = Importer::parse(Importer::readXlsx($path), Atribut::domains());
                $token = bin2hex(random_bytes(16));
                file_put_contents(APP_ROOT . '/storage/uploads/import-' . $token . '.json', json_out($parsed + ['file' => $name]));
                $_SESSION['import_token'] = $token;
                back_to('siswa/import-review');
            } catch (RuntimeException $exception) {
                http_response_code(422);
                $flash = ['type' => 'error', 'title' => 'Gagal membaca file', 'message' => $exception->getMessage()];
            } finally {
                if ($temporary && is_file($temporary)) {
                    unlink($temporary);
                }
            }
        }
        $this->render('siswa/import', compact('serverFile', 'flash') + [
            'pageTitle' => 'Import Data Siswa', 'active' => 'siswa', 'crumbs' => ['Data Siswa' => 'siswa/', 'Import' => null],
        ]);
    }

    private function receiveUpload(): string
    {
        $upload = $_FILES['berkas'] ?? null;
        if (!is_array($upload) || !is_string($upload['name'] ?? null) || ($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Pilih file Excel (.xlsx) yang akan diimport.');
        }
        if ($upload['size'] > self::MAX_UPLOAD) {
            throw new RuntimeException('Ukuran file maksimal 2 MB.');
        }
        if (strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION)) !== 'xlsx') {
            throw new RuntimeException('Format file harus .xlsx.');
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']);
        if (!in_array($mime, ['application/zip', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/octet-stream'], true)) {
            throw new RuntimeException('Isi file bukan workbook Excel.');
        }
        $target = APP_ROOT . '/storage/uploads/' . bin2hex(random_bytes(16)) . '.xlsx';
        if (!move_uploaded_file($upload['tmp_name'], $target)) {
            throw new RuntimeException('File gagal diunggah. Coba lagi.');
        }
        return $target;
    }

    /** Import langkah 2: tinjau ringkasan dan simpan (mengganti seluruh data siswa). */
    public function importReview(): void
    {
        $this->requireAdmin();
        $token = $_SESSION['import_token'] ?? '';
        $file = APP_ROOT . '/storage/uploads/import-' . $token . '.json';
        if (!preg_match('/^[a-f0-9]{32}$/D', $token) || !is_file($file)) {
            flash('error', 'Tidak ada data import', 'Pilih file Excel terlebih dahulu.');
            back_to('siswa/import');
        }
        $parsed = json_decode((string) file_get_contents($file), true);
        if (is_post()) {
            $this->verifyPost('siswa/import-review');
            if ($this->field('aksi') === 'batal') {
                unlink($file);
                unset($_SESSION['import_token']);
                flash('success', 'Import dibatalkan', 'Tidak ada data yang diubah.');
                back_to('siswa/');
            }
            if ($parsed['errors']) {
                flash('error', 'Gagal mengimport', 'Perbaiki kesalahan pada file lalu ulangi import.');
                back_to('siswa/import-review');
            }
            $saved = Importer::store($parsed['rows'], Discretizer::fromDb());
            unlink($file);
            unset($_SESSION['import_token']);
            flash('success', 'Import berhasil', $saved . ' data siswa tersimpan: ' . $parsed['summary']['berlabel'] . ' berlabel dan ' . $parsed['summary']['tanpa_label'] . ' tanpa label.');
            back_to('siswa/');
        }
        $this->render('siswa/import-review', [
            'parsed' => $parsed,
            'summary' => $parsed['summary'],
            'preview' => array_slice($parsed['rows'], 0, 10),
            'pageTitle' => 'Tinjau Import', 'active' => 'siswa', 'crumbs' => ['Data Siswa' => 'siswa/', 'Import' => 'siswa/import', 'Tinjau' => null],
        ]);
    }
}
