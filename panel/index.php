<?php
declare(strict_types=1);

// Front controller panel admin. URL /admin/siswa/detail diteruskan .htaccess sebagai ?r=siswa/detail.
require dirname(__DIR__) . '/app/core/bootstrap.php';
Router::dispatch(query('r', 'dashboard'));
