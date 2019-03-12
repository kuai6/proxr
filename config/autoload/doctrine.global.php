<?php

//настройки doctrine
return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => \Doctrine\DBAL\Driver\PDOPgSql\Driver::class,
                'params' => [
                    'driver'    => 'pdo_pgsql',
                    'host'      => getenv('POSTGRESQL_HOST') ?: 'localhost',
                    'user'      => getenv('POSTGRESQL_USER') ?: 'developer',
                    'password'  => getenv('POSTGRESQL_PASSWORD') ?: 'developer',
                    'dbname'    => getenv('POSTGRESQL_DBNAME') ?: 'proxr',
                    'charset'   => 'utf8',
                    'port'      => getenv('POSTGRESQL_PORT') ?: 5432,
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                //кэширование
                'metadata_cache'    => 'array',
                'query_cache'       => 'array',
                'result_cache'      => 'array',
            ],
        ],

        /**
         * Настройка миграций доктрины
         */
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => __DIR__ .'/../../migrations',
                'namespace' => 'DoctrineMigrations',
                'table' => 'doctrine_migration_versions',
            ],
        ],
    ],
];
