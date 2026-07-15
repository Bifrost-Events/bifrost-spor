<?php

declare(strict_types=1);

return [
    'name' => 'Bifrost Spor',
    'data_path' => dirname(__DIR__) . '/storage/data',
    'schema_version' => 1,
    'route_statuses' => ['draft', 'published'],
    'stop_statuses' => ['draft', 'published'],
    'themes' => ['nature', 'culture', 'urban', 'default'],
];
