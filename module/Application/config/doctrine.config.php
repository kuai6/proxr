<?php

namespace Application;

use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return[
    'doctrine' => [
        'driver' => [
            'AnnotationDriver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Application\\Entity' => 'AnnotationDriver'
                ],
            ],
        ],
    ],
];
