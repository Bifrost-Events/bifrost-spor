<?php

declare(strict_types=1);

namespace App\Support;

final class Csrf
{
    public static function field(): string
    {
        $token = Session::csrfToken();

        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validateRequest(): void
    {
        $token = $_POST['_csrf'] ?? null;
        if (!Session::validateCsrf(is_string($token) ? $token : null)) {
            throw new ValidationException('Ugyldig sikkerhetstoken. Last siden på nytt og prøv igjen.');
        }
    }
}
