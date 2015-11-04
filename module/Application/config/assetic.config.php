<?php

namespace Application;

return [
    'assetic_configuration' => [

//        'routes' => array(
//            'home' => array(
//                '@common_css',
//                '@common_js',
//            ),
//        ),

        'controllers' => [
            'Application\Controller\Index' => [
                '@common_css',
                '@common_js',
            ],
        ],
        'modules' => [
            __NAMESPACE__ => [
                'root_path' => __DIR__ . '/../assets',
                'collections' => [
                    'common_css' => [
                        'assets' => [
                            'css/bootstrap.min.css',
                            'css/animate.css',
                        ],
                    ],
                    'common_js' => [
                        'assets' => [
                            'js/bootstrap.min.js',
                            'js/jquery-2.1.1.js',
                            'js/jquery-ui-1.10.4.min.js',
                            'js/jquery-ui.custom.min.js',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
