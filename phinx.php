<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'default',
        'default' => [
            'adapter' => 'mysql',
            'host' => $_ENV['SQL_HOST'] ?? 'sql',
            'name' => $_ENV['SQL_NAME'] ?? 'test',
            'user' => $_ENV['SQL_USER'] ?? 'test',
            'pass' => $_ENV['SQL_PASS'] ?? 'sql',
            'port' => $_ENV['SQL_PORT'] ?? 3306
        ],
    ],
    'version_order' => 'creation'
];
