<?php

declare(strict_types=1);

namespace App\Support;

final class ParticipantAuth
{
    /** @return array{status: int, headers: array<string, string>, body: string}|null */
    public static function requireParticipant(?string $redirectTo = null): ?array
    {
        if (Session::isParticipantLoggedIn()) {
            return null;
        }

        $target = $redirectTo ?? ($_SERVER['REQUEST_URI'] ?? '/');
        Session::setFlash('info', 'Logg inn eller registrer deg for å delta i konkurransen.');

        return Response::redirect('/logg-inn?redirect=' . rawurlencode($target));
    }
}
