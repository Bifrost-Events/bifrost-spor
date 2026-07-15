<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\Route;

final class RouteOwnership
{
    /** @return array{status: int, headers: array<string, string>, body: string}|null */
    public static function requireParticipant(?string $redirectTo = null): ?array
    {
        $target = $redirectTo ?? ($_SERVER['REQUEST_URI'] ?? '/admin/routes');

        return ParticipantAuth::requireParticipant($target);
    }

    public static function owns(Route $route): bool
    {
        $participant = Session::getParticipant();
        if ($participant === null || $route->ownerId === '') {
            return false;
        }

        return hash_equals($route->ownerId, $participant['id']);
    }

    /** @return array{status: int, headers: array<string, string>, body: string}|null */
    public static function requireOwner(Route $route, string $redirectTo = '/admin/routes'): ?array
    {
        if ($redirect = self::requireParticipant($redirectTo)) {
            return $redirect;
        }

        if (!self::owns($route)) {
            Session::setFlash('error', 'Du har ikke tilgang til denne løypa.');

            return Response::redirect($redirectTo);
        }

        return null;
    }
}
