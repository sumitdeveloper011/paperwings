<?php

return [
    'enabled' => env('QUEUE_MONITOR_ENABLED', false),
    
    'dev_email' => env('QUEUE_MONITOR_EMAIL', null),
    
    'environments' => [
        'local',
        'development',
    ],
];
