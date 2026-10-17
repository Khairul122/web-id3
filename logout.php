<?php
declare(strict_types=1);
require __DIR__ . '/app/core/bootstrap.php';
(new AuthController())->logout();
