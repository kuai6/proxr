<?php

namespace Server;

use Zend\Log\Logger;

return [
    'log' => [
        'ServerLogger' => [
            'writers' => [
                [
                    'name' => 'stream',
                    'priority' => Logger::DEBUG,
                    'options' => [
                        'stream' => 'php://output',
                    ],
                ],
            ],
        ],
    ],
];