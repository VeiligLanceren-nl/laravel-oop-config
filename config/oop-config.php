<?php

return [
    'namespace' => 'App\\Config',
    'path' => app_path('Config'),

    'autoload' => true,
    'stubs' => [
        'class' => base_path('stubs/oop-config-full.stub'),
        'method' => base_path('stubs/oop-config-method.stub'),
    ],
];