<?php

namespace Application;

return [
    'assetic_configuration' => [

        'default' => array(
            'assets' => array(
                '@head_common_js',
                '@head_common_css',
            ),
        ),

//        'routes' => array(
//            'home' => array(
//                '@common_css',
//                '@common_js',
//            ),
//        ),

//        'controllers' => [
//            'Application\Controller\Index' => [
//                '@common_css',
//                '@common_js',
//            ],
//        ],
        'modules' => [
            __NAMESPACE__ => [
                'root_path' => __DIR__ . '/../assets',
                'collections' => [
                    'head_common_css' => [
                        'assets' => [
                            'css/bootstrap.min.css',
                            'css/animate.css',
                            'css/font-awesome.css',
                            'css/style.css',
                        ],
                    ],
                    'head_common_js' => [
                        'assets' => [
                            'js/jquery-2.1.1.js',
                            'js/jquery-ui-1.10.4.min.js',
                            'js/jquery-ui.custom.min.js',
                            'js/bootstrap.min.js',
                        ],
                    ],
                    'common_images' => array(
                        'assets' => array(
                            'images/*',
                            'images/gallery/*',
                            'images/landing/*',

                        ),
                        'options' => array(
                            'move_raw' => true,
                        )
                    ),
                    'common_fonts' => array(
                        'assets' => array(
                            'fonts/*',
                        ),
                        'options' => array(
                            'move_raw' => true,
                        )
                    ),
                ],
            ],
        ],
    ],
];