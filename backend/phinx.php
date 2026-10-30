<?php
require_once __DIR__ . '/config/config.php';

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds'      => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment'     => 'development',
        'development' => [
            'adapter'   => 'mysql',
            'host'      => Config::DB_HOST(),
            'name'      => Config::DB_NAME(),
            'user'      => Config::DB_USER(),
            'pass'      => Config::DB_PASSWORD(),
            'port'      => Config::DB_PORT(),
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],
    'version_order' => 'creation'
];
