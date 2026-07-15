<?php

declare(strict_types=1);

namespace App\Support;

final class AppUrl
{
    public static function base(): string
    {
        $fromEnv = rtrim((string) ($_ENV['APP_URL'] ?? ''), '/');
        if ($fromEnv !== '') {
            return $fromEnv;
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php');
        $scriptDir = str_replace('\\', '/', dirname($scriptName));
        if ($scriptDir === '.' || $scriptDir === '/') {
            $scriptDir = '';
        }

        return rtrim($scheme . '://' . $host . $scriptDir, '/');
    }

    public static function to(string $path): string
    {
        return self::base() . '/' . ltrim($path, '/');
    }

    public static function qrScanUrl(string $token): string
    {
        return self::to('/q/' . rawurlencode($token));
    }
}
