<?php
declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        if ($this->isSiswa) {
            $model = $this->ownSiswa ? ModelId3::active() : null;
            $this->render('dashboard/siswa', [
                'siswa' => $this->ownSiswa,
                'attributes' => $this->ownSiswa ? Atribut::all() : [],
                'model' => $model,
                'live' => $model ? Id3::predict($model['tree'], $this->ownSiswa['atribut']) : null,
                'history' => $this->ownSiswa ? Prediksi::forSiswa((int) $this->ownSiswa['id']) : [],
                'pageTitle' => 'Dashboard', 'active' => 'dashboard', 'crumbs' => ['Dashboard' => null],
            ]);
            return;
        }
        if (!$this->isAdmin) {
            $this->render('dashboard/guru', DashboardService::guidance((int) $this->user['sekolah_id']) + [
                'school' => Sekolah::find((int) $this->user['sekolah_id']),
                'pageTitle' => 'Dashboard', 'active' => 'dashboard', 'crumbs' => ['Dashboard' => null],
            ]);
            return;
        }
        $this->render('dashboard/index', DashboardService::summary() + [
            'pageTitle' => 'Dashboard',
            'active' => 'dashboard',
            'crumbs' => ['Dashboard' => null],
        ]);
    }
}
