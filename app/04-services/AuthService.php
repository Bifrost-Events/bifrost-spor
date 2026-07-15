<?php

declare(strict_types=1);

namespace App\Service;

use App\Support\Environment;
use App\Support\Session;

final class AuthService
{
    public static function isDevBypassEnabled(): bool
    {
        return Environment::isDevelopment()
            && (($_ENV['ADMIN_AUTH_BYPASS'] ?? 'false') === 'true');
    }

    /**
     * @return array{ok: bool, error?: string}
     */
    public function login(string $username, string $password): array
    {
        if (self::isDevBypassEnabled()) {
            Session::setAuth(true);

            return ['ok' => true];
        }

        $expectedUser = trim((string) ($_ENV['ADMIN_USERNAME'] ?? ''));
        $expectedHash = trim((string) ($_ENV['ADMIN_PASSWORD_HASH'] ?? ''));

        if ($expectedUser === '' || $expectedHash === '') {
            return ['ok' => false, 'error' => 'Admininnlogging er ikke konfigurert.'];
        }

        if (!hash_equals($expectedUser, $username) || !password_verify($password, $expectedHash)) {
            return ['ok' => false, 'error' => 'Ugyldig brukernavn eller passord.'];
        }

        Session::setAuth(true);

        return ['ok' => true];
    }

    public function logout(): void
    {
        Session::clearAuth();
    }
}
