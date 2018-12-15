<?php

return [
    'index' => env('TRIADEV_ELASTICSEARCH_ODM_INDEX', 'default_index'),
    'sync' => [
        'chunkSize' => 1000,
        'models' => [
            env('TRIADEV_ELASTICSEARCH_ODM_INDEX', 'default_index') => []
        ]
    ]
];
