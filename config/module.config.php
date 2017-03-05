<?php

return [
    'service_manager' => [
        'factories' => [
            'TransformerManager' => \Abacaphiliac\Zend\Transformer\PluginManager\TransformerPluginManagerFactory::class,
        ],
    ],
    'abacaphiliac/zend-transformer' => [
        'transformers' => [
            
        ],
    ],
];
