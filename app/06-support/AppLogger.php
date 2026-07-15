<?php

declare(strict_types=1);

namespace App\Support;

final class AppLogger
{
    /** @param array<string, mixed> $context */
    public static function error(string $message, array $context = []): void
    {
        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0775, true);
        }

        $line = date('c') . ' ERROR ' . $message;
        if ($context !== []) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        $line .= PHP_EOL;

        @file_put_contents($logDir . '/app.log', $line, FILE_APPEND | LOCK_EX);
        error_log($message);
    }
}
