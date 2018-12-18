<?php

return [
    'index' => env('LEOPARD_INDEX', 'default_index'),
    'sync' => [
        'chunkSize' => env('LEOPARD_SYNC_CHUNK_SIZE', 1000),
        'models' => [
            env('LEOPARD_INDEX', 'default_index') => []
        ]
    ]
];
