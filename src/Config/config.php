<?php

return [
    'index' => env('LEOPARD_INDEX', 'default_index'),
    'sync' => [
        'chunkSize' => 1000,
        'models' => [
            env('LEOPARD_INDEX', 'default_index') => []
        ]
    ]
];
