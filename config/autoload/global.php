<?php

return [
    'log' => [
        'logger' => [
            'errorHandler' => true,
            'exceptionHandler' => true,
            'writers' => [
                'standard-output' => [
                    'name' => 'stream',
                    'priority' => \Zend\Log\Logger::DEBUG,
                    'options' => [
                        'stream' => 'php://stderr',
                    ],
                ],
            ],
        ],
    ],
];
