<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Domain\Stop;

interface StopRepository
{
    /** @return Stop[] */
    public function findByRouteId(string $routeId): array;

    public function findById(string $id): ?Stop;

    public function findByQrToken(string $token): ?Stop;

    public function save(Stop $stop): void;

    public function delete(string $id): void;

    public function isQrTokenTaken(string $token, ?string $excludeStopId = null): bool;
}
