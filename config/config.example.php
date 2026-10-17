<?php
declare(strict_types=1);

return [
    'db_host' => getenv('SIMINAT_DB_HOST') ?: '127.0.0.1',
    'db_port' => getenv('SIMINAT_DB_PORT') ?: '3306',
    'db_name' => getenv('SIMINAT_DB_NAME') ?: 'siminat',
    'db_user' => getenv('SIMINAT_DB_USER') ?: 'root',
    'db_password' => getenv('SIMINAT_DB_PASSWORD') ?: '',
];
