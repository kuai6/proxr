<?php
return [
    // This should be an array of module namespaces used in the application.
    'modules' => [
        'Application',
    ],

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => [
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => [
            __DIR__ . '/../../../../module',
            __DIR__ . '/../../../../vendor',
        ],
    ],
];
