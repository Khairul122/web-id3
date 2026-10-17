<?php
declare(strict_types=1);

final class DashboardController extends Controller
{
    public function index(): void
    {
        $this->render('dashboard/index', DashboardService::summary() + [
            'pageTitle' => 'Dashboard',
            'active' => 'dashboard',
            'crumbs' => ['Dashboard' => null],
        ]);
    }
}
