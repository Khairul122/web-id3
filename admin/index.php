<?php
declare(strict_types=1);

// Alamat lama /admin/... dialihkan ke /panel/... agar tautan tersimpan tetap berfungsi.
require dirname(__DIR__) . '/app/core/bootstrap.php';
$params = $_GET;
unset($params['r']);
redirect('panel/' . ltrim(query('r'), '/') . ($params ? '?' . http_build_query($params) : ''));
