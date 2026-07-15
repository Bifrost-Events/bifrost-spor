<?php

declare(strict_types=1);

namespace App\Support;

use App\Service\AuthService;

final class Auth
{
    public static function check(): bool
    {
        if (AuthService::isDevBypassEnabled()) {
            return true;
        }

        return Session::isAuthenticated();
    }

    /** @return array{status: int, headers: array<string, string>, body: string}|null */
    public static function requireAdmin(): ?array
    {
        if (self::check()) {
            return null;
        }

        return Response::redirect('/admin/login');
    }
}
