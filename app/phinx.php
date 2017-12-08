<?php

// load our environment files - used to store credentials & configuration
(new Dotenv\Dotenv(__DIR__))->load();

return
    [
        'paths' => [
            'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
            'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
        ],
        'environments' =>
            [
                'default_database' => 'connection',
                'default_migration_table' => 'phinxlog',
                'connection'      =>
                    [
                        'adapter' => 'mysql',
                        'host' => (string) getenv('DB_HOST'),
                        'name' => (string) getenv('MYSQL_DATABASE'),
                        'user' => (string) getenv('MYSQL_USER'),
                        'pass' => (string) getenv('MYSQL_PASSWORD'),
                        'port' => 3306,
                        'charset' => 'utf8',
                        'collation' => 'utf8_unicode_ci',
                    ],
            ],
    ];
