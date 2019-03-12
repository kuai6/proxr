<?php

return [
    'queue' => [
        'connection' => [
            'hostname'  => getenv('RABBITMQ_HOST') ?: 'localhost',
            'port'      => getenv('RABBITMQ_PORT') ?: 5672,
            'username'  => getenv('RABBITMQ_USER') ?: 'quest',
            'password'  => getenv('RABBITMQ_PASSWORD') ?: 'quest',
            'vhost'     => getenv('RABBITMQ_VHOST') ?: '/'
        ],

        /**
         * Описание очередей, обменников, роутов
         */
        'exchanges' => [
            'app.daemon.exchange' => [
                'type' => AMQP_EX_TYPE_TOPIC,
                'options' => [],
            ],
        ],
        'queues' => [
            'app.daemon.main.queue' => [
                'options' => [],
                'bindTo' => 'app.daemon.exchange',
                'routingKey' => 'app.daemon.*.event'
            ],
        ],
    ],
];
