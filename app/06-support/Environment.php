<?php

declare(strict_types=1);

namespace App\Support;

final class Environment
{
    public static function isDevelopment(): bool
    {
        return ($_ENV['APP_ENV'] ?? 'production') === 'development';
    }
}
