<?php

return [
    'log' => [
        'logger' => [
            'errorHandler' => true,
            'exceptionhandler' => true,
            'writers' => [
                'standard-output' => [
                    'name' => 'stream',
                    'priority' => \Zend\Log\Logger::INFO,
                    'options' => [
                        'stream' => 'php://stdout',
                    ]
                ]
            ],
        ],
    ],
];
