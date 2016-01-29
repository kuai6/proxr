<?php

return [
    'queue' => [
        'connection' => [
            'hostname'  => 'localhost',
            'port'      => 5672,
            'username'  => 'developer',
            'password'  => 'developer',
            'vhost'     => 'proxr'
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
